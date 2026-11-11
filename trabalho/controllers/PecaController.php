<?php
require_once 'models/PecaModel.php';

class PecaController {
    private $pecaModel;

    public function __construct($pdo) {
        $this->pecaModel = new PecaModel($pdo);
    }

    public function vitrine() {
        $pecas = $this->pecaModel->listarTodasPecasPublicas();
        require_once 'views/public/vitrine.php';
    }

    public function detalhar() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header("Location: index.php?acao=vitrine");
            exit;
        }

        $peca = $this->pecaModel->buscarPecaPorId($id);

        if (!$peca) {
            echo "<script>alert('Peça não encontrada!'); window.location.href='index.php?acao=vitrine';</script>";
            exit;
        }

        require_once 'views/public/detalhar.php';
    }
}
?>