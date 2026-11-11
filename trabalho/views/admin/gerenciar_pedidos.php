<?php require_once 'views/include/header.php'; ?>

<h2>Gestão de Vendas & Reservas</h2>
<p style="margin-bottom:20px; color:#666;">Confira os pedidos realizados pelos clientes, analise os comprovantes PIX e aprove as baixas.</p>

<table style="width:100%; border-collapse:collapse; background:white;">
    <thead>
        <tr style="background:#e9ecef; text-align:left;">
            <th style="padding:10px; border:1px solid #ddd;">#Pedido</th>
            <th style="padding:10px; border:1px solid #ddd;">Cliente</th>
            <th style="padding:10px; border:1px solid #ddd;">Data</th>
            <th style="padding:10px; border:1px solid #ddd;">Valor</th>
            <th style="padding:10px; border:1px solid #ddd;">Comprovante</th>
            <th style="padding:10px; border:1px solid #ddd;">Status</th>
            <th style="padding:10px; border:1px solid #ddd;">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($pedidos)): ?>
            <tr><td colspan="7" style="padding:15px; text-align:center;">Nenhum pedido registrado no momento.</td></tr>
        <?php else: ?>
            <?php foreach ($pedidos as $p): ?>
                <tr>
                    <td style="padding:10px; border:1px solid #ddd;"><strong>#PED-<?= $p['id'] ?></strong></td>
                    <td style="padding:10px; border:1px solid #ddd;"><?= htmlspecialchars($p['cliente_nome']) ?></td>
                    <td style="padding:10px; border:1px solid #ddd;"><?= date('d/m/Y H:i', strtotime($p['criado_em'])) ?></td>
                    <td style="padding:10px; border:1px solid #ddd; font-weight:bold;">R$ <?= number_format($p['valor_total'], 2, ',', '.') ?></td>
                    <td style="padding:10px; border:1px solid #ddd;">
                        <?php if (!empty($p['comprovante'])): ?>
                            <a href="uploads/<?= htmlspecialchars($p['comprovante']) ?>" target="_blank" style="color:#007bff; font-weight:bold;">📄 Anexo</a>
                        <?php else: ?>
                            <small style="color:#888;">Sem anexo</small>
                        <?php endif; ?>
                    </td>
                    <td style="padding:10px; border:1px solid #ddd;">
                        <strong><?= htmlspecialchars($p['status']) ?></strong>
                    </td>
                    <td style="padding:10px; border:1px solid #ddd;">
                        <?php if ($p['status'] === 'Aguardando PIX'): ?>
                            <a href="index.php?acao=aprovar_pedido&id=<?= $p['id'] ?>" style="padding:4px 8px; background:#28a745; color:white; text-decoration:none; border-radius:3px; font-size:12px;" onclick="return confirm('Confirmar pagamento PIX deste pedido?')">✔️ Aprovar</a>
                            <a href="index.php?acao=cancelar_pedido&id=<?= $p['id'] ?>" style="padding:4px 8px; background:#dc3545; color:white; text-decoration:none; border-radius:3px; font-size:12px;" onclick="return confirm('Cancelar pedido e devolver peças para a vitrine?')">❌ Cancelar</a>
                        <?php else: ?>
                            <small style="color:#888;">Finalizado</small>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'views/include/footer.php'; ?>