<?php
require_once 'header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("
    SELECT p.*, c.nome as categoria_nome 
    FROM pecas p 
    JOIN categorias c ON p.categoria_id = c.id 
    WHERE p.id = ?
");
$stmt->execute([$id]);
$peca = $stmt->fetch();

if (!$peca) {
    echo "<h2>Peça não encontrada!</h2><a href='vitrine.php'>Voltar à vitrine</a>";
    require_once 'footer.php';
    exit;
}

$preco_venda = $peca['valor_proposto'] * 1.4;
?>

<a href="index.php?acao=vitrine" style="text-decoration:none; color:#007bff;">← Voltar para a Vitrine</a>

<div style="display:flex; gap:30px; margin-top:20px; align-items:flex-start;">
    <div style="width:300px; height:300px; background:#e9ecef; display:flex; align-items:center; justify-content:center; border-radius:8px; font-size:48px;">
        👕
    </div>
    <div>
        <span style="background:#28a745; color:white; padding:4px 8px; border-radius:4px; font-size:12px; font-weight:bold;">Disponível em Loja</span>
        <h2 style="margin-top:10px;"><?= htmlspecialchars($peca['descricao'] ?: 'Peça de Vestuário Exclusiva') ?></h2>
        <p><strong>Categoria:</strong> <?= htmlspecialchars($peca['categoria_nome']) ?></p>
        <p><strong>Código do Item:</strong> #PECA-<?= $peca['id'] ?></p>
        
        <h1 style="color:#28a745; margin:15px 0;">R$ <?= number_format($preco_venda, 2, ',', '.') ?></h1>

        <form action="index.php?acao=carrinho" method="POST">
            <input type="hidden" name="peca_id" value="<?= $peca['id'] ?>">
            <button type="submit" style="padding:12px 25px; background:#ff9800; color:white; border:none; font-size:16px; font-weight:bold; cursor:pointer; border-radius:5px;">🛒 Reservar / Adicionar ao Carrinho</button>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>