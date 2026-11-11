<?php 
require_once 'views/include/header.php'; 
$preco_venda = $peca['valor_proposto'] * 1.4;
?>

<a href="index.php?acao=vitrine" style="text-decoration:none; color:#007bff; margin-bottom: 20px; display: inline-block; font-weight: bold;">← Voltar para a Vitrine</a>

<div style="display:flex; gap:30px; margin-top:10px; align-items:flex-start; flex-wrap: wrap;">
    <div style="width: 400px; max-width: 100%; border-radius:8px; overflow: hidden; border: 1px solid #ddd;">
        <?php if (!empty($peca['foto_caminho']) && file_exists('uploads/' . $peca['foto_caminho'])): ?>
            <img src="uploads/<?= htmlspecialchars($peca['foto_caminho']) ?>" alt="Foto da peça" style="width: 100%; display: block;">
        <?php else: ?>
            <div style="width:100%; height:400px; background:#e9ecef; display:flex; align-items:center; justify-content:center; font-size:64px;">👕</div>
        <?php endif; ?>
    </div>
    
    <div style="flex: 1; min-width: 300px;">
        <span style="background:#28a745; color:white; padding:4px 8px; border-radius:4px; font-size:12px; font-weight:bold;">Disponível na Loja Física</span>
        <h2 style="margin-top:15px; margin-bottom: 5px;"><?= htmlspecialchars($peca['descricao'] ?: 'Peça de Vestuário Exclusiva') ?></h2>
        <p style="color: #666; margin-bottom: 20px;"><strong>Código do Item:</strong> #PECA-<?= $peca['id'] ?></p>
        
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #e9ecef; margin-bottom: 20px;">
            <p style="margin: 5px 0;"><strong>Categoria:</strong> <?= htmlspecialchars($peca['categoria_nome']) ?></p>
            <p style="margin: 5px 0;"><strong>Marca:</strong> <?= htmlspecialchars($peca['marca'] ?: 'Não informada') ?></p>
            <p style="margin: 5px 0;"><strong>Tamanho:</strong> <?= htmlspecialchars($peca['tamanho'] ?: 'Único') ?></p>
            <p style="margin: 5px 0;"><strong>Cor:</strong> <?= htmlspecialchars($peca['cor'] ?: 'Não informada') ?></p>
            <p style="margin: 5px 0;"><strong>Estado:</strong> <?= htmlspecialchars($peca['estado'] ?: 'Bom') ?></p>
        </div>

        <?php if (!empty($peca['observacoes'])): ?>
            <div style="margin-bottom: 20px;">
                <h4 style="margin-bottom: 5px; color: #495057;">Observações da Triagem:</h4>
                <p style="color: #d9534f; font-size: 14px;"><?= nl2br(htmlspecialchars($peca['observacoes'])) ?></p>
            </div>
        <?php endif; ?>
        
        <h1 style="color:#28a745; margin:15px 0; font-size: 36px;">R$ <?= number_format($preco_venda, 2, ',', '.') ?></h1>
        <p style="color: #666; font-size: 13px;">* Esta peça deve ser adquirida presencialmente no balcão da loja.</p>
    </div>
</div>

<?php require_once 'views/include/footer.php'; ?>