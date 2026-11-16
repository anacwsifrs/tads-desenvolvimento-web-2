<?php
session_start();

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'config/conexao.php'; 
require_once 'controllers/AuthController.php';
require_once 'controllers/LoteController.php';
require_once 'controllers/PecaController.php';
require_once 'controllers/UsuarioController.php';
require_once 'controllers/ContatoController.php';
require_once 'controllers/PedidoController.php';

$acao = $_GET['acao'] ?? 'vitrine';

switch ($acao) {
    case 'login':
    case 'logout':
    case 'recuperar_senha':
        $controller = new AuthController($pdo);
        $controller->$acao();
        break;

    case 'perfil':
    case 'salvar_cadastro':
    case 'buscar_cliente':
    case 'atualizar_perfil':
    case 'atualizar_senha':
    case 'clientes':
    case 'ver_cliente':
    case 'editar_cliente':
    case 'salvar_cliente_admin':
        $controller = new UsuarioController($pdo);
        $controller->$acao();
        break;

    case 'vitrine':
    case 'detalhar':
    case 'categorias':
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
    case 'sugestao_ia':
    case 'upload_comprovante':
        $controller = new LoteController($pdo);
        $controller->$acao();
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
        $controller->$acao();
        break;

    case 'fale_conosco':
        $controller = new ContatoController($pdo);
        $controller->$acao();
        break;

    default:
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Ação não encontrada.']);
        exit;
}
?>