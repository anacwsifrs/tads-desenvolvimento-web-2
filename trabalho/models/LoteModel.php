<?php
class LoteModel {
    private $conn;

    public function __construct($conexao) {
        $this->conn = $conexao;
    }

    public function criarLote($cliente_id, $descricao_lote) {
        $stmt = $this->conn->prepare("INSERT INTO lotes (cliente_id, descricao, status) VALUES (?, ?, 1)");
        $stmt->execute([$cliente_id, $descricao_lote]);
        return $this->conn->lastInsertId();
    }

    public function buscarLotePorId($id) {
        $stmt = $this->conn->prepare("
            SELECT l.*, c.cpf, c.nome as cliente_nome, c.telefone, c.chave_pix, c.saldo_credito 
            FROM lotes l 
            JOIN usuarios c ON l.cliente_id = c.id 
            WHERE l.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarLotes() {
        $stmt = $this->conn->query("
            SELECT l.*, c.nome as cliente_nome, c.telefone 
            FROM lotes l 
            JOIN usuarios c ON l.cliente_id = c.id 
            ORDER BY l.id DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function atualizarStatusLote($lote_id, $novo_status) {
        $stmt = $this->conn->prepare("UPDATE lotes SET status = ? WHERE id = ?");
        return $stmt->execute([$novo_status, $lote_id]);
    }

    public function salvarProposta($lote_id, $valor_total) {
        $stmt = $this->conn->prepare("UPDATE lotes SET valor_proposta = ?, status = 3, data_proposta = NOW() WHERE id = ?");
        return $stmt->execute([$valor_total, $lote_id]);
    }

    public function salvarComprovante($lote_id, $nome_arquivo) {
        $stmt = $this->conn->prepare("UPDATE lotes SET comprovante = ? WHERE id = ?");
        return $stmt->execute([$nome_arquivo, $lote_id]);
    }

    public function excluirLote($id) {
        $stmt = $this->conn->prepare("DELETE FROM lotes WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function atualizarDescricaoLote($lote_id, $descricao) {
        $stmt = $this->conn->prepare("UPDATE lotes SET descricao = ? WHERE id = ?");
        return $stmt->execute([$descricao, $lote_id]);
    }
}
?>