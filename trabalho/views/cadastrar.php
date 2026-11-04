<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Novo Lote - Brechó</title>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], select { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn-salvar { padding: 12px 20px; background-color: #28a745; color: white; border: none; cursor: pointer; width: 100%; font-size: 16px; margin-top: 20px; }
        .btn-add { padding: 8px 12px; background-color: #007bff; color: white; border: none; cursor: pointer; margin-bottom: 15px; }
        .peca-item { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; background: #f8f9fa; padding: 10px; border-radius: 5px; border: 1px solid #ddd; }
        .peca-item > div { flex: 1; }
        .btn-remover { background: #dc3545; color: white; border: none; padding: 8px; cursor: pointer; border-radius: 3px; }
        .aviso-cliente { font-size: 13px; color: #0056b3; margin-top: 3px; display: none; }
    </style>
</head>
<body>
    <h2>Entrada de Mercadoria (Novo Lote)</h2>
    
    <form action="index.php?acao=salvar" method="POST">
        <h3>1. Dados do Fornecedor</h3>
        <div class="form-group">
            <label>CPF do Cliente:</label>
            <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" required>
            <div id="aviso-cliente" class="aviso-cliente">✓ Cliente encontrado! Dados carregados automaticamente.</div>
        </div>
        <div class="form-group"><label>Nome Completo:</label><input type="text" id="nome" name="nome" required></div>
        <div class="form-group"><label>WhatsApp:</label><input type="text" id="telefone" name="telefone" required></div>
        <div class="form-group"><label>Chave PIX:</label><input type="text" id="pix" name="pix" required></div>

        <h3>2. Resumo do Lote</h3>
        <div class="form-group">
            <label>Identificação Física (Ex: Sacola preta, mala azul):</label>
            <input type="text" name="descricao_lote" required>
        </div>
        
        <h3>3. Peças do Lote</h3>
        <button type="button" class="btn-add" id="btn-add-peca">+ Adicionar Peça</button>
        
        <div id="lista-pecas">
            <div class="peca-item">
                <div>
                    <label>Tipo de Peça:</label>
                    <select name="peca_categoria[]" class="select-busca" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= $cat['nome'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Descrição (Opcional):</label>
                    <input type="text" name="peca_descricao[]" placeholder="Ex: preta beatles tamanho GG">
                </div>
                <button type="button" class="btn-remover" onclick="removerPeca(this)">X</button>
            </div>
        </div>

        <button type="submit" class="btn-salvar">Finalizar Cadastro de Lote</button>
    </form>

    <script>
        $(document).ready(function() {
            $('.select-busca').select2({ width: '100%' });
            $('#cpf').on('blur', function() {
                let cpf = $(this).val().trim();
                if (cpf !== '') {
                    $.getJSON('index.php?acao=buscar_cliente&cpf=' + encodeURIComponent(cpf), function(data) {
                        if (data && data.id) {
                            $('#nome').val(data.nome);
                            $('#telefone').val(data.telefone);
                            $('#pix').val(data.chave_pix);
                            $('#aviso-cliente').fadeIn();
                        } else {
                            $('#aviso-cliente').fadeOut();
                        }
                    });
                }
            });
        });

        $('#btn-add-peca').click(function() {
            $('.select-busca').select2('destroy');
            let novaPeca = $('.peca-item:first').clone();
            novaPeca.find('input').val('');
            novaPeca.find('select').val('');
            $('#lista-pecas').append(novaPeca);
            $('.select-busca').select2({ width: '100%' });
        });

        function removerPeca(botao) {
            if ($('.peca-item').length > 1) {
                $(botao).closest('.peca-item').find('.select-busca').select2('destroy');
                $(botao).closest('.peca-item').remove();
            } else {
                alert('O lote precisa ter pelo menos uma peça.');
            }
        }
    </script>
</body>
</html>