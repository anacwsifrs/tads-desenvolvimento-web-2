<?php
class LogModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function registrar($usuario_id, $acao, $descricao) {
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
            $stmt = $this->pdo->prepare("
                INSERT INTO logs (usuario_id, acao, descricao, ip) 
                VALUES (?, ?, ?, ?)
            ");
            return $stmt->execute([$usuario_id, $acao, $descricao, $ip]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function listarTodos($limite = 300) {
        $limite = (int) $limite;
        $stmt = $this->pdo->prepare("
            SELECT 
                l.id,
                l.usuario_id,
                l.acao,
                l.descricao,
                l.ip,
                l.criado_em,
                COALESCE(u.nome, 'Sistema / Visitante') AS usuario_nome,
                u.email AS usuario_email,
                u.tipo AS usuario_tipo
            FROM logs l
            LEFT JOIN usuarios u ON l.usuario_id = u.id
            ORDER BY l.criado_em DESC, l.id DESC
            LIMIT {$limite}
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>