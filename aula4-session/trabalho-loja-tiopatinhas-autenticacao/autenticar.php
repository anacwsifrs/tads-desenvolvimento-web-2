<?php
require_once 'funcoes_log.php';

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => true, 
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();

$usuarios_validos = [
    'patinhas' => 'ouro123',
    'donald' => 'marinha123'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (isset($usuarios_validos[$usuario]) && $usuarios_validos[$usuario] === $senha) {
        session_regenerate_id(true); 

        $_SESSION['logado'] = true;
        $_SESSION['usuario'] = $usuario;

        registrarLog('LOGIN_SUCCESS', 'INFO', $usuario);

        header('Location: restrita.php');
        exit;
    } else {
        registrarLog('LOGIN_FAILED', 'SECURITY', $usuario);
        echo "Acesso negado! <a href='login.php'>Tentar novamente</a>";
    }
}