<?php
require_once 'models/PecaModel.php';

class PecaController {
    private $pecaModel;

    public function __construct($pdo) {
        $this->pecaModel = new PecaModel($pdo);
    }

    public function vitrine() {
        @ob_clean();
        header('Content-Type: application/json');
        echo json_encode($this->pecaModel->listarTodasPecasPublicas());
        exit;
    }

    public function detalhar() {
        @ob_clean();
        header('Content-Type: application/json');

        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID da peça não informado.']);
            exit;
        }

        $peca = $this->pecaModel->buscarPecaPorId($id);
        if (!$peca) {
            http_response_code(404);
            echo json_encode(['erro' => 'Peça não encontrada.']);
            exit;
        }

        echo json_encode($peca);
        exit;
    }

    public function categorias() {
        @ob_clean();
        header('Content-Type: application/json');
        echo json_encode($this->pecaModel->listarCategorias());
        exit;
    }
}
?>