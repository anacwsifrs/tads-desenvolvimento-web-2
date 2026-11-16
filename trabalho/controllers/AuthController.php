<?php
require_once 'models/UsuarioModel.php';

class AuthController {
    private $usuarioModel;

    public function __construct($pdo) {
        $this->usuarioModel = new UsuarioModel($pdo);
    }

    public function login() {
        @ob_clean();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido.']);
            exit;
        }

        $dados = json_decode(file_get_contents('php://input'), true);
        $email = trim($dados['email'] ?? $_POST['email'] ?? '');
        $senha = $dados['senha'] ?? $_POST['senha'] ?? '';

        if (empty($email) || empty($senha)) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Preencha e-mail e senha.']);
            exit;
        }

        $user = $this->usuarioModel->buscarClientePorEmail($email);

        if ($user && password_verify($senha, $user['senha'])) {
            $_SESSION['usuario_id']   = $user['id'];
            $_SESSION['usuario_nome'] = $user['nome'];
            $_SESSION['usuario_tipo'] = $user['tipo'];
            $_SESSION['usuario_foto'] = $user['foto'];

            unset($user['senha']);

            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Login realizado com sucesso!',
                'usuario' => $user
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['sucesso' => false, 'mensagem' => 'E-mail ou senha incorretos!']);
        }
        exit;
    }

    public function logout() {
        @ob_clean();
        header('Content-Type: application/json');
        $_SESSION = array();
        session_destroy();
        echo json_encode(['sucesso' => true]);
        exit;
    }

    public function recuperar_senha() {
        @ob_clean();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido.']);
            exit;
        }

        $dados = json_decode(file_get_contents('php://input'), true);
        $email = filter_var(trim($dados['email'] ?? $_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);

        if (!$email) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Informe um e-mail válido.']);
            exit;
        }

        $usuario = $this->usuarioModel->buscarClientePorEmail($email);

        if ($usuario) {
            echo json_encode([
                'sucesso' => true,
                'mensagem' => "Um e-mail com as instruções de redefinição de senha foi enviado para $email."
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'E-mail não encontrado em nossa base.'
            ]);
        }
        exit;
    }
}
?>