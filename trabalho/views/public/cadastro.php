<?php require_once 'views/include/header.php'; ?>

<h2>Cadastro de Cliente</h2>

<form action="index.php?acao=salvar_cadastro" method="POST" enctype="multipart/form-data" style="margin-top:15px; max-width: 600px;">
    <div style="margin-bottom:10px;">
        <label>CPF (obrigatório):</label><br>
        <input type="text" name="cpf" required placeholder="000.000.000-00" style="width:100%; padding:8px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div style="margin-bottom:10px;">
        <label>Nome Completo:</label><br>
        <input type="text" name="nome" required style="width:100%; padding:8px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div style="margin-bottom:10px;">
        <label>E-mail:</label><br>
        <input type="email" name="email" required style="width:100%; padding:8px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div style="margin-bottom:10px;">
        <label>Senha:</label><br>
        <input type="password" name="senha" required style="width:100%; padding:8px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div style="margin-bottom:10px;">
        <label>WhatsApp:</label><br>
        <input type="text" name="telefone" required style="width:100%; padding:8px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div style="margin-bottom:10px;">
        <label>Chave PIX:</label><br>
        <input type="text" name="pix" style="width:100%; padding:8px; border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div style="margin-bottom:15px;">
        <label>Foto de Perfil (Opcional):</label><br>
        <input type="file" name="foto" accept="image/*">
    </div>
    <button type="submit" style="padding:10px 20px; background:#28a745; color:white; border:none; border-radius:4px; cursor:pointer; font-weight: bold;">Cadastrar</button>
</form>

<?php require_once 'views/include/footer.php'; ?>