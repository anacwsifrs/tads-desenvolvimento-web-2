<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$descricaoPeca = isset($data['descricao']) ? $data['descricao'] : '';

if (empty($descricaoPeca)) {
    echo json_encode(['erro' => 'Descrição não informada.']);
    exit;
}

$apiKey = 'SUA_CHAVE_API_GEMINI_AQUI';
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=" . $apiKey;

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

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>