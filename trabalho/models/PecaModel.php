<?php
class PecaModel {
    private $pdo; 

    public function __construct($pdo) {
        $this->pdo = $pdo; 
    }

    public function listarCategorias() {
        $stmt = $this->pdo->query("SELECT * FROM categorias ORDER BY nome ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function adicionarPeca($lote_id, $categoria_id, $descricao, $marca, $tamanho, $cor, $estado, $observacoes, $foto_caminho) {
        $stmt = $this->pdo->prepare("
            INSERT INTO pecas 
            (lote_id, categoria_id, descricao, marca, tamanho, cor, estado, observacoes, foto_caminho) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $lote_id, $categoria_id, $descricao, $marca, $tamanho, $cor, $estado, $observacoes, $foto_caminho
        ]);
    }

    public function buscarPecasPorLote($lote_id) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, cat.nome as categoria_nome 
            FROM pecas p 
            JOIN categorias cat ON p.categoria_id = cat.id 
            WHERE p.lote_id = ?
        ");
        $stmt->execute([$lote_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function avaliarPeca($peca_id, $lote_id, $status_avaliacao, $valor_proposto) {
        $stmt = $this->pdo->prepare("
            UPDATE pecas 
            SET status_avaliacao = ?, valor_proposto = ? 
            WHERE id = ? AND lote_id = ?
        ");
        return $stmt->execute([$status_avaliacao, $valor_proposto, $peca_id, $lote_id]);
    }

    public function buscarPecaPorId($id) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, cat.nome as categoria_nome 
            FROM pecas p 
            JOIN categorias cat ON p.categoria_id = cat.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarTodasPecasPublicas() {
        $stmt = $this->pdo->query("
            SELECT p.*, c.nome AS categoria_nome 
            FROM pecas p 
            JOIN lotes l ON p.lote_id = l.id 
            JOIN categorias c ON p.categoria_id = c.id 
            WHERE p.status_avaliacao = 1 AND l.status IN (4, 5)
            ORDER BY p.id DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>