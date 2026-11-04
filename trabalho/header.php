<?php
require_once 'conexao.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="plagiarism" content="Trabalho Academico - Sistema de Brecho - Integridade Declarada">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestão - Brechó EcoModa</title>
    <style>
        * { box-sizing: border-box; font-family: Arial, sans-serif; margin: 0; padding: 0; }
        body { background-color: #f4f7f6; color: #333; line-height: 1.6; }
        header { background: #2c3e50; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        nav a { color: white; text-decoration: none; margin-left: 15px; font-weight: bold; }
        nav a:hover { text-decoration: underline; }
        .container { max-width: 1100px; margin: 20px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .user-badge { display: flex; align-items: center; gap: 10px; }
        .user-badge img { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; }
        footer { text-align: center; padding: 15px; background: #e9ecef; margin-top: 30px; font-size: 14px; }
    </style>
</head>
<body>
    <header>
        <h1><a href="index.php" style="color:white; text-decoration:none;">🛍️ Brechó EcoModa</a></h1>
        <nav>
            <a href="index.php">Início</a>
            <a href="vitrine.php">Vitrine Pública</a>
            <a href="fale_conosco.php">Fale Conosco</a>
            <a href="termos.php">Termos e LGPD</a>
            
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <?php if ($_SESSION['usuario_tipo'] === 'admin'): ?>
                    <a href="admin_lotes.php" style="color:#ffc107;">Painel Admin</a>
                <?php else: ?>
                    <a href="meus_lotes.php" style="color:#20c997;">Meus Lotes</a>
                <?php endif; ?>
                <a href="logout.php" style="color:#f44336;">Sair</a>
            <?php else: ?>
                <a href="login.php" style="color:#28a745;">Login / Cadastro</a>
            <?php endif; ?>
        </nav>
        
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <div class="user-badge">
                <img src="uploads/<?= $_SESSION['usuario_foto'] ?>" alt="Foto">
                <span><?= htmlspecialchars($_SESSION['usuario_nome']) ?></span>
            </div>
        <?php endif; ?>
    </header>
    <div class="container">