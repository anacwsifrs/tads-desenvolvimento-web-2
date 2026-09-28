<?php
require_once 'Lote.php';

class LoteController {
    private $modelo;

    public function __construct() {
        $this->modelo = new Lote();
    }

    public function index() {
        require 'views/listar.php';
    }

    public function formCriar() {
        $categorias = $this->modelo->listarCategorias();
        $lote = null;
        require 'views/form.php';
    }

    public function salvar() {
        $codigo = $_POST['codigo'];
        $chave_pix = $_POST['chave_pix'];
        $tipo_recebimento = $_POST['tipo_recebimento'] ?? 'pix';
        $valor_proposta = (float)($_POST['valor_proposta'] ?? 0);
        $valor_credito = $valor_proposta * 1.20;
        $status = $_POST['status'];
        $categorias = $_POST['categorias'] ?? [];
        $comprovante = $_POST['comprovante_atual'] ?? '';

        if (isset($_FILES['comprovante_pix']) && $_FILES['comprovante_pix']['error'] === UPLOAD_ERR_OK) {
            $extensao = pathinfo($_FILES['comprovante_pix']['name'], PATHINFO_EXTENSION);
            $nomeArquivo = uniqid() . '.' . $extensao;
            $destino = 'uploads/' . $nomeArquivo;

            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }

            if (move_uploaded_file($_FILES['comprovante_pix']['tmp_name'], $destino)) {
                $comprovante = $destino;
            }
        }

        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $this->modelo->atualizar($_POST['id'], $codigo, $chave_pix, $tipo_recebimento, $valor_proposta, $valor_credito, $status, $comprovante, $categorias);
        } else {
            $this->modelo->criar($codigo, $chave_pix, $tipo_recebimento, $valor_proposta, $valor_credito, $status, $comprovante, $categorias);
        }
        header("Location: index.php");
    }

    public function formEditar($id) {
        $lote = $this->modelo->buscarPorId($id);
        $categorias = $this->modelo->listarCategorias();
        require 'views/form.php';
    }

    public function deletar($id) {
        $this->modelo->deletar($id);
        header("Location: index.php");
    }

    public function apiListar() {
        header('Content-Type: application/json');
        $lotes = $this->modelo->listarTodos();
        echo json_encode($lotes);
    }

    public function apiDetalhar($id) {
        header('Content-Type: application/json');
        $lote = $this->modelo->buscarPorId($id);
        echo json_encode($lote);
    }
}
?>