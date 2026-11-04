<?php
require_once 'conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['usuario_id']   = $user['id'];
        $_SESSION['usuario_nome'] = $user['nome'];
        $_SESSION['usuario_tipo'] = $user['tipo'];
        $_SESSION['usuario_foto'] = $user['foto'];

        header("Location: index.php");
        exit;
    } else {
        $erro = "E-mail ou senha incorretos!";
    }
}

require_once 'header.php';
?>

<h2>Login do Usuário</h2>
<?php if ($erro): ?>
    <p style="color:red;"><?= $erro ?></p>
<?php endif; ?>

<form action="login.php" method="POST" style="max-width:400px; margin-top:15px;">
    <div style="margin-bottom:10px;">
        <label>E-mail:</label><br>
        <input type="email" name="email" required style="width:100%; padding:8px;">
    </div>
    <div style="margin-bottom:10px;">
        <label>Senha:</label><br>
        <input type="password" name="senha" required style="width:100%; padding:8px;">
    </div>
    <button type="submit" style="padding:10px 20px; background:#007bff; color:white; border:none; cursor:pointer;">Entrar</button>
</form>
<p style="margin-top:15px;">
    Não tem conta? <a href="cadastro.php">Cadastre-se aqui</a> | <a href="recuperar_senha.php">Esqueceu a senha?</a>
</p>

<?php require_once 'footer.php'; ?>