<?php
require_once 'conexao.php';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cpf = trim($_POST['cpf']);
    $nome = trim($_POST['nome']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'];
    $telefone = trim($_POST['telefone']);
    $pix = trim($_POST['pix']);

    if (!$email || empty($cpf) || empty($nome) || empty($senha)) {
        $mensagem = "Por favor, preencha todos os campos obrigatórios corretamente.";
    } else {
        $nomeFoto = 'default.png';
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $nomeFoto = "perfil_" . time() . "_" . uniqid() . "." . $ext;
            if (!is_dir('uploads')) { mkdir('uploads', 0777, true); }
            move_uploaded_file($_FILES['foto']['tmp_name'], 'uploads/' . $nomeFoto);
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (cpf, nome, email, senha, telefone, chave_pix, foto, tipo) VALUES (?, ?, ?, ?, ?, ?, ?, 'cliente')");
            $stmt->execute([$cpf, $nome, $email, $senhaHash, $telefone, $pix, $nomeFoto]);
            
            echo "<script>alert('Cadastro realizado com sucesso! Faça seu login.'); window.location.href='index.php?acao=login';</script>";
            exit;
        } catch (PDOException $e) {
            $mensagem = "Erro: CPF ou E-mail já cadastrados no sistema.";
        }
    }
}

require_once 'header.php';
?>

<h2>Cadastro de Cliente</h2>
<?php if ($mensagem): ?>
    <p style="color:red; font-weight:bold;"><?= $mensagem ?></p>
<?php endif; ?>

<form action="index.php?acao=cadastro" method="POST" enctype="multipart/form-data" style="margin-top:15px;">
    <div style="margin-bottom:10px;">
        <label>CPF (obrigatório):</label><br>
        <input type="text" name="cpf" required placeholder="000.000.000-00" style="width:100%; padding:8px;">
    </div>
    <div style="margin-bottom:10px;">
        <label>Nome Completo:</label><br>
        <input type="text" name="nome" required style="width:100%; padding:8px;">
    </div>
    <div style="margin-bottom:10px;">
        <label>E-mail:</label><br>
        <input type="email" name="email" required style="width:100%; padding:8px;">
    </div>
    <div style="margin-bottom:10px;">
        <label>Senha:</label><br>
        <input type="password" name="senha" required style="width:100%; padding:8px;">
    </div>
    <div style="margin-bottom:10px;">
        <label>WhatsApp:</label><br>
        <input type="text" name="telefone" required style="width:100%; padding:8px;">
    </div>
    <div style="margin-bottom:10px;">
        <label>Chave PIX:</label><br>
        <input type="text" name="pix" style="width:100%; padding:8px;">
    </div>
    <div style="margin-bottom:15px;">
        <label>Foto de Perfil:</label><br>
        <input type="file" name="foto" accept="image/*">
    </div>
    <button type="submit" style="padding:10px 20px; background:#28a745; color:white; border:none; cursor:pointer;">Cadastrar</button>
</form>

<?php require_once 'footer.php'; ?>