<?php
require_once 'conexao.php';

class Lote {
    private $conn;

    public function __construct() {
        $this->conn = Conexao::getConexao();
    }

    public function criar($token, $status, $categorias_ids) {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("INSERT INTO lotes (token, status) VALUES (?, ?)");
            $stmt->execute([$token, $status]);
            $lote_id = $this->conn->lastInsertId();

            if (!empty($categorias_ids)) {
                $stmtCat = $this->conn->prepare("INSERT INTO lote_categoria (lote_id, categoria_id) VALUES (?, ?)");
                foreach ($categorias_ids as $cat_id) {
                    $stmtCat->execute([$lote_id, $cat_id]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function listarTodos() {
        $sql = "SELECT l.*, GROUP_CONCAT(c.nome SEPARATOR ', ') as categorias_nomes 
                FROM lotes l 
                LEFT JOIN lote_categoria lc ON l.id = lc.lote_id 
                LEFT JOIN categorias c ON lc.categoria_id = c.id 
                GROUP BY l.id";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM lotes WHERE id = ?");
        $stmt->execute([$id]);
        $lote = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmtCat = $this->conn->prepare("SELECT categoria_id FROM lote_categoria WHERE lote_id = ?");
        $stmtCat->execute([$id]);
        $lote['categorias'] = $stmtCat->fetchAll(PDO::FETCH_COLUMN);

        return $lote;
    }

    public function atualizar($id, $token, $status, $categorias_ids) {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("UPDATE lotes SET token = ?, status = ? WHERE id = ?");
            $stmt->execute([$token, $status, $id]);

            $stmtDel = $this->conn->prepare("DELETE FROM lote_categoria WHERE lote_id = ?");
            $stmtDel->execute([$id]);

            if (!empty($categorias_ids)) {
                $stmtCat = $this->conn->prepare("INSERT INTO lote_categoria (lote_id, categoria_id) VALUES (?, ?)");
                foreach ($categorias_ids as $cat_id) {
                    $stmtCat->execute([$id, $cat_id]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function deletar($id) {
        $stmt = $this->conn->prepare("DELETE FROM lotes WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function listarCategorias() {
        $stmt = $this->conn->query("SELECT * FROM categorias");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>