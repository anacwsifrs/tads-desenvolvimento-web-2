<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Lotes - Brechó</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Lotes de Avaliação</h2>
        <a href="index.php?acao=novo" class="btn btn-success">Cadastrar Lote</a>
    </div>
    
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Código</th>
                <th>Status</th>
                <th>Peças do Lote</th>
                <th style="width: 200px;">Ações</th>
            </tr>
        </thead>
        <tbody id="tabela-lotes">
            <tr>
                <td colspan="5" class="text-center text-muted">Carregando dados...</td>
            </tr>
        </tbody>
    </table>

    <div class="card mt-4 d-none" id="card-detalhes">
        <div class="card-header bg-primary text-white">
            Detalhes do Lote
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>ID:</strong> <span id="detalhe-id"></span></p>
                    <p><strong>Código:</strong> <span id="detalhe-codigo"></span></p>
                    <p><strong>Chave PIX:</strong> <span id="detalhe-pix"></span></p>
                    <p><strong>Status Atual:</strong> <span id="detalhe-status"></span></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Valor em PIX:</strong> R$ <span id="detalhe-valor-pix"></span></p>
                    <p><strong>Valor em Crédito (+20%):</strong> R$ <span id="detalhe-valor-credito"></span></p>
                    <p><strong>Forma Escolhida:</strong> <span id="detalhe-tipo"></span></p>
                    <p><strong>Comprovante:</strong> <span id="detalhe-comprovante"></span></p>
                </div>
            </div>
            <hr>
            <p><strong>Categorias de Roupas:</strong> <span id="detalhe-categorias"></span></p>
        </div>
    </div>

    <script>
        function carregarLotes() {
            fetch('index.php?acao=api_listar')
                .then(res => res.json())
                .then(lotes => {
                    const tbody = document.getElementById('tabela-lotes');
                    tbody.innerHTML = '';
                    if (lotes.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center">Nenhum lote cadastrado.</td></tr>';
                        return;
                    }
                    lotes.forEach(l => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${l.id}</td>
                            <td><strong>${l.codigo}</strong></td>
                            <td><span class="badge bg-info text-dark">${l.status}</span></td>
                            <td>${l.categorias_nomes ? l.categorias_nomes : 'Nenhuma categoria'}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="carregarDetalhes(${l.id})">Visualizar</button>
                                <a href="index.php?acao=editar&id=${l.id}" class="btn btn-sm btn-outline-secondary">Editar</a>
                                <a href="index.php?acao=deletar&id=${l.id}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deseja excluir este lote?')">Excluir</a>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                });
        }

        function carregarDetalhes(id) {
            fetch('index.php?acao=api_detalhar&id=' + id)
                .then(res => res.json())
                .then(lote => {
                    document.getElementById('detalhe-id').textContent = lote.id;
                    document.getElementById('detalhe-codigo').textContent = lote.codigo;
                    document.getElementById('detalhe-pix').textContent = lote.chave_pix;
                    document.getElementById('detalhe-status').textContent = lote.status;
                    
                    const valPix = parseFloat(lote.valor_proposta || 0).toFixed(2).replace('.', ',');
                    const valCred = parseFloat(lote.valor_credito || 0).toFixed(2).replace('.', ',');
                    
                    document.getElementById('detalhe-valor-pix').textContent = valPix;
                    document.getElementById('detalhe-valor-credito').textContent = valCred;
                    document.getElementById('detalhe-tipo').textContent = lote.tipo_recebimento === 'credito_loja' ? 'Crédito na Loja' : 'PIX';
                    document.getElementById('detalhe-categorias').textContent = lote.categorias_nomes ? lote.categorias_nomes : 'Nenhuma';
                    
                    const compElem = document.getElementById('detalhe-comprovante');
                    if (lote.comprovante_pix) {
                        compElem.innerHTML = `<a href="${lote.comprovante_pix}" target="_blank">Visualizar Anexo</a>`;
                    } else {
                        compElem.textContent = 'Pendente';
                    }

                    document.getElementById('card-detalhes').classList.remove('d-none');
                });
        }

        carregarLotes();
    </script>
</body>
</html>