<?php
require_once 'models/UsuarioModel.php';
require_once 'models/LogModel.php';
require_once 'helpers/EmailHelper.php';

class AuthController {
    private $usuarioModel;
    private $logModel;
    private $emailHelper;

    public function __construct($pdo) {
        $this->usuarioModel = new UsuarioModel($pdo);
        $this->logModel = new LogModel($pdo);
        $this->emailHelper = new EmailHelper($pdo);
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

            $this->logModel->registrar($user['id'], 'LOGIN', "Login realizado com sucesso ({$user['email']}).");

            unset($user['senha']);

            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Login realizado com sucesso!',
                'usuario' => $user
            ]);
        } else {
            $this->logModel->registrar($user['id'] ?? null, 'LOGIN_FALHA', "Tentativa de login falhou para o e-mail: {$email}.");
            http_response_code(401);
            echo json_encode(['sucesso' => false, 'mensagem' => 'E-mail ou senha incorretos!']);
        }
        exit;
    }

    public function logout() {
        @ob_clean();
        header('Content-Type: application/json');
        if (isset($_SESSION['usuario_id'])) {
            $this->logModel->registrar($_SESSION['usuario_id'], 'LOGOUT', "Usuário encerrou a sessão.");
        }
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
            $senhaProvisoria = 'Brecho@' . rand(1000, 9999);
            $senhaHash = password_hash($senhaProvisoria, PASSWORD_DEFAULT);
            $this->usuarioModel->atualizarSenha($usuario['id'], $senhaHash);

            $html = "
                <p>Olá, <strong>{$usuario['nome']}</strong>!</p>
                <p>Recebemos uma solicitação de recuperação de senha para a sua conta no <strong>Intent Brechó</strong>.</p>
                <p>Sua nova senha provisória de acesso é:</p>
                <div style='background:#ecfdf5; border:1px solid #a7f3d0; padding:12px 16px; border-radius:8px; font-size:18px; font-weight:bold; color:#065f46; text-align:center; margin:16px 0;'>
                    {$senhaProvisoria}
                </div>
                <p>Recomendamos que você acesse a aba <strong>Meu Perfil &rarr; Segurança</strong> logo após entrar para definir uma senha pessoal.</p>
            ";

            $envio = $this->emailHelper->enviar($email, $usuario['nome'], 'Recuperação de Senha - Intent Brechó', $html);

            $this->logModel->registrar($usuario['id'], 'RECUPERAR_SENHA', "Senha provisória gerada para {$email}.");

            if ($envio['sucesso']) {
                echo json_encode([
                    'sucesso' => true,
                    'mensagem' => "Uma senha provisória foi enviada para {$email}. Verifique sua caixa de entrada (e spam)."
                ]);
            } else {
                echo json_encode([
                    'sucesso' => true,
                    'mensagem' => "Senha redefinida no sistema, porém houve falha no envio SMTP: {$envio['erro']}"
                ]);
            }
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