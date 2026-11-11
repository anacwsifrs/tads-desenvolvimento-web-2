<?php require_once 'views/include/header.php'; ?>

<style>
    .cart-table { width: 100%; border-collapse: collapse; background: white; margin-top: 15px; }
    .cart-table th, .cart-table td { padding: 12px; border: 1px solid #ddd; text-align: left; vertical-align: middle; }
    .cart-table th { background: #e9ecef; }
    .btn-finalizar { padding: 12px 20px; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; font-size: 16px; width: 100%; }
    .btn-remover { color: #dc3545; text-decoration: none; font-weight: bold; font-size: 14px; }
</style>

<h2>Meu Carrinho de Compras</h2>

<?php if (empty($itensCarrinho)): ?>
    <div style="background: white; padding: 30px; text-align: center; margin-top: 20px; border-radius: 8px;">
        <h3>Seu carrinho está vazio! 🛍️</h3>
        <a href="index.php?acao=vitrine" style="display:inline-block; margin-top:15px; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px;">Voltar para a Vitrine</a>
    </div>
<?php else: ?>
    <?php $subtotal = 0; ?>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Peça</th>
                <th>Valor Venda</th>
                <th style="width: 80px;">Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($itensCarrinho as $id => $item): ?>
                <?php $subtotal += $item['preco_venda']; ?>
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <?php if (!empty($item['foto_caminho']) && file_exists('uploads/' . $item['foto_caminho'])): ?>
                                <img src="uploads/<?= htmlspecialchars($item['foto_caminho']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                            <?php endif; ?>
                            <strong><?= htmlspecialchars($item['descricao']) ?></strong>
                        </div>
                    </td>
                    <td><strong>R$ <?= number_format($item['preco_venda'], 2, ',', '.') ?></strong></td>
                    <td><a href="index.php?acao=remover_carrinho&id=<?= $id ?>" class="btn-remover">Remover</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="font-size: 20px; text-align: right; margin-top: 15px; font-weight: bold;">
        Total: <span style="color: #28a745;">R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
    </div>

    <div style="background: #e8f5e9; padding: 20px; border-radius: 8px; margin-top: 20px;">
        <h3 style="margin-top:0; color: #2e7d32;">📱 Confirmar Pedido</h3>
        <p>Ao finalizar, as peças serão reservadas para você. Você será redirecionado para a tela de compras onde poderá anexar o comprovante do PIX.</p>
        
        <form action="index.php?acao=finalizar_compra" method="POST" style="margin-top: 15px;">
            <button type="submit" class="btn-finalizar">✅ Gerar Pedido & Reservar</button>
        </form>
    </div>
<?php endif; ?>

<?php require_once 'views/include/footer.php'; ?>