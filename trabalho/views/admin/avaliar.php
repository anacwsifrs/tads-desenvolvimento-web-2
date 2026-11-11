<?php require_once 'views/include/header.php'; ?>

<style>
    body { font-family: Arial, sans-serif;}
    .card { background: #f8f9fa; padding: 15px; border-radius: 5px; border: 1px solid #ddd; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; background: white; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; vertical-align: middle; }
    th { background: #eee; }
    
    .thumb-peca { width: 65px; height: 65px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
    .badge-attr { display: inline-block; background: #e9ecef; color: #495057; font-size: 11px; padding: 2px 6px; border-radius: 3px; margin: 2px 2px 2px 0; font-weight: bold; }
    .badge-estado { background: #d1ecf1; color: #0c5460; }
    
    .btn-acao { padding: 6px 12px; border-radius: 4px; font-size: 13px; margin-right: 4px; cursor: pointer; font-weight: bold; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 4px; }
    
    .btn-aprovar { background-color: #e8f5e9; color: #28a745; border: 1px solid #28a745; }
    .btn-aprovar:hover { background-color: #28a745; color: white; }
    .btn-aprovar.ativo { background-color: #28a745; color: white; box-shadow: 0 2px 6px rgba(40,167,69,0.35); transform: scale(1.03); }

    .btn-reprovar { background-color: #ffebee; color: #dc3545; border: 1px solid #dc3545; }
    .btn-reprovar:hover { background-color: #dc3545; color: white; }
    .btn-reprovar.ativo { background-color: #dc3545; color: white; box-shadow: 0 2px 6px rgba(220,53,69,0.35); transform: scale(1.03); }

    .ia-wrapper { position: relative; display: inline-block; }
    
    .btn-ia-icon { background: linear-gradient(135deg, #6f42c1, #8a2be2); color: white; border: none; width: 34px; height: 34px; border-radius: 50%; cursor: pointer; font-size: 15px; display: inline-flex; align-items: center; justify-content: center; transition: 0.2s; box-shadow: 0 2px 5px rgba(111,66,193,0.3); }
    .btn-ia-icon:hover { transform: scale(1.1); box-shadow: 0 4px 8px rgba(111,66,193,0.4); }

    .spinner { display: inline-block; width: 14px; height: 14px; border: 2px solid rgba(255,255,255,0.4); border-top-color: #fff; border-radius: 50%; animation: spin 0.8s linear infinite; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    .ia-popover { display: none; position: absolute; right: 0; top: 40px; width: 280px; background: white; border: 1px solid #7000ff; border-radius: 8px; box-shadow: 0 4px 18px rgba(0,0,0,0.15); padding: 12px; z-index: 99; text-align: left; animation: fadeIn 0.2s; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }

    .ia-popover-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e9ecef; padding-bottom: 6px; margin-bottom: 8px; font-weight: bold; font-size: 13px; color: #6f42c1; }
    .ia-popover-close { background: none; border: none; font-size: 16px; cursor: pointer; color: #888; font-weight: bold; }
    .ia-popover-close:hover { color: #dc3545; }
    .ia-popover-body { font-size: 12px; color: #333; line-height: 1.4; max-height: 200px; overflow-y: auto; }

    .input-valor { padding: 8px; width: 110px; border: 1px solid #ccc; border-radius: 4px; }
    .total-box { font-size: 20px; text-align: right; padding: 15px; background: #e9ecef; margin-top: 15px; border-radius: 5px; }
    .btn-salvar { padding: 12px 20px; background-color: #007bff; color: white; border: none; cursor: pointer; font-size: 16px; margin-top: 10px; width: 100%; border-radius: 5px; font-weight: bold; }
</style>

<h2>Avaliação de Lote #<?= $lote['id'] ?></h2>

<div class="card">
    <p><strong>Fornecedor:</strong> <?= htmlspecialchars($lote['cliente_nome']) ?> (<?= htmlspecialchars($lote['telefone']) ?>)</p>
    <p><strong>Descrição Geral do Lote:</strong> <?= htmlspecialchars($lote['descricao']) ?></p>
</div>

<form action="index.php?acao=salvar_proposta" method="POST" id="formAvaliacao">
    <input type="hidden" name="lote_id" value="<?= $lote['id'] ?>">

    <div class="card">
        <h3>Avaliar Peças Cadastradas</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 75px;">Foto</th>
                    <th>Detalhes da Peça</th>
                    <th>Características</th>
                    <th style="width: 170px;">Decisão</th>
                    <th style="width: 220px;">Valor Ofertado (R$)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lote['pecas'] as $peca): ?>
                    <tr id="linha-peca-<?= $peca['id'] ?>" 
                        data-categoria="<?= htmlspecialchars($peca['categoria_nome'] ?? '') ?>"
                        data-marca="<?= htmlspecialchars($peca['marca'] ?? '') ?>"
                        data-tamanho="<?= htmlspecialchars($peca['tamanho'] ?? '') ?>"
                        data-cor="<?= htmlspecialchars($peca['cor'] ?? '') ?>"
                        data-estado="<?= htmlspecialchars($peca['estado'] ?? '') ?>"
                        data-descricao="<?= htmlspecialchars($peca['descricao'] ?? '') ?>"
                        data-observacoes="<?= htmlspecialchars($peca['observacoes'] ?? '') ?>">
                        
                        <td style="text-align: center;">
                            <?php if (!empty($peca['foto_caminho']) && file_exists('uploads/' . $peca['foto_caminho'])): ?>
                                <img src="uploads/<?= htmlspecialchars($peca['foto_caminho']) ?>" class="thumb-peca" alt="Foto">
                            <?php else: ?>
                                <div class="thumb-peca" style="background:#eee; display:flex; align-items:center; justify-content:center; font-size:24px;">👕</div>
                            <?php endif; ?>
                        </td>

                        <td>
                            <strong><?= htmlspecialchars($peca['categoria_nome'] ?? 'Sem Categoria') ?></strong>
                            <p style="margin: 3px 0 0 0; font-size: 13px; color: #333;"><?= htmlspecialchars($peca['descricao']) ?></p>
                            <?php if (!empty($peca['observacoes'])): ?>
                                <small style="color: #d9534f; display: block; margin-top: 3px;">Obs: <?= htmlspecialchars($peca['observacoes']) ?></small>
                            <?php endif; ?>
                        </td>

                        <td>
                            <span class="badge-attr">Marca: <?= htmlspecialchars($peca['marca'] ?: 'N/A') ?></span><br>
                            <span class="badge-attr">Tam: <?= htmlspecialchars($peca['tamanho'] ?: 'N/A') ?></span>
                            <span class="badge-attr">Cor: <?= htmlspecialchars($peca['cor'] ?: 'N/A') ?></span><br>
                            <span class="badge-attr badge-estado">Estado: <?= htmlspecialchars($peca['estado'] ?: 'N/A') ?></span>
                        </td>

                        <td>
                            <input type="hidden" name="pecas[<?= $peca['id'] ?>][status]" id="status_<?= $peca['id'] ?>" class="status-input" value="0">
                            <button type="button" class="btn-acao btn-aprovar" id="btn_aprov_<?= $peca['id'] ?>" onclick="avaliarPeca(<?= $peca['id'] ?>, 1)">✔️ Aprovar</button>
                            <button type="button" class="btn-acao btn-reprovar" id="btn_reprov_<?= $peca['id'] ?>" onclick="avaliarPeca(<?= $peca['id'] ?>, 2)">❌ Reprovar</button>
                        </td>

                        <td>
                            <div style="display: flex; gap: 8px; align-items: center; position: relative;">
                                <div id="box-valor-<?= $peca['id'] ?>" style="display: none;">
                                    <input type="number" step="0.01" min="0" name="pecas[<?= $peca['id'] ?>][valor]" id="valor_<?= $peca['id'] ?>" class="input-valor" placeholder="0.00" oninput="calcularTotal()">
                                </div>
                                
                                <small id="txt-reprovado-<?= $peca['id'] ?>" style="color: #888;">Pendente</small>

                                <div class="ia-wrapper">
                                    <button type="button" id="btn-ia-<?= $peca['id'] ?>" onclick="handleCliqueIA(<?= $peca['id'] ?>)" class="btn-ia-icon" title="Sugestão IA">
                                        ✨
                                    </button>

                                    <div class="ia-popover" id="popover-ia-<?= $peca['id'] ?>">
                                        <div class="ia-popover-header">
                                            <span>✨ Sugestão da IA</span>
                                            <button type="button" class="ia-popover-close" onclick="fecharIA(<?= $peca['id'] ?>)">×</button>
                                        </div>
                                        <div class="ia-popover-body" id="conteudo-ia-<?= $peca['id'] ?>"></div>
                                    </div>
                                </div>
                            </div>
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
    const memoriaRespostaIA = {};

    function avaliarPeca(pecaId, status) {
        document.getElementById('status_' + pecaId).value = status;
        
        let btnAprov = document.getElementById('btn_aprov_' + pecaId);
        let btnReprov = document.getElementById('btn_reprov_' + pecaId);
        let boxValor = document.getElementById('box-valor-' + pecaId);
        let inputValor = document.getElementById('valor_' + pecaId);
        let txtReprovado = document.getElementById('txt-reprovado-' + pecaId);

        if (status === 1) {
            btnAprov.classList.add('ativo');
            btnReprov.classList.remove('ativo');
            boxValor.style.display = 'block';
            txtReprovado.style.display = 'none';
            inputValor.setAttribute('required', 'required');
        } else {
            btnReprov.classList.add('ativo');
            btnAprov.classList.remove('ativo');
            boxValor.style.display = 'none';
            txtReprovado.style.display = 'inline';
            txtReprovado.innerText = 'Reprovado';
            inputValor.removeAttribute('required');
            inputValor.value = '';
        }
        
        calcularTotal();
    }

    function calcularTotal() {
        let total = 0;
        let inputsValor = document.querySelectorAll('.input-valor');
        
        inputsValor.forEach(function(input) {
            if (input.parentElement.style.display === 'block' && input.value !== '') {
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

    function handleCliqueIA(idPeca) {
        const popover = document.getElementById('popover-ia-' + idPeca);

        if (popover.style.display === 'block') {
            fecharIA(idPeca);
            return;
        }

        if (memoriaRespostaIA[idPeca]) {
            abrirIA(idPeca, memoriaRespostaIA[idPeca]);
            return;
        }

        pedirSugestaoIA(idPeca);
    }

    function fecharIA(idPeca) {
        document.getElementById('popover-ia-' + idPeca).style.display = 'none';
    }

    function abrirIA(idPeca, htmlTexto) {
        document.querySelectorAll('.ia-popover').forEach(el => el.style.display = 'none');

        const bodyDiv = document.getElementById('conteudo-ia-' + idPeca);
        const popover = document.getElementById('popover-ia-' + idPeca);
        
        bodyDiv.innerHTML = htmlTexto;
        popover.style.display = 'block';
    }

    function pedirSugestaoIA(idPeca) {
        const tr = document.getElementById('linha-peca-' + idPeca);
        const btnIA = document.getElementById('btn-ia-' + idPeca);
        
        const payload = {
            categoria: tr.dataset.categoria,
            marca: tr.dataset.marca,
            tamanho: tr.dataset.tamanho,
            cor: tr.dataset.cor,
            estado: tr.dataset.estado,
            descricao: tr.dataset.descricao,
            observacoes: tr.dataset.observacoes
        };
        
        btnIA.disabled = true;
        btnIA.innerHTML = "<span class='spinner'></span>";

        fetch('index.php?acao=sugestao_ia', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            btnIA.disabled = false;
            btnIA.innerHTML = "✨";

            if (data.error && (data.error.code === 503 || data.error.status === 'UNAVAILABLE')) {
                const msgErro = "<span style='color:#dc3545;'>⚠️ A IA está com alta demanda no momento (Erro 503). Por favor, aguarde alguns instantes e tente novamente.</span>";
                abrirIA(idPeca, msgErro);
                return;
            }

            if (data.erro || data.error) {
                const erroTexto = data.erro || data.error.message || "Erro inesperado ao consultar a IA.";
                abrirIA(idPeca, "<span style='color:#dc3545;'>Erro: " + erroTexto + "</span>");
                return;
            }

            if (data.candidates && data.candidates.length > 0) {
                const textoIA = data.candidates[0].content.parts[0].text;
                memoriaRespostaIA[idPeca] = textoIA; 
                abrirIA(idPeca, textoIA);
            } else {
                abrirIA(idPeca, "A IA não retornou sugestões para esta peça.");
            }
        })
        .catch(error => {
            btnIA.disabled = false;
            btnIA.innerHTML = "✨";
            abrirIA(idPeca, "<span style='color:#dc3545;'>Erro de comunicação com o servidor. Tente novamente.</span>");
        });
    }
</script>

<?php require_once 'views/include/footer.php'; ?>