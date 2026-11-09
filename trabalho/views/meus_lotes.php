<?php require_once 'header.php';?>

<h2>Meus Lotes Cadastrados</h2>
<p style="margin-bottom:20px;">Acompanhe o status e as propostas dos seus lotes de roupas enviado ao brechó.</p>

<table style="width:100%; border-collapse:collapse; background:white; margin-top:10px;">
    <thead>
        <tr style="background:#e9ecef; text-align:left;">
            <th style="padding:10px; border:1px solid #ddd;">#ID</th>
            <th style="padding:10px; border:1px solid #ddd;">Descrição</th>
            <th style="padding:10px; border:1px solid #ddd;">Status</th>
            <th style="padding:10px; border:1px solid #ddd;">Valor Ofertado</th>
            <th style="padding:10px; border:1px solid #ddd;">Ação</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($meusLotes)): ?>
            <tr><td colspan="5" style="padding:15px; text-align:center;">Você ainda não possui nenhum lote cadastrado.</td></tr>
        <?php else: ?>
            <?php 
            $statusMap = [
                1 => 'Aguardando Triagem',
                2 => 'Em Avaliação',
                3 => 'Proposta Pronta!',
                4 => 'Aceito (PIX)',
                5 => 'Aceito (Crédito)',
                6 => 'Recusado',
                7 => 'Doado'
            ];
            foreach ($meusLotes as $l): 
            ?>
                <tr>
                    <td style="padding:10px; border:1px solid #ddd;">#<?= $l['id'] ?></td>
                    <td style="padding:10px; border:1px solid #ddd;"><?= htmlspecialchars($l['descricao']) ?></td>
                    <td style="padding:10px; border:1px solid #ddd;">
                        <strong><?= $statusMap[$l['status']] ?></strong>
                    </td>
                    <td style="padding:10px; border:1px solid #ddd;">
                        <?= $l['valor_proposta'] ? 'R$ ' . number_format($l['valor_proposta'], 2, ',', '.') : '-' ?>
                    </td>
                    <td style="padding:10px; border:1px solid #ddd;">
                        <?php if ($l['status'] == 3): ?>
                            <a href="index.php?acao=proposta&token=<?= $l['token'] ?>" style="padding:5px 10px; background:#28a745; color:white; text-decoration:none; border-radius:3px;">Ver Proposta</a>
                        <?php else: ?>
                            <a href="index.php?acao=proposta&token=<?= $l['token'] ?>" style="padding:5px 10px; background:#17a2b8; color:white; text-decoration:none; border-radius:3px;">Detalhes</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'footer.php'; ?>