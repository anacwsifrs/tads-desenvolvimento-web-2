<?php require_once 'views/include/header.php'; ?>

<h2>Fale Conosco</h2>
<?php if (!empty($mensagem_status)): ?>
    <div style="margin-bottom: 15px; max-width: 600px;">
        <?= $mensagem_status ?>
    </div>
<?php endif; ?>

<form action="index.php?acao=fale_conosco" method="POST" style="margin-top:15px; max-width:600px;">
    <div style="margin-bottom:10px;">
        <label>Seu Nome:</label><br>
        <input type="text" name="nome" required style="width:100%; padding:8px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div style="margin-bottom:10px;">
        <label>Seu E-mail:</label><br>
        <input type="email" name="email" required style="width:100%; padding:8px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div style="margin-bottom:10px;">
        <label>Assunto:</label><br>
        <input type="text" name="assunto" required style="width:100%; padding:8px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div style="margin-bottom:10px;">
        <label>Mensagem:</label><br>
        <textarea name="mensagem" rows="5" required style="width:100%; padding:8px; border: 1px solid #ccc; border-radius: 4px;"></textarea>
    </div>
    <button type="submit" style="padding:10px 20px; background:#007bff; color:white; border:none; border-radius: 4px; font-weight: bold; cursor:pointer;">Enviar Mensagem</button>
</form>

<?php require_once 'views/include/footer.php'; ?>