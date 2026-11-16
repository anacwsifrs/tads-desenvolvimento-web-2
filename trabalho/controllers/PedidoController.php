<?php
require_once 'models/PedidoModel.php';
require_once 'models/PecaModel.php';
require_once 'models/UsuarioModel.php';

class PedidoController {
    private $pedidoModel;
    private $pecaModel;
    private $usuarioModel;

    public function __construct($pdo) {
        $this->pedidoModel = new PedidoModel($pdo);
        $this->pecaModel = new PecaModel($pdo);
        $this->usuarioModel = new UsuarioModel($pdo);
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
            if ($valor_final_a_pagar == 0) {
                $this->pedidoModel->atualizarStatus($pedido_id, 'Aprovado');
            }
            $_SESSION['carrinho'] = [];

            $mensagem = ($valor_final_a_pagar == 0)
                ? 'Pedido gerado e pago integralmente com seu saldo! Peças reservadas.'
                : 'Pedido gerado! Valor pendente: R$ ' . number_format($valor_final_a_pagar, 2, ',', '.') . '. Efetue o PIX e anexe o comprovante.';

            echo json_encode([
                'sucesso' => true,
                'mensagem' => $mensagem,
                'pedido_id' => $pedido_id
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
        echo json_encode(['sucesso' => true, 'mensagem' => 'Pedido aprovado com sucesso!']);
        exit;
    }

    public function cancelar_pedido() {
        $this->verificarAdmin();
        @ob_clean();
        header('Content-Type: application/json');
        $pedido_id = $_GET['id'] ?? 0;
        $this->pedidoModel->cancelarPedidoERestaurarPecas($pedido_id);
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