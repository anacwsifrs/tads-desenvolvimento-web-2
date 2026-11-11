<?php
class PedidoModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function criarPedido($cliente_id, $valor_total, $itens) {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("INSERT INTO pedidos (cliente_id, valor_total) VALUES (?, ?)");
            $stmt->execute([$cliente_id, $valor_total]);
            $pedido_id = $this->pdo->lastInsertId();

            $stmtItem = $this->pdo->prepare("INSERT INTO pedido_itens (pedido_id, peca_id, preco_venda) VALUES (?, ?, ?)");
            $stmtPeca = $this->pdo->prepare("UPDATE pecas SET disponivel = 0 WHERE id = ?");

            foreach ($itens as $peca_id => $item) {
                $stmtItem->execute([$pedido_id, $peca_id, $item['preco_venda']]);
                $stmtPeca->execute([$peca_id]);
            }

            $this->pdo->commit();
            return $pedido_id;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function listarPedidosCliente($cliente_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM pedidos WHERE cliente_id = ? ORDER BY criado_em DESC");
        $stmt->execute([$cliente_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTodosPedidos() {
        $stmt = $this->pdo->prepare("
            SELECT p.*, u.nome as cliente_nome 
            FROM pedidos p 
            JOIN usuarios u ON p.cliente_id = u.id 
            ORDER BY p.criado_em DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function atualizarStatus($pedido_id, $status) {
        $stmt = $this->pdo->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $pedido_id]);
    }

    public function cancelarPedidoERestaurarPecas($pedido_id) {
        $stmt = $this->pdo->prepare("UPDATE pecas SET disponivel = 1 WHERE id IN (SELECT peca_id FROM pedido_itens WHERE pedido_id = ?)");
        $stmt->execute([$pedido_id]);
        return $this->atualizarStatus($pedido_id, 'Cancelado');
    }

    public function salvarComprovantePix($pedido_id, $caminho) {
        $stmt = $this->pdo->prepare("UPDATE pedidos SET comprovante = ? WHERE id = ?");
        return $stmt->execute([$caminho, $pedido_id]);
    }
}
?>