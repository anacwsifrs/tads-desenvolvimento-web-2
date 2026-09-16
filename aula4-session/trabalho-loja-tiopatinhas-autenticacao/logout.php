<?php
require_once 'funcoes_log.php';
session_start();

$usuario = $_SESSION['usuario'] ?? 'Desconhecido';
registrarLog('LOGOUT', 'INFO', $usuario);

$_SESSION = array();[cite: 4]

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy(); 
header('Location: login.php');
exit;