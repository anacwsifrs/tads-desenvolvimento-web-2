<?php require_once 'header.php'; ?>

<style>
    body { font-family: Arial, sans-serif; padding: 20px;}
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .btn-voltar { padding: 10px 15px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
    table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    th, td { padding: 12px; border: 1px solid #dee2e6; text-align: left; }
    th { background-color: #e9ecef; }
    .saldo-positivo { font-weight: bold; color: #28a745; font-size: 16px; }
    .form-debitar { display: flex; gap: 5px; }
    .form-debitar input[type="number"] { width: 90px; padding: 5px; }
    .form-debitar input[type="text"] { width: 180px; padding: 5px; }
    .btn-debitar { background: #dc3545; color: white; border: none; padding: 6px 12px; cursor: pointer; border-radius: 3px; font-weight: bold; }
</style>
<div class="header">
    <h2>Gestão de Clientes e Crédito em Loja</h2>
    <a href="index.php" class="btn-voltar">← Voltar ao Painel Geral</a>
</div>

<table>
    <thead>
        <tr>
            <th>CPF</th>
            <th>Nome</th>
            <th>WhatsApp</th>
            <th>Saldo Disponível (Créditos)</th>
            <th>Debitar Saldo Utilizado em Loja</th>
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
                    <td>
                        <?php if ($c['saldo_credito'] > 0): ?>
                            <form action="index.php?acao=debitar_saldo" method="POST" class="form-debitar" onsubmit="return confirm('Confirma o débito no saldo deste cliente?')">
                                <input type="hidden" name="cliente_id" value="<?= $c['id'] ?>">
                                <input type="number" step="0.01" max="<?= $c['saldo_credito'] ?>" min="0.01" name="valor_gasto" placeholder="Valor R$" required>
                                <input type="text" name="justificativa" placeholder="Motivo / Peça comprada" required>
                                <button type="submit" class="btn-debitar">Debitar</button>
                            </form>
                        <?php else: ?>
                            <small style="color:#888;">Sem saldo disponível</small>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'footer.php'; ?>