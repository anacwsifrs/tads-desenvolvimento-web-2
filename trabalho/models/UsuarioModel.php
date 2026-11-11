<?php
class UsuarioModel {
    private $conn;

    public function __construct($conexao) {
        $this->conn = $conexao;
    }

    public function buscarClientePorCpf($cpf) {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE cpf = ?");
        $stmt->execute([$cpf]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarClientePorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarClientes() {
        $stmt = $this->conn->query("SELECT * FROM usuarios WHERE tipo = 'cliente' ORDER BY nome ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function debitarSaldoCliente($cliente_id, $valor_debitar) {
        $stmt = $this->conn->prepare("UPDATE usuarios SET saldo_credito = GREATEST(0, saldo_credito - ?) WHERE id = ?");
        return $stmt->execute([$valor_debitar, $cliente_id]);
    }

    public function criarUsuario($cpf, $nome, $email, $senha_hash, $telefone, $pix, $tipo = 'cliente') {
        $stmt = $this->conn->prepare("INSERT INTO usuarios (cpf, nome, email, senha, telefone, chave_pix, tipo) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$cpf, $nome, $email, $senha_hash, $telefone, $pix, $tipo]);
        return $this->conn->lastInsertId();
    }

    public function atualizarUsuarioBasico($id, $nome, $telefone, $pix) {
        $stmt = $this->conn->prepare("UPDATE usuarios SET nome = ?, telefone = ?, chave_pix = ? WHERE id = ?");
        return $stmt->execute([$nome, $telefone, $pix, $id]);
    }

    public function atualizarUsuarioCompleto($id, $cpf, $nome, $telefone, $pix) {
        $stmt = $this->conn->prepare("UPDATE usuarios SET cpf = ?, nome = ?, telefone = ?, chave_pix = ? WHERE id = ?");
        return $stmt->execute([$cpf, $nome, $telefone, $pix, $id]);
    }
}
?>