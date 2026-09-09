<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Lotes - Brechó</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Lotes Avaliados (Relação N:N)</h2>
    <a href="index.php?acao=novo" class="btn btn-success mb-3">Novo Lote (Token)</a>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Token</th>
                <th>Status da Proposta</th>
                <th>Categorias de Peças (N:N)</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lotes as $l): ?>
            <tr>
                <td><?= $l['id'] ?></td>
                <td><strong><?= htmlspecialchars($l['token']) ?></strong></td>
                <td><?= htmlspecialchars($l['status']) ?></td>
                <td><?= htmlspecialchars($l['categorias_nomes'] ?? 'Nenhuma peça vinculada') ?></td>
                <td>
                    <a href="index.php?acao=editar&id=<?= $l['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                    <a href="index.php?acao=deletar&id=<?= $l['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Certeza?')">Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>