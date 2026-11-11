<?php require_once 'views/include/header.php'; ?>

<h2>Minhas Compras e Reservas</h2>
<p style="margin-bottom:20px; color:#666;">Acompanhe seus pedidos e envie o comprovante PIX para confirmação da reserva.</p>

<table style="width:100%; border-collapse:collapse; background:white;">
    <thead>
        <tr style="background:#e9ecef; text-align:left;">
            <th style="padding:10px; border:1px solid #ddd;">#Pedido</th>
            <th style="padding:10px; border:1px solid #ddd;">Data</th>
            <th style="padding:10px; border:1px solid #ddd;">Valor Total</th>
            <th style="padding:10px; border:1px solid #ddd;">Status</th>
            <th style="padding:10px; border:1px solid #ddd;">Comprovante PIX</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($pedidos)): ?>
            <tr><td colspan="5" style="padding:15px; text-align:center;">Você ainda não realizou nenhuma compra.</td></tr>
        <?php else: ?>
            <?php foreach ($pedidos as $p): ?>
                <tr>
                    <td style="padding:10px; border:1px solid #ddd;"><strong>#PED-<?= $p['id'] ?></strong></td>
                    <td style="padding:10px; border:1px solid #ddd;"><?= date('d/m/Y H:i', strtotime($p['criado_em'])) ?></td>
                    <td style="padding:10px; border:1px solid #ddd; font-weight:bold; color:#28a745;">R$ <?= number_format($p['valor_total'], 2, ',', '.') ?></td>
                    <td style="padding:10px; border:1px solid #ddd;">
                        <?php 
                            if ($p['status'] === 'Aprovado') echo "<span style='color:#28a745; font-weight:bold;'>✅ Aprovado</span>";
                            elseif ($p['status'] === 'Cancelado') echo "<span style='color:#dc3545; font-weight:bold;'>❌ Cancelado</span>";
                            else echo "<span style='color:#ff9800; font-weight:bold;'>⏳ Aguardando PIX</span>";
                        ?>
                    </td>
                    <td style="padding:10px; border:1px solid #ddd;">
                        <?php if (!empty($p['comprovante'])): ?>
                            <a href="uploads/<?= htmlspecialchars($p['comprovante']) ?>" target="_blank" style="color:#007bff; font-weight:bold;">📄 Ver Comprovante</a>
                        <?php elseif ($p['status'] === 'Aguardando PIX'): ?>
                            <form action="index.php?acao=upload_comprovante_pix" method="POST" enctype="multipart/form-data" style="display:flex; gap:5px;">
                                <input type="hidden" name="pedido_id" value="<?= $p['id'] ?>">
                                <input type="file" name="comprovante" accept="image/*,.pdf" required style="font-size:11px; width:130px;">
                                <button type="submit" style="background:#28a745; color:white; border:none; padding:4px 8px; border-radius:3px; cursor:pointer; font-size:11px;">Enviar</button>
                            </form>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'views/include/footer.php'; ?>