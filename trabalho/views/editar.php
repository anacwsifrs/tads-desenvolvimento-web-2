<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Lote #<?= $lote['id'] ?></title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 600px; margin: auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"] { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn-salvar { padding: 10px 15px; background-color: #007bff; color: white; border: none; cursor: pointer; width: 100%; font-size: 16px; }
    </style>
</head>
<body>
    <h2>Editar Dados do Lote #<?= $lote['id'] ?></h2>

    <form action="index.php?acao=salvar_edicao" method="POST">
        <input type="hidden" name="lote_id" value="<?= $lote['id'] ?>">

        <div class="form-group">
            <label>Nome do Cliente:</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($lote['cliente_nome']) ?>" required>
        </div>
        <div class="form-group">
            <label>WhatsApp:</label>
            <input type="text" name="telefone" value="<?= htmlspecialchars($lote['telefone']) ?>" required>
        </div>
        <div class="form-group">
            <label>Chave PIX:</label>
            <input type="text" name="pix" value="<?= htmlspecialchars($lote['chave_pix']) ?>" required>
        </div>
        <div class="form-group">
            <label>Descrição do Lote:</label>
            <input type="text" name="descricao_lote" value="<?= htmlspecialchars($lote['descricao']) ?>" required>
        </div>

        <button type="submit" class="btn-salvar">Salvar Alterações</button>
    </form>
</body>
</html>