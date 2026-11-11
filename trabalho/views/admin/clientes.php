<?php require_once 'views/include/header.php'; ?>

<style>
    body { font-family: Arial, sans-serif;}
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    th, td { padding: 12px; border: 1px solid #dee2e6; text-align: left; vertical-align: middle;}
    th { background-color: #e9ecef; }
    .saldo-positivo { font-weight: bold; color: #28a745; font-size: 15px; }
    .btn-acao { padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 14px; margin-right: 5px; display: inline-block;}
    .btn-ver { background: #17a2b8; color: white; }
    .btn-editar { background: #ffc107; color: #333; font-weight: bold; }
</style>

<div class="header">
    <h2>👥 Painel de Usuários</h2>
</div>

<table>
    <thead>
        <tr>
            <th>CPF</th>
            <th>Nome</th>
            <th>WhatsApp</th>
            <th>Saldo (Créditos)</th>
            <th style="width: 100px; text-align: center;">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($clientes)): ?>
            <tr><td colspan="5" style="text-align:center;">Nenhum cliente cadastrado.</td></tr>
        <?php else: ?>
            <?php foreach ($clientes as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['cpf']) ?></td>
                    <td><strong><?= htmlspecialchars($c['nome']) ?></strong></td>
                    <td><?= htmlspecialchars($c['telefone']) ?></td>
                    <td>
                        <span class="saldo-positivo">
                            R$ <?= number_format($c['saldo_credito'], 2, ',', '.') ?>
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <a href="index.php?acao=ver_cliente&id=<?= $c['id'] ?>" class="btn-acao btn-ver" title="Visualizar">👁️</a>
                        <a href="index.php?acao=editar_cliente&id=<?= $c['id'] ?>" class="btn-acao btn-editar" title="Editar">✏️</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'views/include/footer.php'; ?>