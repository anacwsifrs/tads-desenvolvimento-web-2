<?php require_once 'views/include/header.php'; ?>

<h2>Login do Usuário</h2>
<?php if (!empty($erro)): ?>
    <p style="color:red; font-weight:bold; background: #f8d7da; padding: 10px; border-radius: 5px;"><?= htmlspecialchars($erro) ?></p>
<?php endif; ?>

<form action="index.php?acao=login" method="POST" style="max-width:400px; margin-top:15px;">
    <div style="margin-bottom:10px;">
        <label>E-mail:</label><br>
        <input type="email" name="email" required style="width:100%; padding:8px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div style="margin-bottom:10px;">
        <label>Senha:</label><br>
        <input type="password" name="senha" required style="width:100%; padding:8px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <button type="submit" style="padding:10px 20px; background:#007bff; color:white; border:none; border-radius: 4px; cursor:pointer; font-weight: bold;">Entrar</button>
</form>
<p style="margin-top:15px;">
    Não tem conta? <a href="index.php?acao=cadastro" style="color: #007bff;">Cadastre-se aqui</a> | <a href="index.php?acao=recuperar_senha" style="color: #007bff;">Esqueceu a senha?</a>
</p>

<?php require_once 'views/include/footer.php'; ?>