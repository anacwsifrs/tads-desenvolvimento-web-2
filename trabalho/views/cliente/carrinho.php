<?php require_once 'views/include/header.php'; ?>

<style>
    .cart-table { width: 100%; border-collapse: collapse; background: white; margin-top: 15px; }
    .cart-table th, .cart-table td { padding: 12px; border: 1px solid #ddd; text-align: left; vertical-align: middle; }
    .cart-table th { background: #e9ecef; }
    .btn-finalizar { padding: 12px 20px; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; font-size: 16px; width: 100%; }
    .btn-remover { color: #dc3545; text-decoration: none; font-weight: bold; font-size: 14px; }
    .box-saldo { background: #fff3cd; padding: 15px; border: 1px solid #ffeeba; border-radius: 5px; margin-top: 20px; display: flex; align-items: center; gap: 10px; font-size: 16px; }
    .box-resumo { font-size: 20px; text-align: right; margin-top: 15px; background: white; padding: 15px; border-radius: 5px; }
</style>

<h2>Meu Carrinho de Compras</h2>

<?php if (empty($itensCarrinho)): ?>
    <div style="background: white; padding: 30px; text-align: center; margin-top: 20px; border-radius: 8px;">
        <h3>Seu carrinho está vazio! 🛍️</h3>
        <a href="index.php?acao=vitrine" style="display:inline-block; margin-top:15px; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px;">Voltar para a Vitrine</a>
    </div>
<?php else: ?>
    <?php 
        $subtotal = 0;
        $saldo = $cliente['saldo_credito'] ?? 0;
    ?>
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

    <form action="index.php?acao=finalizar_compra" method="POST">
        
        <?php if ($saldo > 0): ?>
            <div class="box-saldo">
                <input type="checkbox" id="usar_saldo" name="usar_saldo" value="1" onchange="calcularTotal()" style="width: 20px; height: 20px;">
                <label for="usar_saldo" style="cursor: pointer; font-weight: bold; color: #856404;">
                    Usar meu saldo disponível de R$ <?= number_format($saldo, 2, ',', '.') ?>
                </label>
            </div>
        <?php endif; ?>

        <div class="box-resumo">
            Subtotal: <strong>R$ <span id="span-subtotal"><?= number_format($subtotal, 2, ',', '') ?></span></strong><br>
            <div id="linha-desconto" style="display: none; color: #dc3545; font-size: 16px;">
                Desconto do Saldo: <strong>- R$ <span id="span-desconto">0,00</span></strong><br>
            </div>
            Total a Pagar: <span style="color: #28a745; font-weight: bold; font-size: 24px;">R$ <span id="span-total"><?= number_format($subtotal, 2, ',', '') ?></span></span>
        </div>

        <div style="background: #e8f5e9; padding: 20px; border-radius: 8px; margin-top: 20px;">
            <h3 style="margin-top:0; color: #2e7d32;">📱 Confirmar Pedido</h3>
            <p id="msg-instrucao">Ao finalizar, você será redirecionado para a tela de compras onde poderá anexar o comprovante do PIX.</p>
            
            <button type="submit" class="btn-finalizar" id="btn-submit">✅ Gerar Pedido & Reservar</button>
        </div>
    </form>

    <script>
        const subtotal = <?= $subtotal ?>;
        const saldo = <?= $saldo ?>;

        function calcularTotal() {
            const checkbox = document.getElementById('usar_saldo');
            const spanDesconto = document.getElementById('span-desconto');
            const spanTotal = document.getElementById('span-total');
            const linhaDesconto = document.getElementById('linha-desconto');
            const msgInstrucao = document.getElementById('msg-instrucao');
            const btnSubmit = document.getElementById('btn-submit');
            
            let desconto = 0;
            let totalFinal = subtotal;

            if (checkbox && checkbox.checked) {
                desconto = Math.min(subtotal, saldo);
                totalFinal = subtotal - desconto;
                
                linhaDesconto.style.display = 'block';
                spanDesconto.innerText = desconto.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            } else {
                linhaDesconto.style.display = 'none';
            }

            spanTotal.innerText = totalFinal.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});

            if (totalFinal === 0) {
                msgInstrucao.innerText = "Seu saldo cobre o valor total do pedido. A reserva será confirmada imediatamente após clicar no botão, sem necessidade de PIX!";
                btnSubmit.innerText = "✅ Finalizar Compra com Saldo";
            } else {
                msgInstrucao.innerText = "Ao finalizar, você será redirecionado para a tela de compras onde poderá anexar o comprovante do PIX do valor restante.";
                btnSubmit.innerText = "✅ Gerar Pedido & Reservar";
            }
        }
    </script>
<?php endif; ?>

<?php require_once 'views/include/footer.php'; ?>