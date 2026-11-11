<?php require_once 'views/include/header.php'; ?>

<h2>Recuperação de Senha</h2>
<?php if (!empty($msg)): ?>
    <p style="color: #0c5460; background: #d1ecf1; padding: 10px; border-radius: 5px; font-weight:bold; margin-bottom:15px; max-width: 400px; border: 1px solid #bee5eb;"><?= htmlspecialchars($msg) ?></p>
<?php endif; ?>

<form action="index.php?acao=recuperar_senha" method="POST" style="max-width:400px;">
    <div style="margin-bottom:15px;">
        <label style="font-weight: bold; margin-bottom: 5px; display: block;">Informe seu e-mail cadastrado:</label>
        <input type="email" name="email" required style="width:100%; padding:10px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <button type="submit" style="padding:10px 15px; background:#17a2b8; color:white; border:none; border-radius: 4px; font-weight: bold; cursor:pointer;">Enviar Instruções</button>
</form>

<?php require_once 'views/include/footer.php'; ?>