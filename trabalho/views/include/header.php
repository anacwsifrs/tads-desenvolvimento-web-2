<?php 
$qtdCarrinho = isset($_SESSION['carrinho']) ? count($_SESSION['carrinho']) : 0;
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
        header { background: #2c3e50; color: white; padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; position: relative; }
        
        .header-left { display: flex; align-items: center; gap: 20px; }
        .header-left h1 a { color: white; text-decoration: none; font-size: 20px; font-weight: bold; }
        
        nav { display: flex; gap: 15px; align-items: center; }
        nav a { color: white; text-decoration: none; font-weight: bold; font-size: 14px; transition: color 0.2s; }
        nav a:hover { color: #17a2b8; text-decoration: underline; }

        .profile-container { position: relative; display: inline-block; }
        .user-badge { display: flex; align-items: center; gap: 8px; cursor: pointer; background: rgba(255,255,255,0.12); padding: 6px 12px; border-radius: 20px; transition: background 0.2s; user-select: none; }
        .user-badge:hover { background: rgba(255,255,255,0.25); }
        .user-badge img { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; }
        .user-badge span { font-size: 14px; font-weight: bold; color: white; }
        .dropdown-arrow { font-size: 10px; margin-left: 2px; }

        .dropdown-menu { display: none; position: absolute; right: 0; top: 115%; background: white; color: #333; min-width: 210px; border-radius: 8px; box-shadow: 0 4px 14px rgba(0,0,0,0.18); z-index: 1000; overflow: hidden; border: 1px solid #e0e0e0; }
        .dropdown-menu.show { display: block; animation: fadeIn 0.15s ease-in-out; }

        .dropdown-header { padding: 10px 15px; background: #f8f9fa; border-bottom: 1px solid #eee; font-size: 12px; color: #666; }
        .dropdown-header strong { display: block; color: #333; font-size: 13px; margin-top: 2px; }

        .dropdown-menu a { display: flex; align-items: center; justify-content: space-between; padding: 10px 15px; color: #333; text-decoration: none; font-size: 14px; transition: background 0.15s; font-weight: normal; }
        .dropdown-menu a:hover { background: #f1f3f5; color: #007bff; text-decoration: none; }
        .dropdown-divider { height: 1px; background: #eee; margin: 4px 0; }

        .badge-carrinho { background: #e74c3c; color: white; border-radius: 10px; padding: 2px 7px; font-size: 11px; font-weight: bold; }
        
        .btn-login-nav { background: #28a745; color: white !important; padding: 6px 14px; border-radius: 4px; text-decoration: none !important; }
        .btn-login-nav:hover { background: #218838 !important; }

        .container { max-width: 1100px; margin: 20px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        footer { text-align: center; padding: 15px; background: #e9ecef; margin-top: 30px; font-size: 14px; }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <header>
        <div class="header-left">
            <h1><a href="index.php?acao=inicio">🛍️ Brechó EcoModa</a></h1>
            <nav>
                <a href="index.php?acao=inicio">Início</a>
                <a href="index.php?acao=vitrine">Vitrine</a>
                <a href="index.php?acao=fale_conosco">Fale Conosco</a>
                <a href="index.php?acao=termos">Termos e LGPD</a>
            </nav>
        </div>
        
        <div>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <?php 
                    $fotoUsuario = (!empty($_SESSION['usuario_foto']) && file_exists('uploads/' . $_SESSION['usuario_foto'])) 
                        ? 'uploads/' . $_SESSION['usuario_foto'] 
                        : 'assets/img/default.png';
                ?>
                <div class="profile-container">
                    <div class="user-badge" onclick="toggleDropdownProfile(event)" title="Menu do Usuário">
                        <img src="<?= htmlspecialchars($fotoUsuario) ?>" alt="Foto do usuário">
                        <span><?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Perfil') ?></span>
                        <span class="dropdown-arrow">▼</span>
                    </div>

                    <div class="dropdown-menu" id="dropdownProfileMenu">
                        <div class="dropdown-header">
                            Conectado como:
                            <strong><?= htmlspecialchars($_SESSION['usuario_nome'] ?? '') ?></strong>
                        </div>

                        <a href="index.php?acao=perfil">👤 Meu Perfil</a>
                        
                        <a href="index.php?acao=carrinho">
                            <span>🛒 Meu Carrinho</span>
                            <?php if ($qtdCarrinho > 0): ?>
                                <span class="badge-carrinho"><?= $qtdCarrinho ?></span>
                            <?php endif; ?>
                        </a>

                        <div class="dropdown-divider"></div>

                        <?php if ($_SESSION['usuario_tipo'] === 'admin'): ?>
                            <a href="index.php?acao=listar">📋 Painel Lotes</a>
                            <a href="index.php?acao=gerenciar_pedidos">🛍️ Painel Vendas</a>
                            <a href="index.php?acao=clientes">👥 Painel Usuários</a>
                        <?php else: ?>
                            <a href="index.php?acao=meus_lotes">📦 Meus Lotes</a>
                            <a href="index.php?acao=meus_pedidos">🛒 Minhas Compras</a>
                        <?php endif; ?>

                        <div class="dropdown-divider"></div>

                        <a href="index.php?acao=logout" style="color: #dc3545; font-weight: bold;" onclick="return confirm('Tem certeza que deseja sair do sistema?');">🚪 Sair</a>
                    </div>
                </div>
            <?php else: ?>
                <nav>
                    <a href="index.php?acao=login" class="btn-login-nav">🔑 Login / Cadastro</a>
                </nav>
            <?php endif; ?>
        </div>
    </header>

    <script>
        function toggleDropdownProfile(event) {
            event.stopPropagation();
            const menu = document.getElementById('dropdownProfileMenu');
            if (menu) {
                menu.classList.toggle('show');
            }
        }

        document.addEventListener('click', function(event) {
            const menu = document.getElementById('dropdownProfileMenu');
            const badge = document.querySelector('.user-badge');
            if (menu && menu.classList.contains('show')) {
                if (!menu.contains(event.target) && (!badge || !badge.contains(event.target))) {
                    menu.classList.remove('show');
                }
            }
        });
    </script>
    <div class="container">