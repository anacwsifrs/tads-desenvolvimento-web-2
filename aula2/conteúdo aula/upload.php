<?php
$diretorioDestino = "uploads/";
if (!is_dir($diretorioDestino)) {
    mkdir($diretorioDestino, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["arquivo"])) {
    $arquivo = $_FILES["arquivo"];
    
    $extensao = strtolower(pathinfo($arquivo["name"], PATHINFO_EXTENSION));

    $hashConteudo = md5_file($arquivo["tmp_name"]);
    $nomeNovo = $hashConteudo . "." . $extensao;
    $caminhoFinal = $diretorioDestino . $nomeNovo;

    $tamanhoMaximo = 2 * 1024 * 1024; 
    $extensoesPermitidas = ["jpg", "png", "pdf", "txt"];

    if ($arquivo["size"] > $tamanhoMaximo) {
        die("Erro: O arquivo é muito grande. Máximo permitido: 2MB.");
    }

    if (!in_array($extensao, $extensoesPermitidas)) {
        die("Erro: Tipo de arquivo não permitido.");
    }

    if (file_exists($caminhoFinal)) {
        echo "Este arquivo já foi enviado anteriormente e já existe na pasta!<br><br>";
        echo "<a href='listar.php'>Ver Lista de Arquivos</a> | <a href='form.html'>Voltar</a>";
    } else {
        if (move_uploaded_file($arquivo["tmp_name"], $caminhoFinal)) {
            echo "Arquivo enviado com sucesso!<br><br>";
            echo "<a href='listar.php'>Ver Lista de Arquivos</a> | <a href='form.html'>Enviar Outro Arquivo</a>";
        } else {
            echo "Erro ao enviar o arquivo.";
        }
    }
} else {
    echo "Nenhum arquivo enviado.";
}
?>