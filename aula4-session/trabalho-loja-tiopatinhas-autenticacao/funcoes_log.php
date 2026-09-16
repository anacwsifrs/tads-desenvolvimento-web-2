<?php
function registrarLog($acao, $nivel = 'INFO', $usuario = 'N/A') {
    $arquivoLog = __DIR__ . '/acessos.log';
    
    $dataHora = date('c'); 
    
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconhecido';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido';
    $reqId = uniqid('req_', true); 

    $mensagem = sprintf(
        "[%s] [%s] [%s] AÇÃO: %s | USER: %s | IP: %s | UA: %s" . PHP_EOL,
        $dataHora, $nivel, $reqId, $acao, $usuario, $ip, $userAgent
    );

    file_put_contents($arquivoLog, $mensagem, FILE_APPEND);
}