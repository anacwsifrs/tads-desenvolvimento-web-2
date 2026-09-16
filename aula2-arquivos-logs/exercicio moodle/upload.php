<?php
function escreverLog($mensagem) {
    $arquivoLog = "logs.txt";
    $dataHora = date("Y-m-d H:i:s");
    $entradaLog = "[$dataHora] - $mensagem" . PHP_EOL;
    file_put_contents($arquivoLog, $entradaLog, FILE_APPEND);
}

$diretorioDestino = "uploads/";
if (!is_dir($diretorioDestino)) {
    mkdir($diretorioDestino, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["arquivo"])) {
    $arquivo = $_FILES["arquivo"];
    $nomeOriginal = $arquivo["name"];
    
    $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

    $hashConteudo = md5_file($arquivo["tmp_name"]);
    $nomeNovo = $hashConteudo . "." . $extensao;
    $caminhoFinal = $diretorioDestino . $nomeNovo;

    $tamanhoMaximo = 14 * 1024 * 1024; 
    $extensoesPermitidas = ["jpg", "jpeg", "png", "gif", "pdf", "doc", "docx", "xls", "xlsx", "ods", "odt", "txt"];

    if ($arquivo["size"] > $tamanhoMaximo) {
        escreverLog("Erro: Tentativa de upload do arquivo '$nomeOriginal' excede o limite de 14MB.");
        die("Erro: O arquivo é muito grande. Máximo permitido: 14MB.");
    }

    if (!in_array($extensao, $extensoesPermitidas)) {
        escreverLog("Erro: Tentativa de upload do arquivo '$nomeOriginal' com extensão não permitida (.$extensao).");
        die("Erro: Tipo de arquivo não permitido.");
    }

    if (file_exists($caminhoFinal)) {
        escreverLog("Aviso: O arquivo '$nomeOriginal' (Hash: $nomeNovo) já existe no servidor.");
        echo "Este arquivo já foi enviado anteriormente e já existe na pasta!<br><br>";
        echo "<a href='listar.php'>Ver Lista de Arquivos</a> | <a href='form.html'>Voltar</a>";
    } else {
        if (move_uploaded_file($arquivo["tmp_name"], $caminhoFinal)) {
            escreverLog("Sucesso: Arquivo '$nomeOriginal' enviado e salvo como '$nomeNovo'.");
            echo "Arquivo enviado com sucesso!<br><br>";
            echo "<a href='listar.php'>Ver Lista de Arquivos</a> | <a href='form.html'>Enviar Outro Arquivo</a>";
        } else {
            escreverLog("Erro: Falha ao mover o arquivo '$nomeOriginal' para a pasta de destino.");
            echo "Erro ao enviar o arquivo.";
        }
    }
} else {
    echo "Nenhum arquivo enviado.";
}
?>