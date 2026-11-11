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
        .user-badge { display: flex; align-items: center; gap: 10px; text-decoration: none; color: white; transition: opacity 0.2s; }
        .user-badge:hover { opacity: 0.85; text-decoration: underline; }
        .user-badge img { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; }
        footer { text-align: center; padding: 15px; background: #e9ecef; margin-top: 30px; font-size: 14px; }
    </style>
</head>
<body>
    <header>
        <h1><a href="index.php?acao=inicio" style="color:white; text-decoration:none;">🛍️ Brechó EcoModa</a></h1>
        <nav>
            <a href="index.php?acao=inicio">Início</a>
            <a href="index.php?acao=vitrine">Vitrine</a>
            <a href="index.php?acao=fale_conosco">Fale Conosco</a>
            <a href="index.php?acao=termos">Termos e LGPD</a>
            
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <?php if ($_SESSION['usuario_tipo'] === 'admin'): ?>
                    <a href="index.php?acao=listar" style="color:#ffc107;">Painel Admin</a>
                <?php else: ?>
                    <a href="index.php?acao=meus_lotes" style="color:#20c997;">Meus Lotes</a>
                <?php endif; ?>
                <a href="index.php?acao=logout" style="color:#f44336;">Sair</a>
            <?php else: ?>
                <a href="index.php?acao=login" style="color:#28a745;">Login / Cadastro</a>
            <?php endif; ?>
        </nav>
        
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <?php 
                $fotoUsuario = (!empty($_SESSION['usuario_foto']) && file_exists('uploads/' . $_SESSION['usuario_foto'])) 
                    ? 'uploads/' . $_SESSION['usuario_foto'] 
                    : 'assets/img/default.png';
            ?>
            <a href="index.php?acao=perfil" class="user-badge" title="Editar Meu Perfil">
                <img src="<?= htmlspecialchars($fotoUsuario) ?>" alt="Foto do usuário">
                <span><?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Perfil') ?></span>
            </a>
        <?php endif; ?>
    </header>
    <div class="container">