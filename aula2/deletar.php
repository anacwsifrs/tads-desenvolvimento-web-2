<?php
if (isset($_REQUEST["arquivo"])) {
    $arquivo = "uploads/" . $_REQUEST["arquivo"];
    if (file_exists($arquivo)) {
        unlink($arquivo);
        echo "Arquivo deletado com sucesso!";
    } else {
        echo "Erro: Arquivo não encontrado.";
    }
}
?>