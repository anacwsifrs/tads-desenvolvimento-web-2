<?php
session_start();
require_once 'config/conexao.php'; 
require_once 'controllers/AuthController.php';
require_once 'controllers/LoteController.php';
require_once 'controllers/PecaController.php';
require_once 'controllers/UsuarioController.php';
require_once 'controllers/ContatoController.php';
require_once 'controllers/PedidoController.php';

$acao = $_GET['acao'] ?? 'inicio';

switch ($acao) {
    case 'login':
    case 'logout':
    case 'recuperar_senha':
        $controller = new AuthController($pdo);
        $controller->$acao();
        break;

    case 'perfil':
    case 'cadastro':
    case 'salvar_cadastro':
    case 'api_buscar_cliente':
    case 'api_atualizar_perfil':
    case 'api_atualizar_senha':
    case 'clientes':
    case 'debitar_saldo':
        $controller = new UsuarioController($pdo);
        if (method_exists($controller, $acao)) {
            $controller->$acao();
        } else {
            require_once "views/public/cadastro.php";
        }
        break;
    
    case 'inicio':
    case 'vitrine':
    case 'detalhar':
        $controller = new PecaController($pdo);
        if (method_exists($controller, $acao)) {
            $controller->$acao();
        } else {
            require_once "views/public/inicio.php";
        }
        break;

    case 'listar':
    case 'meus_lotes':
    case 'cadastrar':
    case 'salvar': 
    case 'avaliar':
    case 'salvar_proposta':
    case 'proposta':
    case 'responder_proposta':
    case 'editar':
    case 'salvar_edicao':
    case 'excluir':
    case 'sugestao_ia':
    case 'upload_comprovante':
        $controller = new LoteController($pdo);
        if (method_exists($controller, $acao)) {
            $controller->$acao();
        } else {
            die("Ação não encontrada no LoteController.");
        }
        break;

    case 'carrinho':
    case 'adicionar_carrinho':
    case 'remover_carrinho':
    case 'finalizar_compra':
    case 'meus_pedidos': 
    case 'upload_comprovante_pix':
    case 'gerenciar_pedidos': 
    case 'aprovar_pedido':
    case 'cancelar_pedido':
        $controller = new PedidoController($pdo);
        if (method_exists($controller, $acao)) {
            $controller->$acao();
        } else {
            header("Location: index.php?acao=inicio");
            exit;
        }
        break;

    case 'fale_conosco':
        $controller = new ContatoController($pdo);
        $controller->$acao();
        break;

    case 'termos':
        require_once "views/public/termos.php";
        break;

    default:
        header("Location: index.php?acao=inicio");
        exit;
}
?>