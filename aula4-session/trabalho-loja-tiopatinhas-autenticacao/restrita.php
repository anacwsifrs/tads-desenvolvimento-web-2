<?php
require_once 'funcoes_log.php';
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
    <html lang="pt-br">
        <head>
            <meta charset="UTF-8">
            <title>Cofre Forte - Área Restrita</title>
        </head>
        <body>
            <h2>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario']); ?>!</h2>
            <p>Você está acessando o sistema seguro da Loja do Tio Patinhas.</p>
            <a href="logout.php">Encerrar Sessão</a>
        </body>
    </html>