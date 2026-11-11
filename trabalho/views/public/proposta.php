<?php 
require_once 'views/include/header.php'; 
$valor_pix = $lote['valor_proposta'] ?? 0;
$valor_credito = $valor_pix * 1.15; 
?>

<style>
    body { font-family: Arial, sans-serif;}
    .container-proposta { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto; }
    .container-proposta h2 { text-align: center; color: #2c3e50; }
    .resumo { background: #eef2f5; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    .valores { display: flex; flex-direction: row; gap: 15px; margin-bottom: 25px; }
    @media(max-width: 600px){ .valores { flex-direction: column; } }
    .card-valor { flex: 1; padding: 15px; border-radius: 8px; border: 2px solid transparent; text-align: center; }
    .card-pix { background: #e8f5e9; border-color: #4caf50; }
    .card-credito { background: #fff3e0; border-color: #ff9800; }
    .valor-texto { font-size: 24px; font-weight: bold; margin: 10px 0; }
    .btn { padding: 15px; width: 100%; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; cursor: pointer; margin-bottom: 10px; transition: 0.2s; }
    .btn-pix { background: #4caf50; color: white; }
    .btn-credito { background: #ff9800; color: white; }
    .btn-recusar { background: #f44336; color: white; margin-top: 15px; }
    .btn:hover { opacity: 0.9; transform: scale(1.02); }
    .status-msg { text-align: center; padding: 20px; font-size: 16px; font-weight: bold; border-radius: 5px; line-height: 1.5; }
    .badge-aprovada { color: #28a745; font-weight: bold; }
    .badge-reprovada { color: #dc3545; font-weight: bold; text-decoration: line-through; }
</style>
<div class="container-proposta">
    <h2>Olá, <?= htmlspecialchars($lote['cliente_nome']) ?>!</h2>
    
    <?php if ($lote['status'] < 3): ?>
        <div class="status-msg" style="background: #e9ecef; color: #495057;">
            ⏳ <strong>Seu lote ainda está em processo de avaliação!</strong><br><br>
            Nossa equipe está analisando suas peças com carinho. Assim que a avaliação for concluída, os valores da proposta aparecerão nesta mesma página.
        </div>
    <?php else:?>

        <p style="text-align: center;">Aqui está o resultado da avaliação do seu lote.</p>
        <div class="resumo">
            <h3 style="margin-top: 0;">Peças Avaliadas:</h3>
            <ul style="margin-bottom: 0;">
                <?php foreach ($lote['pecas'] as $peca): ?>
                    <li style="margin-bottom: 5px;">
                        <?= htmlspecialchars($peca['categoria_nome']) ?> 
                        <?php if ($peca['descricao']): ?> (<?= htmlspecialchars($peca['descricao']) ?>) <?php endif; ?>
                        - 
                        <?php if ($peca['status_avaliacao'] == 1): ?>
                            <span class="badge-aprovada">Aprovada (R$ <?= number_format($peca['valor_proposto'], 2, ',', '.') ?>)</span>
                        <?php else: ?>
                            <span class="badge-reprovada">Reprovada</span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php if ($lote['status'] == 3):?>
            
            <h3 style="text-align: center; margin-top: 30px;">Escolha como deseja receber:</h3>
            
            <form action="index.php?acao=responder_proposta" method="POST">
                <input type="hidden" name="lote_id" value="<?= $lote['id'] ?>">
                <input type="hidden" name="token" value="<?= $lote['token'] ?>">
                
                <div class="valores">
                    <div class="card-valor card-pix">
                        <h4 style="margin:0;">Receber em Dinheiro (PIX)</h4>
                        <div class="valor-texto">R$ <?= number_format($valor_pix, 2, ',', '.') ?></div>
                        <button type="submit" name="decisao" value="4" class="btn btn-pix">Aceitar via PIX</button>
                    </div>

                    <div class="card-valor card-credito">
                        <h4 style="margin:0;">Crédito na Loja (+15%)</h4>
                        <div class="valor-texto">R$ <?= number_format($valor_credito, 2, ',', '.') ?></div>
                        <p style="font-size: 12px; margin-top:0;">Use este valor para comprar na loja!</p>
                        <button type="submit" name="decisao" value="5" class="btn btn-credito">Aceitar Crédito na Loja</button>
                    </div>
                </div>

                <button type="submit" name="decisao" value="6" class="btn btn-recusar" onclick="return confirm('Tem certeza que deseja recusar a proposta?')">Não aceito, quero retirar as peças</button>
            </form>

        <?php else:?>
            <div class="status-msg" style="background: #e9ecef; margin-top: 20px;">
                <?php 
                    if ($lote['status'] == 4) echo "<span style='color: #4caf50;'>✅ Você aceitou a proposta em PIX.</span>";
                    elseif ($lote['status'] == 5) echo "<span style='color: #ff9800;'>🛍️ Você aceitou os Créditos na Loja!</span>";
                    elseif ($lote['status'] == 6) echo "<span style='color: #f44336;'>❌ Você recusou a proposta. Retire suas peças na loja.</span>";
                    elseif ($lote['status'] == 7) echo "<span style='color: #343a40;'>📦 Prazo expirado. O lote foi destinado para doação.</span>";
                    else echo "Este lote já foi finalizado.";
                ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once 'views/include/footer.php'; ?>