<?php
if (isset($_REQUEST["arquivo"])) {
    $nomeArquivo = basename($_REQUEST["arquivo"]);
    $caminhoArquivo = "uploads/" . $nomeArquivo;

    if (file_exists($caminhoArquivo)) {
        unlink($caminhoArquivo);
    }
}

// Redireciona de volta para a listagem imediatamente
header("Location: listar.php");
exit;
?>