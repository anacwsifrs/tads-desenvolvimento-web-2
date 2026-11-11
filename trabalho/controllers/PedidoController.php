<?php
require_once 'models/PedidoModel.php';
require_once 'models/PecaModel.php';

class PedidoController {
    private $pedidoModel;
    private $pecaModel;

    public function __construct($pdo) {
        $this->pedidoModel = new PedidoModel($pdo);
        $this->pecaModel = new PecaModel($pdo);
    }

    public function carrinho() {
        $this->verificarLogin();
        $itensCarrinho = $_SESSION['carrinho'] ?? [];
        require_once 'views/cliente/carrinho.php';
    }

    public function adicionar_carrinho() {
        $this->verificarLogin();

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
            }
        }
        header("Location: index.php?acao=carrinho");
        exit;
    }

    public function remover_carrinho() {
        $this->verificarLogin();
        $id = $_GET['id'] ?? null;
        if ($id && isset($_SESSION['carrinho'][$id])) {
            unset($_SESSION['carrinho'][$id]);
        }
        header("Location: index.php?acao=carrinho");
        exit;
    }

    public function finalizar_compra() {
        $this->verificarLogin();

        if (empty($_SESSION['carrinho'])) {
            header("Location: index.php?acao=vitrine");
            exit;
        }

        $cliente_id = $_SESSION['usuario_id'];
        $itens = $_SESSION['carrinho'];
        $valor_total = array_sum(array_column($itens, 'preco_venda'));

        $pedido_id = $this->pedidoModel->criarPedido($cliente_id, $valor_total, $itens);

        if ($pedido_id) {
            $_SESSION['carrinho'] = [];
            echo "<script>alert('Pedido gerado com sucesso! Efetue o PIX e anexe o comprovante.'); window.location.href='index.php?acao=meus_pedidos';</script>";
        } else {
            echo "<script>alert('Erro ao processar compra.'); window.location.href='index.php?acao=carrinho';</script>";
        }
        exit;
    }

    public function meus_pedidos() {
        $this->verificarLogin();
        $cliente_id = $_SESSION['usuario_id'];
        $pedidos = $this->pedidoModel->listarPedidosCliente($cliente_id);
        require_once 'views/cliente/meus_pedidos.php';
    }

    public function upload_comprovante_pix() {
        $this->verificarLogin();

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
                echo "<script>alert('Comprovante enviado!'); window.location.href='index.php?acao=meus_pedidos';</script>";
            } else {
                echo "<script>alert('Falha ao enviar comprovante.'); window.history.back();</script>";
            }
        }
    }

    public function gerenciar_pedidos() {
        $this->verificarAdmin();
        $pedidos = $this->pedidoModel->listarTodosPedidos();
        require_once 'views/admin/gerenciar_pedidos.php';
    }

    public function aprovar_pedido() {
        $this->verificarAdmin();
        $pedido_id = $_GET['id'] ?? 0;
        $this->pedidoModel->atualizarStatus($pedido_id, 'Aprovado');
        echo "<script>alert('Pedido aprovado!'); window.location.href='index.php?acao=gerenciar_pedidos';</script>";
        exit;
    }

    public function cancelar_pedido() {
        $this->verificarAdmin();
        $pedido_id = $_GET['id'] ?? 0;
        $this->pedidoModel->cancelarPedidoERestaurarPecas($pedido_id);
        echo "<script>alert('Pedido cancelado e peças devolvidas à vitrine!'); window.location.href='index.php?acao=gerenciar_pedidos';</script>";
        exit;
    }

    private function verificarLogin() {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?acao=login");
            exit;
        }
    }

    private function verificarAdmin() {
        $this->verificarLogin();
        if ($_SESSION['usuario_tipo'] !== 'admin') {
            header("Location: index.php?acao=meus_lotes");
            exit;
        }
    }
}
?>