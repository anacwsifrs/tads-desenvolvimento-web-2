<?php require_once 'header.php'; ?>

<style>
    body { font-family: Arial, sans-serif; padding: 20px; max-width: 1200px; margin: auto; background-color: #f8f9fa; }
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .nav-btns { display: flex; gap: 10px; }
    .btn-menu { padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
    .btn-novo { background-color: #28a745; }
    
    table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    th, td { padding: 10px; border: 1px solid #dee2e6; text-align: left; vertical-align: middle; }
    th { background-color: #e9ecef; }
    
    .badge { padding: 5px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; color: white; display: inline-block; }
    .status-1 { background-color: #6c757d; }
    .status-2 { background-color: #17a2b8; }
    .status-3 { background-color: #ffc107; color: #212529; }
    .status-4 { background-color: #28a745; }
    .status-5 { background-color: #20c997; }
    .status-6 { background-color: #dc3545; }
    .status-7 { background-color: #343a40; }

    .btn-acao { padding: 5px 8px; text-decoration: none; border-radius: 3px; font-size: 12px; margin-right: 2px; color: white; }
    .btn-avaliar { background-color: #007bff; }
    .btn-editar { background-color: #ffc107; color: #212529; }
    .btn-excluir { background-color: #dc3545; }
    .btn-token { background-color: #17a2b8; cursor: pointer; border: none; padding: 5px 8px; border-radius: 3px; color: white; }
    
    .upload-form { display: flex; gap: 5px; align-items: center; }
    .upload-form input[type="file"] { font-size: 11px; width: 140px; }
    .btn-upload { background: #28a745; color: white; border: none; padding: 4px 8px; border-radius: 3px; cursor: pointer; font-size: 11px; }
    .link-comp { color: #007bff; font-weight: bold; text-decoration: underline; font-size: 13px; }
</style>
<div class="header">
    <h2>Painel Geral - Lotes de Avaliação</h2>
    <div class="nav-btns">
        <a href="index.php?acao=clientes" class="btn-menu">💳 Ver Saldo dos Clientes</a>
        <a href="index.php?acao=cadastrar" class="btn-menu btn-novo">+ Registrar Novo Lote</a>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>#ID</th>
            <th>Cliente</th>
            <th>Telefone</th>
            <th>Descrição do Lote</th>
            <th>Status</th>
            <th>Proposta</th>
            <th>Comprovante PIX</th>
            <th>Link Cliente</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($lotes)): ?>
            <tr><td colspan="9" style="text-align:center;">Nenhum lote cadastrado.</td></tr>
        <?php else: ?>
            <?php 
            $statusMap = [
                1 => 'Aguardando Triagem',
                2 => 'Em Avaliação',
                3 => 'Aguardando Resposta',
                4 => 'Aceito (PIX)',
                5 => 'Aceito (Crédito)',
                6 => 'Recusado',
                7 => 'Doado'
            ];
            foreach ($lotes as $l): 
                $linkCliente = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/index.php?acao=proposta&token=" . $l['token'];
            ?>
                <tr>
                    <td><strong>#<?= $l['id'] ?></strong></td>
                    <td><?= htmlspecialchars($l['cliente_nome']) ?></td>
                    <td><?= htmlspecialchars($l['telefone']) ?></td>
                    <td><?= htmlspecialchars($l['descricao']) ?></td>
                    <td>
                        <span class="badge status-<?= $l['status'] ?>">
                            <?= $statusMap[$l['status']] ?>
                        </span>
                    </td>
                    <td>
                        <?= $l['valor_proposta'] !== null ? 'R$ ' . number_format($l['valor_proposta'], 2, ',', '.') : '-' ?>
                    </td>
                    <td>
                        <?php if ($l['status'] == 4):?>
                            <?php if ($l['comprovante']): ?>
                                <a href="uploads/<?= $l['comprovante'] ?>" target="_blank" class="link-comp">📄 Ver Anexo</a>
                            <?php else: ?>
                                <form action="index.php?acao=upload_comprovante" method="POST" enctype="multipart/form-data" class="upload-form">
                                    <input type="hidden" name="lote_id" value="<?= $l['id'] ?>">
                                    <input type="file" name="comprovante" accept="image/*,.pdf" required>
                                    <button type="submit" class="btn-upload">Enviar</button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <small style="color:#888;">N/A</small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($l['token']): ?>
                            <button class="btn-token" onclick="copiarLink('<?= $linkCliente ?>')">📋 Copiar Link</button>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="index.php?acao=avaliar&id=<?= $l['id'] ?>" class="btn-acao btn-avaliar">🔍 Avaliar</a>
                        <a href="index.php?acao=editar&id=<?= $l['id'] ?>" class="btn-acao btn-editar">✏️ Editar</a>
                        <a href="index.php?acao=excluir&id=<?= $l['id'] ?>" class="btn-acao btn-excluir" onclick="return confirm('Excluir este lote?')">🗑️</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<script>
    function copiarLink(url) {
        navigator.clipboard.writeText(url);
        alert("Link copiado!\n\n" + url);
    }
</script>

<?php require_once 'footer.php'; ?>