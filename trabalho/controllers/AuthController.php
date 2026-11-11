<?php
require_once 'models/UsuarioModel.php';

class AuthController {
    private $usuarioModel;
    private $pdo; 

    public function __construct($pdo) {
        $this->pdo = $pdo; 
        $this->usuarioModel = new UsuarioModel($pdo);
    }

    public function login() {
        $erro = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $senha = $_POST['senha'];

            $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($senha, $user['senha'])) {
                $_SESSION['usuario_id']   = $user['id'];
                $_SESSION['usuario_nome'] = $user['nome'];
                $_SESSION['usuario_tipo'] = $user['tipo'];
                $_SESSION['usuario_foto'] = $user['foto'];

                header("Location: index.php?acao=vitrine");
                exit;
            } else {
                $erro = "E-mail ou senha incorretos!";
            }
        }

        require_once 'views/public/login.php';
    }

    public function logout() {
        $_SESSION = array();
        session_destroy();
        header("Location: index.php?acao=vitrine");
        exit;
    }

    public function recuperar_senha() {
        $msg = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
            
            if ($email) {
                $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
                $stmt->execute([$email]);
                
                if ($stmt->fetch()) {
                    $msg = "Um e-mail com as instruções de redefinição de senha foi enviado para $email.";
                } else {
                    $msg = "E-mail não encontrado em nossa base.";
                }
            }
        }
        
        require_once 'views/public/recuperar_senha.php';
    }
}
?>