<?php
function escreverLog($mensagem) {
    $arquivoLog = "logs.txt";
    $dataHora = date("Y-m-d H:i:s");
    $entradaLog = "[$dataHora] - $mensagem" . PHP_EOL;
    file_put_contents($arquivoLog, $entradaLog, FILE_APPEND);
}

if (isset($_REQUEST["arquivo"])) {
    $nomeArquivo = basename($_REQUEST["arquivo"]);
    $caminhoArquivo = "uploads/" . $nomeArquivo;

    if (file_exists($caminhoArquivo)) {
        if (unlink($caminhoArquivo)) {
            escreverLog("Exclusão: Arquivo '$nomeArquivo' foi removido.");
        } else {
            escreverLog("Erro: Falha ao tentar excluir o arquivo '$nomeArquivo'.");
        }
    } else {
        escreverLog("Erro: Tentativa de excluir arquivo inexistente '$nomeArquivo'.");
    }
}

header("Location: listar.php");
exit;
?>