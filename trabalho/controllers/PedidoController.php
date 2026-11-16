<?php
require_once 'models/PedidoModel.php';
require_once 'models/PecaModel.php';
require_once 'models/UsuarioModel.php';
require_once 'models/LogModel.php';
require_once 'helpers/EmailHelper.php';

class PedidoController {
    private $pedidoModel;
    private $pecaModel;
    private $usuarioModel;
    private $logModel;
    private $emailHelper;

    public function __construct($pdo) {
        $this->pedidoModel = new PedidoModel($pdo);
        $this->pecaModel = new PecaModel($pdo);
        $this->usuarioModel = new UsuarioModel($pdo);
        $this->logModel = new LogModel($pdo);
        $this->emailHelper = new EmailHelper($pdo);
    }

    public function carrinho() {
        $this->verificarLogin();
        @ob_clean();
        header('Content-Type: application/json');
        $itensCarrinho = array_values($_SESSION['carrinho'] ?? []);
        $cliente = $this->usuarioModel->buscarClientePorId($_SESSION['usuario_id']);
        echo json_encode([
            'itens' => $itensCarrinho,
            'saldo_credito' => floatval($cliente['saldo_credito'] ?? 0)
        ]);
        exit;
    }

    public function adicionar_carrinho() {
        $this->verificarLogin();
        @ob_clean();
        header('Content-Type: application/json');

        $id = $_GET['id'] ?? null;
        if ($id) {
            $peca = $this->pecaModel->buscarPecaPorId($id);
            if ($peca && isset($peca['disponivel']) && $peca['disponivel'] == 1) {
                if (!isset($_SESSION['carrinho'])) $_SESSION['carrinho'] = [];

                $_SESSION['carrinho'][$id] = [
                    'id' => $peca['id'],
                    'descricao' => $peca['descricao'],
                    'foto_caminho' => $peca['foto_caminho'],
                    'preco_venda' => $peca['valor_proposto'] * 1.4
                ];
                echo json_encode([
                    'sucesso' => true,
                    'mensagem' => 'Peça adicionada ao carrinho!',
                    'total_itens' => count($_SESSION['carrinho'])
                ]);
                exit;
            }
        }

        http_response_code(400);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Peça indisponível ou inválida.']);
        exit;
    }

    public function remover_carrinho() {
        $this->verificarLogin();
        @ob_clean();
        header('Content-Type: application/json');

        $id = $_GET['id'] ?? null;
        if ($id && isset($_SESSION['carrinho'][$id])) {
            unset($_SESSION['carrinho'][$id]);
        }
        echo json_encode([
            'sucesso' => true,
            'itens' => array_values($_SESSION['carrinho'] ?? [])
        ]);
        exit;
    }

    public function finalizar_compra() {
        $this->verificarLogin();
        @ob_clean();
        header('Content-Type: application/json');

        if (empty($_SESSION['carrinho'])) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Seu carrinho está vazio.']);
            exit;
        }

        $dados = json_decode(file_get_contents('php://input'), true);
        $usar_saldo = !empty($dados['usar_saldo']) || isset($_POST['usar_saldo']);

        $cliente_id = $_SESSION['usuario_id'];
        $itens = $_SESSION['carrinho'];
        $valor_total_pedido = array_sum(array_column($itens, 'preco_venda'));
        $valor_final_a_pagar = $valor_total_pedido;
        $novo_saldo = null;

        if ($usar_saldo) {
            $cliente = $this->usuarioModel->buscarClientePorId($cliente_id);
            $saldo_disponivel = floatval($cliente['saldo_credito']);

            if ($saldo_disponivel > 0) {
                if ($saldo_disponivel >= $valor_total_pedido) {
                    $saldo_descontado = $valor_total_pedido;
                    $valor_final_a_pagar = 0;
                } else {
                    $saldo_descontado = $saldo_disponivel;
                    $valor_final_a_pagar = $valor_total_pedido - $saldo_disponivel;
                }

                $novo_saldo = $saldo_disponivel - $saldo_descontado;
                $this->usuarioModel->atualizarUsuarioAdmin(
                    $cliente_id,
                    $cliente['nome'],
                    $cliente['telefone'],
                    $cliente['chave_pix'],
                    $novo_saldo
                );
            }
        }

        $pedido_id = $this->pedidoModel->criarPedido($cliente_id, $valor_final_a_pagar, $itens);

        if ($pedido_id) {
            $_SESSION['carrinho'] = [];

            $qtdItens = count($itens);
            $valorFormatado = number_format((float)$valor_final_a_pagar, 2, ',', '.');
            $forma = ($valor_final_a_pagar == 0) ? 'Saldo integral' : "PIX pendente (R$ {$valorFormatado})";
            $this->logModel->registrar(
                $cliente_id,
                'NOVA_COMPRA',
                "Pedido #PED-{$pedido_id} criado com {$qtdItens} peça(s) — {$forma}."
            );

            $mensagem = ($valor_final_a_pagar == 0)
                ? 'Pedido gerado e abatido do seu saldo! Aguarde a aprovação do administrador para retirada.'
                : 'Pedido gerado! Valor pendente: R$ ' . $valorFormatado . '. Efetue o PIX e anexe o comprovante.';

            echo json_encode([
                'sucesso' => true,
                'mensagem' => $mensagem,
                'pedido_id' => $pedido_id,
                'novo_saldo' => $novo_saldo
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao processar compra.']);
        }
        exit;
    }

    public function meus_pedidos() {
        $this->verificarLogin();
        @ob_clean();
        header('Content-Type: application/json');
        echo json_encode($this->pedidoModel->listarPedidosCliente($_SESSION['usuario_id']));
        exit;
    }

    public function detalhar_pedido() {
        $this->verificarLogin();
        @ob_clean();
        header('Content-Type: application/json');

        $pedido_id = $_GET['id'] ?? 0;
        $pedido = $this->pedidoModel->buscarPedidoComItens($pedido_id);

        if (!$pedido) {
            http_response_code(404);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Pedido não encontrado.']);
            exit;
        }

        if ($_SESSION['usuario_tipo'] !== 'admin' && $_SESSION['usuario_id'] != $pedido['cliente_id']) {
            http_response_code(403);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso não autorizado a este pedido.']);
            exit;
        }

        echo json_encode($pedido);
        exit;
    }

    public function upload_comprovante_pix() {
        $this->verificarLogin();
        @ob_clean();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['comprovante'])) {
            $pedido_id = $_POST['pedido_id'] ?? 0;

            $diretorio = 'uploads/comprovantes_pix/';
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0777, true);
            }

            $extensao = pathinfo($_FILES['comprovante']['name'], PATHINFO_EXTENSION);
            $nome_arquivo = "pix_pedido_{$pedido_id}_" . time() . "." . $extensao;

            if (move_uploaded_file($_FILES['comprovante']['tmp_name'], $diretorio . $nome_arquivo)) {
                $caminho_bd = 'comprovantes_pix/' . $nome_arquivo;
                $this->pedidoModel->salvarComprovantePix($pedido_id, $caminho_bd);

                $this->logModel->registrar(
                    $_SESSION['usuario_id'],
                    'COMPROVANTE_PIX_PEDIDO',
                    "Cliente anexou comprovante PIX ao pedido #PED-{$pedido_id}."
                );

                echo json_encode([
                    'sucesso' => true,
                    'mensagem' => 'Comprovante enviado com sucesso!',
                    'comprovante' => $caminho_bd
                ]);
                exit;
            }
        }

        http_response_code(400);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Falha ao enviar comprovante.']);
        exit;
    }

    public function gerenciar_pedidos() {
        $this->verificarAdmin();
        @ob_clean();
        header('Content-Type: application/json');
        echo json_encode($this->pedidoModel->listarTodosPedidos());
        exit;
    }

    public function aprovar_pedido() {
        $this->verificarAdmin();
        @ob_clean();
        header('Content-Type: application/json');
        $pedido_id = $_GET['id'] ?? 0;
        $this->pedidoModel->atualizarStatus($pedido_id, 'Aprovado');
        $this->logModel->registrar($_SESSION['usuario_id'], 'APROVAR_PEDIDO', "Admin aprovou o pedido #PED-{$pedido_id}.");

        $pedido = $this->pedidoModel->buscarPedidoComItens($pedido_id);
        if ($pedido && !empty($pedido['cliente_email'])) {
            $valorFormatado = number_format((float)$pedido['valor_total'], 2, ',', '.');
            $html = "
                <p>Olá, <strong>{$pedido['cliente_nome']}</strong>!</p>
                <p>Seu pedido <strong>#PED-{$pedido_id}</strong> (Valor final: <strong>R$ {$valorFormatado}</strong>) foi <strong>APROVADO</strong> pela nossa equipe!</p>
                <div style='background:#ecfdf5; border:1px solid #a7f3d0; padding:12px 16px; border-radius:8px; margin:16px 0; color:#065f46;'>
                    Suas peças já estão separadas e prontas para retirada presencial no balcão do <strong>Intent Brechó</strong>.
                </div>
                <p>Você pode conferir os detalhes das peças reservadas a qualquer momento na aba <strong>Minhas Compras</strong>.</p>
            ";
            $this->emailHelper->enviar(
                $pedido['cliente_email'],
                $pedido['cliente_nome'],
                "Pedido #PED-{$pedido_id} Aprovado para Retirada - Intent Brechó",
                $html
            );
        }

        echo json_encode(['sucesso' => true, 'mensagem' => 'Pedido aprovado com sucesso!']);
        exit;
    }

    public function cancelar_pedido() {
        $this->verificarAdmin();
        @ob_clean();
        header('Content-Type: application/json');
        $pedido_id = $_GET['id'] ?? 0;
        $pedido = $this->pedidoModel->buscarPedidoComItens($pedido_id);

        $this->pedidoModel->cancelarPedidoERestaurarPecas($pedido_id);
        $this->logModel->registrar($_SESSION['usuario_id'], 'CANCELAR_PEDIDO', "Admin cancelou o pedido #PED-{$pedido_id} e devolveu as peças à vitrine.");

        if ($pedido && !empty($pedido['cliente_email'])) {
            $html = "
                <p>Olá, <strong>{$pedido['cliente_nome']}</strong>.</p>
                <p>Informamos que o seu pedido <strong>#PED-{$pedido_id}</strong> foi <strong>cancelado</strong> pela administração e as peças voltaram a ficar disponíveis na vitrine.</p>
                <p>Caso tenha dúvidas ou precise de auxílio com uma nova reserva, entre em contato conosco pela página <strong>Fale Conosco</strong>.</p>
            ";
            $this->emailHelper->enviar(
                $pedido['cliente_email'],
                $pedido['cliente_nome'],
                "Pedido #PED-{$pedido_id} Cancelado - Intent Brechó",
                $html
            );
        }

        echo json_encode(['sucesso' => true, 'mensagem' => 'Pedido cancelado e peças devolvidas à vitrine!']);
        exit;
    }

    private function verificarLogin() {
        if (!isset($_SESSION['usuario_id'])) {
            @ob_clean();
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão expirada.']);
            exit;
        }
    }

    private function verificarAdmin() {
        $this->verificarLogin();
        if ($_SESSION['usuario_tipo'] !== 'admin') {
            @ob_clean();
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso restrito a administradores.']);
            exit;
        }
    }
}
?>