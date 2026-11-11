<?php
session_start();
require_once 'config/conexao.php'; 
require_once 'controllers/AuthController.php';
require_once 'controllers/LoteController.php';
require_once 'controllers/PecaController.php';
require_once 'controllers/UsuarioController.php';
require_once 'controllers/ContatoController.php';

$acao = $_GET['acao'] ?? 'vitrine';

switch ($acao) {
    case 'login':
    case 'logout':
    case 'recuperar_senha':
        $controller = new AuthController($pdo);
        $controller->$acao();
        break;

    case 'perfil':
    case 'clientes':
    case 'debitar_saldo':
        $controller = new UsuarioController($pdo);
        $controller->$acao();
        break;

    case 'vitrine':
    case 'detalhar':
        $controller = new PecaController($pdo);
        $controller->$acao();
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
    case 'upload_comprovante':
        $controller = new LoteController($pdo);
        if (method_exists($controller, $acao)) {
            $controller->$acao();
        } else {
            die("Ação não encontrada no LoteController.");
        }
        break;

    case 'cadastro':
    case 'salvar_cadastro':
        $controller = new UsuarioController($pdo);
        if (method_exists($controller, $acao)) {
            $controller->$acao();
        } else {
            require_once "views/public/cadastro.php";
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
        header("Location: index.php?acao=vitrine");
        exit;
}
?>