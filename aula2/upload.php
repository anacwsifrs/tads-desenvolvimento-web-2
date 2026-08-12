<?php
$diretorioDestino = "uploads/";
if (!is_dir($diretorioDestino)) {
    mkdir($diretorioDestino, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["arquivo"])) {
    $arquivo = $_FILES["arquivo"];
    $nomeArquivo = basename($arquivo["name"]);
    $caminhoFinal = $diretorioDestino . $nomeArquivo;
    
    $extensao = strtolower(pathinfo($caminhoFinal, PATHINFO_EXTENSION));

    $tamanhoMaximo = 2 * 1024 * 1024; // 2MB
    $extensoesPermitidas = ["jpg", "png", "pdf", "txt"];

    if ($arquivo["size"] > $tamanhoMaximo) {
        die("Erro: O arquivo é muito grande. Máximo permitido: 2MB.");
    }

    if (!in_array($extensao, $extensoesPermitidas)) {
        die("Erro: Tipo de arquivo não permitido.");
    }

    if (move_uploaded_file($arquivo["tmp_name"], $caminhoFinal)) {
        echo "Arquivo enviado com sucesso: <a href='$caminhoFinal'>$nomeArquivo</a>";
    } else {
        echo "Erro ao enviar o arquivo.";
    }
} else {
    echo "Nenhum arquivo enviado.";
}
?>