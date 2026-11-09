<?php require_once 'header.php'; ?>

<style>
    body { font-family: Arial, sans-serif; padding: 20px; max-width: 900px; margin: auto; }
    .card { background: #f8f9fa; padding: 15px; border-radius: 5px; border: 1px solid #ddd; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; vertical-align: middle; }
    th { background: #eee; }
    
    .btn-acao { padding: 8px 12px; border: none; cursor: pointer; border-radius: 4px; font-size: 14px; margin-right: 5px; opacity: 0.5; transition: 0.2s; }
    .btn-aprovar { background-color: #28a745; color: white; }
    .btn-reprovar { background-color: #dc3545; color: white; }
    .btn-acao.ativo { opacity: 1; font-weight: bold; transform: scale(1.1); box-shadow: 0 0 5px rgba(0,0,0,0.3); }
    
    .input-valor { padding: 8px; width: 100px; display: none; }
    .total-box { font-size: 20px; text-align: right; padding: 15px; background: #e9ecef; margin-top: 15px; border-radius: 5px; }
    .btn-salvar { padding: 12px 20px; background-color: #007bff; color: white; border: none; cursor: pointer; font-size: 16px; margin-top: 10px; width: 100%; }
</style>

<h2>Avaliação de Lote #<?= $lote['id'] ?></h2>

<div class="card">
    <p><strong>Fornecedor:</strong> <?= htmlspecialchars($lote['cliente_nome']) ?> (<?= htmlspecialchars($lote['telefone']) ?>)</p>
    <p><strong>Lote:</strong> <?= htmlspecialchars($lote['descricao']) ?></p>
</div>

<form action="index.php?acao=salvar_proposta" method="POST" id="formAvaliacao">
    <input type="hidden" name="lote_id" value="<?= $lote['id'] ?>">

    <div class="card">
        <h3>Avaliar Peças</h3>
        <table>
            <thead>
                <tr>
                    <th>Categoria</th>
                    <th>Descrição</th>
                    <th>Decisão</th>
                    <th>Valor Ofertado (R$)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lote['pecas'] as $peca): ?>
                    <tr>
                        <td><?= htmlspecialchars($peca['categoria_nome']) ?></td>
                        <td><?= htmlspecialchars($peca['descricao']) ?></td>
                        <td>
                            <input type="hidden" name="pecas[<?= $peca['id'] ?>][status]" id="status_<?= $peca['id'] ?>" class="status-input" value="0">
                            
                            <button type="button" class="btn-acao btn-aprovar" id="btn_aprov_<?= $peca['id'] ?>" onclick="avaliarPeca(<?= $peca['id'] ?>, 1)">✔️ Aprovar</button>
                            <button type="button" class="btn-acao btn-reprovar" id="btn_reprov_<?= $peca['id'] ?>" onclick="avaliarPeca(<?= $peca['id'] ?>, 2)">❌ Reprovar</button>
                        </td>
                        <td>
                            <input type="number" step="0.01" min="0" name="pecas[<?= $peca['id'] ?>][valor]" id="valor_<?= $peca['id'] ?>" class="input-valor" placeholder="0.00" oninput="calcularTotal()">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="total-box">
            <strong>Valor Total da Proposta: R$ </strong>
            <input type="text" name="valor_total" id="valor_total" value="0.00" readonly style="border:none; background:transparent; font-size:20px; font-weight:bold; width:120px; outline:none;">
        </div>
        
        <button type="submit" class="btn-salvar" onclick="return validarFormulario()">Salvar Avaliação e Gerar Proposta</button>
    </div>
</form>

<script>
    function avaliarPeca(pecaId, status) {
        document.getElementById('status_' + pecaId).value = status;
        
        let btnAprov = document.getElementById('btn_aprov_' + pecaId);
        let btnReprov = document.getElementById('btn_reprov_' + pecaId);
        let inputValor = document.getElementById('valor_' + pecaId);

        if (status === 1) {
            btnAprov.classList.add('ativo');
            btnReprov.classList.remove('ativo');
            inputValor.style.display = 'block';
            inputValor.setAttribute('required', 'required');
        } else {
            btnReprov.classList.add('ativo');
            btnAprov.classList.remove('ativo');
            inputValor.style.display = 'none';
            inputValor.removeAttribute('required');
            inputValor.value = '';
        }
        
        calcularTotal();
    }

    function calcularTotal() {
        let total = 0;
        let inputsValor = document.querySelectorAll('.input-valor');
        
        inputsValor.forEach(function(input) {
            if (input.style.display === 'block' && input.value !== '') {
                total += parseFloat(input.value);
            }
        });
        
        document.getElementById('valor_total').value = total.toFixed(2);
    }

    function validarFormulario() {
        let statusInputs = document.querySelectorAll('.status-input');
        let todasAvaliadas = true;
        
        statusInputs.forEach(function(input) {
            if (input.value === "0") {
                todasAvaliadas = false;
            }
        });
        
        if (!todasAvaliadas) {
            alert("Você precisa aprovar (✔️) ou reprovar (❌) TODAS as peças antes de salvar.");
            return false;
        }
        return true;
    }
</script>

<?php require_once 'footer.php'; ?>