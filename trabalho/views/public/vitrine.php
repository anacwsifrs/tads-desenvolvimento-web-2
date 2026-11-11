<?php require_once 'views/include/header.php'; ?>

<h2>Vitrine de Peças Disponíveis no Brechó</h2>
<p style="margin-bottom:15px; color:#666;">Confira os itens selecionados e higienizados prontos para compra presencial ou reserva.</p>

<div style="display: flex; flex-wrap: wrap; gap: 20px;">
    <?php if (empty($pecas)): ?>
        <p>Nenhuma peça disponível na vitrine no momento.</p>
    <?php else: ?>
        <?php foreach ($pecas as $p): ?>
            <div style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; width: 250px; background: #fff; text-align: center;">
                <?php if (!empty($p['foto_caminho']) && file_exists('uploads/' . $p['foto_caminho'])): ?>
                    <img src="uploads/<?= htmlspecialchars($p['foto_caminho']) ?>" alt="Foto da peça" style="max-width: 100%; height: 200px; object-fit: cover; border-radius: 5px; margin-bottom: 10px;">
                <?php else: ?>
                    <div style="width: 100%; height: 200px; background:#e9ecef; display:flex; align-items:center; justify-content:center; border-radius:5px; font-size:48px; margin-bottom: 10px;">👕</div>
                <?php endif; ?>
                
                <span style="background:#e9ecef; padding:3px 8px; font-size:12px; border-radius:3px; font-weight:bold; color: #333;"><?= htmlspecialchars($p['categoria_nome']) ?></span>
                <h3 style="margin: 10px 0 5px 0; font-size:16px;"><?= htmlspecialchars($p['descricao'] ?: 'Peça de Vestuário') ?></h3>
                
                <?php if (!empty($p['tamanho'])): ?>
                    <p style="margin: 5px 0; font-size: 14px; color: #555;">Tamanho: <strong><?= htmlspecialchars($p['tamanho']) ?></strong></p>
                <?php endif; ?>

                <p style="font-size: 20px; color: #28a745; font-weight: bold; margin-top: 10px;">R$ <?= number_format($p['valor_proposto'] * 1.4, 2, ',', '.') ?></p>
                <a href="index.php?acao=detalhar&id=<?= $p['id'] ?>" style="display:block; margin-top:10px; padding:10px; background:#007bff; color:white; text-decoration:none; border-radius:4px; font-size:14px; font-weight: bold;">Ver Detalhes</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once 'views/include/footer.php'; ?>