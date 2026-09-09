<?php
require_once 'Lote.php';

class LoteController {
    private $modelo;

    public function __construct() {
        $this->modelo = new Lote();
    }

    public function index() {
        $lotes = $this->modelo->listarTodos();
        require 'views/listar.php';
    }

    public function formCriar() {
        $categorias = $this->modelo->listarCategorias();
        $lote = null;
        require 'views/form.php';
    }

    public function salvar() {
        $token = $_POST['token'];
        $status = $_POST['status'];
        $categorias = $_POST['categorias'] ?? []; 

        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $this->modelo->atualizar($_POST['id'], $token, $status, $categorias);
        } else {
            $this->modelo->criar($token, $status, $categorias);
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
}
?>