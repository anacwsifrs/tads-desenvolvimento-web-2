<?php require_once 'views/include/header.php'; ?>

<style>
    .form-container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; font-weight: bold; margin-bottom: 5px; }
    .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
    .form-group input[readonly] { background-color: #e9ecef; cursor: not-allowed; }
    .btn-salvar { background: #28a745; color: white; border: none; padding: 10px 15px; font-size: 16px; font-weight: bold; border-radius: 4px; cursor: pointer; width: 100%; }
    .btn-voltar { display: inline-block; margin-bottom: 15px; text-decoration: none; color: #007bff; font-weight: bold; }
</style>

<a href="index.php?acao=clientes" class="btn-voltar">← Voltar para Usuários</a>

<div class="form-container">
    <h2><?= $readonly ? '👁️ Dados do Cliente' : '✏️ Editar Cliente' ?></h2>
    <hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #eee;">

    <form action="index.php?acao=salvar_cliente_admin" method="POST">
        <input type="hidden" name="id" value="<?= $cliente['id'] ?>">

        <div class="form-group">
            <label>CPF:</label>
            <input type="text" value="<?= htmlspecialchars($cliente['cpf']) ?>" readonly>
        </div>

        <div class="form-group">
            <label>E-mail:</label>
            <input type="email" value="<?= htmlspecialchars($cliente['email']) ?>" readonly>
        </div>

        <div class="form-group">
            <label>Nome Completo:</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($cliente['nome']) ?>" <?= $readonly ? 'readonly' : 'required' ?>>
        </div>

        <div class="form-group">
            <label>WhatsApp:</label>
            <input type="text" name="telefone" value="<?= htmlspecialchars($cliente['telefone']) ?>" <?= $readonly ? 'readonly' : 'required' ?>>
        </div>

        <div class="form-group">
            <label>Chave PIX:</label>
            <input type="text" name="pix" value="<?= htmlspecialchars($cliente['chave_pix'] ?? '') ?>" <?= $readonly ? 'readonly' : '' ?>>
        </div>

        <div class="form-group">
            <label>Saldo de Crédito (R$):</label>
            <input type="number" step="0.01" min="0" name="saldo_credito" value="<?= htmlspecialchars($cliente['saldo_credito']) ?>" <?= $readonly ? 'readonly' : 'required' ?>>
        </div>

        <?php if (!$readonly): ?>
            <button type="submit" class="btn-salvar">Salvar Alterações</button>
        <?php endif; ?>
    </form>
</div>

<?php require_once 'views/include/footer.php'; ?>