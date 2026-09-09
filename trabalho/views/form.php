<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= $lote ? 'Editar' : 'Novo' ?> Lote</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2><?= $lote ? 'Editar Lote' : 'Registrar Novo Lote' ?></h2>
    
    <form action="index.php?acao=salvar" method="POST">
        <input type="hidden" name="id" value="<?= $lote['id'] ?? '' ?>">
        
        <div class="mb-3">
            <label>Token (Impresso na Nota Fiscal):</label>
            <input type="text" name="token" class="form-control" value="<?= htmlspecialchars($lote['token'] ?? '') ?>" required>
        </div>
        
        <div class="mb-3">
            <label>Status da Avaliação:</label>
            <select name="status" class="form-control" required>
                <option value="Aguardando Avaliação" <?= ($lote['status'] ?? '') == 'Aguardando Avaliação' ? 'selected' : '' ?>>Aguardando Avaliação</option>
                <option value="Proposta Enviada por SMS" <?= ($lote['status'] ?? '') == 'Proposta Enviada por SMS' ? 'selected' : '' ?>>Proposta Enviada por SMS</option>
                <option value="Aprovado (Aguardando PIX)" <?= ($lote['status'] ?? '') == 'Aprovado (Aguardando PIX)' ? 'selected' : '' ?>>Aprovado (Aguardando PIX)</option>
                <option value="Rejeitado pelo Cliente" <?= ($lote['status'] ?? '') == 'Rejeitado pelo Cliente' ? 'selected' : '' ?>>Rejeitado pelo Cliente</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Selecione as categorias de roupas presentes neste lote (N:N):</label>
            <div class="card p-3">
                <?php foreach ($categorias as $cat): ?>
                    <?php 
                        $checked = (isset($lote['categorias']) && in_array($cat['id'], $lote['categorias'])) ? 'checked' : ''; 
                    ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="categorias[]" value="<?= $cat['id'] ?>" id="cat_<?= $cat['id'] ?>" <?= $checked ?>>
                        <label class="form-check-label" for="cat_<?= $cat['id'] ?>">
                            <?= htmlspecialchars($cat['nome']) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Salvar Lote</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>