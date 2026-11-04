<?php
require_once 'header.php';

$stmt = $pdo->query("
    SELECT p.*, c.nome as categoria_nome 
    FROM pecas p 
    JOIN categorias c ON p.categoria_id = c.id 
    WHERE p.status_avaliacao = 1 
    ORDER BY p.id DESC
");
$pecas = $stmt->fetchAll();
?>

<h2>Vitrine de Peças Disponíveis no Brechó</h2>
<p style="margin-bottom:15px; color:#666;">Confira os itens selecionados e higienizados prontos para compra presencial ou reserva.</p>

<div style="display: flex; flex-wrap: wrap; gap: 20px;">
    <?php if (empty($pecas)): ?>
        <p>Nenhuma peça disponível na vitrine no momento.</p>
    <?php else: ?>
        <?php foreach ($pecas as $p): ?>
            <div style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; width: 250px; background: #fff;">
                <span style="background:#e9ecef; padding:3px 8px; font-size:12px; border-radius:3px; font-weight:bold;"><?= htmlspecialchars($p['categoria_nome']) ?></span>
                <h3 style="margin: 10px 0 5px 0; font-size:18px;"><?= htmlspecialchars($p['descricao'] ?: 'Peça de Vestuário') ?></h3>
                <p style="font-size: 20px; color: #28a745; font-weight: bold;">R$ <?= number_format($p['valor_proposto'] * 1.4, 2, ',', '.') ?></p> <!-- Margem de venda da loja -->
                <a href="index.php?acao=detalhar&id=<?= $p['id'] ?>" style="display:inline-block; margin-top:10px; padding:8px 12px; background:#007bff; color:white; text-decoration:none; border-radius:4px; font-size:14px;">Ver Detalhes</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>