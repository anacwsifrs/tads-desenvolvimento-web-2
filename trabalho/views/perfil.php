<?php require_once 'header.php'; ?>

<style>
    .tabs { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
    .tab-btn { padding: 10px 20px; border: none; background: #e9ecef; cursor: pointer; font-weight: bold; border-radius: 5px; transition: 0.3s; }
    .tab-btn.active { background: #17a2b8; color: white; }
    .tab-content { display: none; background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
    .tab-content.active { display: block; animation: fadeIn 0.4s; }
    
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; font-weight: bold; margin-bottom: 5px; }
    .form-group input { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
    .btn-submit { padding: 10px 15px; background: #28a745; color: white; border: none; cursor: pointer; border-radius: 4px; font-weight: bold; }
    
    .msg-feedback { display: none; padding: 10px; margin-bottom: 15px; border-radius: 4px; font-weight: bold; text-align: center; }
    .msg-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .msg-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>

<h2>Meu Perfil</h2>

<div class="tabs">
    <button class="tab-btn active" onclick="mudarAba('dados', this)">Meus Dados</button>
    <button class="tab-btn" onclick="mudarAba('senha', this)">Segurança</button>
</div>

<div id="aba-dados" class="tab-content active">
    <h3>Informações Pessoais</h3>
    <div id="feedback-dados" class="msg-feedback"></div>
    
    <form id="form-perfil">
        <div class="form-group">
            <label>E-mail (Login):</label>
            <input type="email" value="<?= htmlspecialchars($usuario['email']) ?>" readonly style="background: #e9ecef; cursor: not-allowed;">
            <small style="color: #666;">O e-mail não pode ser alterado.</small>
        </div>
        <div class="form-group">
            <label>Nome Completo:</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
        </div>
        <div class="form-group">
            <label>WhatsApp:</label>
            <input type="text" name="telefone" value="<?= htmlspecialchars($usuario['telefone']) ?>" required>
        </div>
        <div class="form-group">
            <label>Chave PIX:</label>
            <input type="text" name="pix" value="<?= htmlspecialchars($usuario['chave_pix'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Nova Foto de Perfil (Opcional):</label>
            <input type="file" name="foto" accept="image/*">
        </div>
        <button type="submit" class="btn-submit">Salvar Alterações</button>
    </form>
</div>

<div id="aba-senha" class="tab-content">
    <h3>Redefinir Senha</h3>
    <div id="feedback-senha" class="msg-feedback"></div>
    
    <form id="form-senha">
        <div class="form-group">
            <label>Senha Atual:</label>
            <input type="password" name="senha_atual" required>
        </div>
        <div class="form-group">
            <label>Nova Senha:</label>
            <input type="password" name="nova_senha" required minlength="6">
        </div>
        <button type="submit" class="btn-submit" style="background: #007bff;">Atualizar Senha</button>
    </form>
</div>

<script>
    function mudarAba(abaId, botao) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        
        document.getElementById('aba-' + abaId).classList.add('active');
        botao.classList.add('active');
        
        document.querySelectorAll('.msg-feedback').forEach(el => el.style.display = 'none');
    }

    function mostrarMensagem(divId, sucesso, texto) {
        const div = document.getElementById(divId);
        div.className = 'msg-feedback ' + (sucesso ? 'msg-success' : 'msg-error');
        div.innerText = texto;
        div.style.display = 'block';
    }

    document.getElementById('form-perfil').addEventListener('submit', function(e) {
        e.preventDefault(); 
        let formData = new FormData(this);

        fetch('index.php?acao=api_atualizar_perfil', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => mostrarMensagem('feedback-dados', data.sucesso, data.mensagem))
        .catch(error => mostrarMensagem('feedback-dados', false, 'Erro de comunicação com o servidor.'));
    });

    document.getElementById('form-senha').addEventListener('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        fetch('index.php?acao=api_atualizar_senha', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            mostrarMensagem('feedback-senha', data.sucesso, data.mensagem);
            if(data.sucesso) this.reset();
        })
        .catch(error => mostrarMensagem('feedback-senha', false, 'Erro de comunicação com o servidor.'));
    });
</script>

<?php require_once 'footer.php'; ?>