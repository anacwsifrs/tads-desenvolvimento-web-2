<?php
require_once 'models/UsuarioModel.php';

class UsuarioController {
    private $usuarioModel;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->usuarioModel = new UsuarioModel($pdo);
    }

    public function perfil() {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?acao=login");
            exit;
        }

        $usuario = $this->usuarioModel->buscarClientePorId($_SESSION['usuario_id']);
        require_once 'views/cliente/perfil.php';
    }

    public function api_buscar_cliente() {
        header('Content-Type: application/json');
        $cpf = $_GET['cpf'] ?? '';
        $cliente = $this->usuarioModel->buscarClientePorCpf($cpf);
        echo json_encode($cliente ?: []);
        exit;
    }

    public function cadastro() {
        require_once 'views/public/cadastro.php';
    }

    public function salvar_cadastro() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cpf = trim($_POST['cpf'] ?? '');
            $nome = trim($_POST['nome'] ?? '');
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
            $senha = $_POST['senha'] ?? '';
            $telefone = trim($_POST['telefone'] ?? '');
            $pix = trim($_POST['pix'] ?? '');

            if (!$email || empty($cpf) || empty($nome) || empty($senha)) {
                echo "<script>alert('Preencha todos os campos corretamente!'); window.history.back();</script>";
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
                $stmt = $this->pdo->prepare("INSERT INTO usuarios (cpf, nome, email, senha, telefone, chave_pix, foto, tipo) VALUES (?, ?, ?, ?, ?, ?, ?, 'cliente')");
                $stmt->execute([$cpf, $nome, $email, $senhaHash, $telefone, $pix, $caminhoFotoBD]);
                
                echo "<script>alert('Cadastro realizado com sucesso! Faça seu login.'); window.location.href='index.php?acao=login';</script>";
                exit;
            } catch (PDOException $e) {
                echo "<script>alert('Erro: CPF ou E-mail já cadastrados no sistema.'); window.history.back();</script>";
                exit;
            }
        }
    }

    public function api_atualizar_perfil() {
        @ob_clean();
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão expirada.']);
            exit;
        }

        $id = $_SESSION['usuario_id'];
        $nome = $_POST['nome'] ?? '';
        $telefone = $_POST['telefone'] ?? '';
        $pix = $_POST['pix'] ?? '';

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $nomeFoto = "perfil_" . time() . "_" . uniqid() . "." . $ext;
            
            $diretorio = 'uploads/perfil/';
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0777, true);
            }

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $diretorio . $nomeFoto)) {
                $caminhoFotoBD = 'perfil/' . $nomeFoto;
                $this->atualizarFotoPerfil($id, $caminhoFotoBD);
                $_SESSION['usuario_foto'] = $caminhoFotoBD;
            }
        }

        if ($this->usuarioModel->atualizarUsuarioBasico($id, $nome, $telefone, $pix)) {
            $_SESSION['usuario_nome'] = $nome;
            echo json_encode(['sucesso' => true, 'mensagem' => 'Perfil atualizado com sucesso!']);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar dados no banco.']);
        }
        exit;
    }

    public function api_atualizar_senha() {
        @ob_clean();
        header('Content-Type: application/json');
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão expirada.']);
            exit;
        }

        $id = $_SESSION['usuario_id'];
        $senha_atual = $_POST['senha_atual'] ?? '';
        $nova_senha = $_POST['nova_senha'] ?? '';

        $usuario = $this->usuarioModel->buscarClientePorId($id);
        if ($usuario && password_verify($senha_atual, $usuario['senha'])) {
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
            $stmt->execute([$senha_hash, $id]);
            echo json_encode(['sucesso' => true, 'mensagem' => 'Senha alterada com sucesso!']);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Senha atual incorreta.']);
        }
        exit;
    }

    public function clientes() {
        $this->verificarAdmin();
        $clientes = $this->usuarioModel->listarClientes();
        require_once 'views/admin/clientes.php';
    }

    private function verificarAdmin() {
        if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
            header("Location: index.php?acao=meus_lotes");
            exit;
        }
    }

    private function atualizarFotoPerfil($id, $caminhoFoto) {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET foto = ? WHERE id = ?");
        $stmt->execute([$caminhoFoto, $id]);
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
}
?>