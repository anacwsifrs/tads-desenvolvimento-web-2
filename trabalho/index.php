<?php
require_once 'LoteController.php';

$controller = new LoteController();
$acao = $_GET['acao'] ?? 'index';

switch ($acao) {
    case 'index':
        $controller->index();
        break;
    case 'novo':
        $controller->formCriar();
        break;
    case 'salvar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->salvar();
        }
        break;
    case 'editar':
        $controller->formEditar($_GET['id']);
        break;
    case 'deletar':
        $controller->deletar($_GET['id']);
        break;
    case 'api_listar':
        $controller->apiListar();
        break;
    case 'api_detalhar':
        $controller->apiDetalhar($_GET['id']);
        break;
    default:
        $controller->index();
        break;
}
?>