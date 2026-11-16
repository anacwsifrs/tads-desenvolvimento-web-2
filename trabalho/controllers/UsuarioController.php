<?php
require_once 'models/UsuarioModel.php';

class UsuarioController {
    private $usuarioModel;

    public function __construct($pdo) {
        $this->usuarioModel = new UsuarioModel($pdo);
    }

    public function buscar_cliente() {
        @ob_clean();
        header('Content-Type: application/json');
        $cpf = $_GET['cpf'] ?? '';
        $cliente = $this->usuarioModel->buscarClientePorCpf($cpf);
        echo json_encode($cliente ?: []);
        exit;
    }

    public function salvar_cadastro() {
        @ob_clean();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido.']);
            exit;
        }

        $cpf = trim($_POST['cpf'] ?? '');
        $nome = trim($_POST['nome'] ?? '');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $senha = $_POST['senha'] ?? '';
        $telefone = trim($_POST['telefone'] ?? '');
        $pix = trim($_POST['pix'] ?? '');

        if (!$email || empty($cpf) || empty($nome) || empty($senha) || empty($telefone)) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Preencha todos os campos obrigatórios corretamente!']);
            exit;
        }

        $caminhoFotoBD = 'default.png';
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $nomeFoto = "perfil_" . time() . "_" . uniqid() . "." . $ext;
            
            $diretorio = 'uploads/perfil/';
            if (!is_dir($diretorio)) { 
                mkdir($diretorio, 0777, true); 
            }

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $diretorio . $nomeFoto)) {
                $caminhoFotoBD = 'perfil/' . $nomeFoto;
            }
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        try {
            $this->usuarioModel->criarUsuario($cpf, $nome, $email, $senhaHash, $telefone, $pix, 'cliente', $caminhoFotoBD);
            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Cadastro realizado com sucesso! Faça seu login.'
            ]);
        } catch (PDOException $e) {
            http_response_code(409);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Erro: CPF ou E-mail já cadastrados no sistema.'
            ]);
        }
        exit;
    }

    public function perfil() {
        @ob_clean();
        header('Content-Type: application/json');

        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Não autenticado.']);
            exit;
        }

        $usuario = $this->usuarioModel->buscarClientePorId($_SESSION['usuario_id']);
        if ($usuario) {
            unset($usuario['senha']);
        }
        echo json_encode($usuario ?: []);
        exit;
    }

    public function atualizar_perfil() {
        @ob_clean();
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão expirada.']);
            exit;
        }

        $id = $_SESSION['usuario_id'];
        $nome = $_POST['nome'] ?? '';
        $telefone = $_POST['telefone'] ?? '';
        $pix = $_POST['pix'] ?? '';
        $novaFoto = null;

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $nomeFoto = "perfil_" . time() . "_" . uniqid() . "." . $ext;
            
            $diretorio = 'uploads/perfil/';
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0777, true);
            }

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $diretorio . $nomeFoto)) {
                $novaFoto = 'perfil/' . $nomeFoto;
                $this->usuarioModel->atualizarFoto($id, $novaFoto);
                $_SESSION['usuario_foto'] = $novaFoto;
            }
        }

        if ($this->usuarioModel->atualizarUsuarioBasico($id, $nome, $telefone, $pix)) {
            $_SESSION['usuario_nome'] = $nome;
            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Perfil atualizado com sucesso!',
                'foto' => $novaFoto
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar dados no banco.']);
        }
        exit;
    }

    public function atualizar_senha() {
        @ob_clean();
        header('Content-Type: application/json');

        if (!isset($_SESSION['usuario_id'])) {
            http_response_code(401);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão expirada.']);
            exit;
        }

        $id = $_SESSION['usuario_id'];
        $dados = json_decode(file_get_contents('php://input'), true);
        $senha_atual = $dados['senha_atual'] ?? $_POST['senha_atual'] ?? '';
        $nova_senha = $dados['nova_senha'] ?? $_POST['nova_senha'] ?? '';

        $usuario = $this->usuarioModel->buscarClientePorId($id);
        if ($usuario && password_verify($senha_atual, $usuario['senha'])) {
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $this->usuarioModel->atualizarSenha($id, $senha_hash);
            echo json_encode(['sucesso' => true, 'mensagem' => 'Senha alterada com sucesso!']);
        } else {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Senha atual incorreta.']);
        }
        exit;
    }

    // --- Métodos Admin (serão convertidos para JSON quando migrarmos as telas admin) ---
    public function clientes() {
        $this->verificarAdmin();
        $clientes = $this->usuarioModel->listarClientes();
        require_once 'views/admin/clientes.php';
    }

    public function ver_cliente() {
        $this->verificarAdmin();
        $id = $_GET['id'] ?? 0;
        $cliente = $this->usuarioModel->buscarClientePorId($id);
        $readonly = true;
        require_once 'views/admin/form_cliente.php';
    }

    public function editar_cliente() {
        $this->verificarAdmin();
        $id = $_GET['id'] ?? 0;
        $cliente = $this->usuarioModel->buscarClientePorId($id);
        $readonly = false;
        require_once 'views/admin/form_cliente.php';
    }

    public function salvar_cliente_admin() {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $nome = $_POST['nome'] ?? '';
            $telefone = $_POST['telefone'] ?? '';
            $pix = $_POST['pix'] ?? '';
            $saldo = $_POST['saldo_credito'] ?? 0;

            if ($this->usuarioModel->atualizarUsuarioAdmin($id, $nome, $telefone, $pix, $saldo)) {
                echo "<script>alert('Cliente atualizado com sucesso!'); window.location.href='index.php?acao=clientes';</script>";
            } else {
                echo "<script>alert('Erro ao atualizar cliente.'); window.history.back();</script>";
            }
        }
    }

    private function verificarAdmin() {
        if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
            header("Location: index.php?acao=meus_lotes");
            exit;
        }
    }
}
?>