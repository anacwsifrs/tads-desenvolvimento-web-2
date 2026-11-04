<?php
require_once 'conexao.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    if ($email) {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $msg = "Um e-mail com as instruções de redefinição de senha foi enviado para $email.";
        } else {
            $msg = "E-mail não encontrado em nossa base.";
        }
    }
}

require_once 'header.php';
?>

<h2>Recuperação de Senha</h2>
<?php if ($msg): ?>
    <p style="color:blue; font-weight:bold; margin-bottom:10px;"><?= $msg ?></p>
<?php endif; ?>

<form action="index.php?acao=recuperar_senha" method="POST" style="max-width:400px;">
    <div style="margin-bottom:10px;">
        <label>Informe seu e-mail cadastrado:</label><br>
        <input type="email" name="email" required style="width:100%; padding:8px;">
    </div>
    <button type="submit" style="padding:8px 15px; background:#17a2b8; color:white; border:none; cursor:pointer;">Enviar Instruções</button>
</form>

<?php require_once 'footer.php'; ?>