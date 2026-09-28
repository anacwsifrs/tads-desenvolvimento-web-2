<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= $lote ? 'Editar' : 'Novo' ?> Lote</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4 mb-5">
    <h2><?= $lote ? 'Editar Lote' : 'Registrar Novo Lote' ?></h2>
    
    <form action="index.php?acao=salvar" method="POST" enctype="multipart/form-data" class="mt-3">
        <input type="hidden" name="id" value="<?= $lote['id'] ?? '' ?>">
        <input type="hidden" name="comprovante_atual" value="<?= $lote['comprovante_pix'] ?? '' ?>">
        
        <div class="mb-3">
            <label class="form-label">Código do Lote:</label>
            <input type="text" name="codigo" class="form-control" value="<?= htmlspecialchars($lote['codigo'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Chave PIX do Cliente:</label>
            <input type="text" name="chave_pix" class="form-control" value="<?= htmlspecialchars($lote['chave_pix'] ?? '') ?>" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Valor da Proposta em PIX (R$):</label>
                <input type="number" step="0.01" id="valor_proposta" name="valor_proposta" class="form-control" value="<?= htmlspecialchars($lote['valor_proposta'] ?? '0.00') ?>" required oninput="calcularCredito()">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Valor em Crédito na Loja (+20% Bônus):</label>
                <input type="text" id="valor_credito_preview" class="form-control bg-light" value="R$ <?= number_format(($lote['valor_credito'] ?? 0), 2, ',', '.') ?>" readonly>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Opção de Recebimento Escolhida pelo Cliente:</label>
            <select name="tipo_recebimento" class="form-select">
                <option value="pix" <?= ($lote['tipo_recebimento'] ?? '') == 'pix' ? 'selected' : '' ?>>PIX (Valor Padrão)</option>
                <option value="credito_loja" <?= ($lote['tipo_recebimento'] ?? '') == 'credito_loja' ? 'selected' : '' ?>>Crédito na Loja (Com Bônus de 20%)</option>
            </select>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Status da Avaliação e Proposta:</label>
            <select name="status" class="form-select" required>
                <option value="Aguardando Avaliação" <?= ($lote['status'] ?? '') == 'Aguardando Avaliação' ? 'selected' : '' ?>>Aguardando Avaliação</option>
                <option value="Aprovado Totalmente - Aguardando Aceite" <?= ($lote['status'] ?? '') == 'Aprovado Totalmente - Aguardando Aceite' ? 'selected' : '' ?>>Aprovado Totalmente - Aguardando Aceite</option>
                <option value="Aprovado Parcialmente - Aguardando Aceite" <?= ($lote['status'] ?? '') == 'Aprovado Parcialmente - Aguardando Aceite' ? 'selected' : '' ?>>Aprovado Parcialmente - Aguardando Aceite</option>
                <option value="Proposta Aceita (PIX)" <?= ($lote['status'] ?? '') == 'Proposta Aceita (PIX)' ? 'selected' : '' ?>>Proposta Aceita (PIX)</option>
                <option value="Proposta Aceita (Crédito na Loja)" <?= ($lote['status'] ?? '') == 'Proposta Aceita (Crédito na Loja)' ? 'selected' : '' ?>>Proposta Aceita (Crédito na Loja)</option>
                <option value="Proposta Recusada pelo Cliente" <?= ($lote['status'] ?? '') == 'Proposta Recusada pelo Cliente' ? 'selected' : '' ?>>Proposta Recusada pelo Cliente</option>
                <option value="Recusado pelo Brechó" <?= ($lote['status'] ?? '') == 'Recusado pelo Brechó' ? 'selected' : '' ?>>Recusado pelo Brechó</option>
                <option value="Concluído (PIX Realizado)" <?= ($lote['status'] ?? '') == 'Concluído (PIX Realizado)' ? 'selected' : '' ?>>Concluído (PIX Realizado)</option>
                <option value="Concluído (Crédito Liberado)" <?= ($lote['status'] ?? '') == 'Concluído (Crédito Liberado)' ? 'selected' : '' ?>>Concluído (Crédito Liberado)</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Comprovante de Pagamento PIX (opcional):</label>
            <input type="file" name="comprovante_pix" class="form-control">
            <?php if (!empty($lote['comprovante_pix'])): ?>
                <div class="form-text mt-1">Anexo atual: <a href="<?= $lote['comprovante_pix'] ?>" target="_blank">Abrir Comprovante</a></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">Categorias das Peças no Lote:</label>
            <div class="card p-3" style="max-height: 200px; overflow-y: auto;">
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
        
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </form>

    <script>
        function calcularCredito() {
            const val = parseFloat(document.getElementById('valor_proposta').value) || 0;
            const credito = val * 1.20;
            document.getElementById('valor_credito_preview').value = 'R$ ' + credito.toFixed(2).replace('.', ',');
        }
    </script>
</body>
</html>