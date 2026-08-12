<?php
function escreverLog($mensagem) {
    $arquivoLog = "logs.txt";
    $dataHora = date("Y-m-d H:i:s");
    $entradaLog = "[$dataHora] - $mensagem" . PHP_EOL;
    file_put_contents($arquivoLog, $entradaLog, FILE_APPEND);
}

function lerLogs() {
    $arquivoLog = "logs.txt";
    if (file_exists($arquivoLog)) {
        return file_get_contents($arquivoLog);
    } else {
        return "Nenhum log encontrado.";
    }
}

escreverLog("Usuário fez login.");
echo nl2br(lerLogs());
?>