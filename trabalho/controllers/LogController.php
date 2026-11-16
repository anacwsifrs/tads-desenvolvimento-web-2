<?php
require_once 'models/LogModel.php';

class LogController {
    private $logModel;

    public function __construct($pdo) {
        $this->logModel = new LogModel($pdo);
    }

    public function listar_logs() {
        $this->verificarAdmin();
        @ob_clean();
        header('Content-Type: application/json');
        echo json_encode($this->logModel->listarTodos(300));
        exit;
    }

    private function verificarAdmin() {
        if (!isset($_SESSION['usuario_id'])) {
            @ob_clean();
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão expirada.']);
            exit;
        }
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