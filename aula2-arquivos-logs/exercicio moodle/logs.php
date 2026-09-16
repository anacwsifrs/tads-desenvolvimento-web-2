<?php
function lerLogs() {
    $arquivoLog = "logs.txt";
    if (file_exists($arquivoLog)) {
        return file_get_contents($arquivoLog);
    } else {
        return "Nenhum log registrado ainda.";
    }
}

echo "<h2>Histórico de Logs do Sistema</h2>";
echo "<div style='font-family: monospace; background: #f4f4f4; padding: 15px; border: 1px solid #ccc;'>";
echo nl2br(lerLogs());
echo "</div>";

echo "<br><a href='form.html'>Voltar para o Formuário</a> | <a href='listar.php'>Ver Arquivos</a>";
?>