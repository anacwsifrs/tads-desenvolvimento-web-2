<?php
header('Content-Type: application/json');

function carregarEnv($caminho) {
    if (!file_exists($caminho)) {
        return false;
    }
    $linhas = file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($linhas as $linha) {
        if (strpos(trim($linha), '#') === 0) continue; 
        list($nome, $valor) = explode('=', $linha, 2);
        $_ENV[trim($nome)] = trim($valor);
    }
    return true;
}

carregarEnv(__DIR__ . '/../.env');
$apiKey = $_ENV['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY');

if (!$apiKey) {
    echo json_encode(['erro' => 'Chave de API do Gemini não configurada no ficheiro .env.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$descricaoPeca = isset($data['descricao']) ? $data['descricao'] : '';

if (empty($descricaoPeca)) {
    echo json_encode(['erro' => 'Descrição não informada.']);
    exit;
}

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-lite-latest:generateContent?key=" . $apiKey;

$prompt = "Você é um avaliador especialista em brechós de moda. Analise a seguinte peça: '$descricaoPeca'. Sugira um valor em Reais (R$) para compra no brechó e faça um comentário curto sobre o apelo comercial dela. Responda em texto simples e conciso.";

$payload = [
    "contents" => [
        [
            "parts" => [
                ["text" => $prompt]
            ]
        ]
    ]
];

$opcoes = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($payload),
        'ignore_errors' => true // Permite capturar a resposta mesmo se der erro 400/500
    ]
];

$contexto = stream_context_create($opcoes);
$response = file_get_contents($url, false, $contexto);

if ($response === FALSE) {
    echo json_encode(['erro' => 'Falha ao conectar com o Google Gemini.']);
    exit;
}

echo $response;
?>