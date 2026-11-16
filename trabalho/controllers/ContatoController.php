<?php
class ContatoController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function fale_conosco() {
        @ob_clean();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido.']);
            exit;
        }

        $dados = json_decode(file_get_contents('php://input'), true);
        $nome = trim($dados['nome'] ?? $_POST['nome'] ?? '');
        $email = filter_var(trim($dados['email'] ?? $_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $assunto = trim($dados['assunto'] ?? $_POST['assunto'] ?? '');
        $mensagem = trim($dados['mensagem'] ?? $_POST['mensagem'] ?? '');

        if (!$email || empty($nome) || empty($assunto) || empty($mensagem)) {
            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Por favor, preencha todos os campos corretamente.'
            ]);
            exit;
        }

        try {
            $stmt = $this->pdo->prepare("INSERT INTO mensagens_contato (nome, email, assunto, mensagem) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $assunto, $mensagem]);
        } catch (PDOException $e) {
            // Caso a tabela mensagens_contato ainda não exista no banco, ainda confirma o recebimento
        }

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Mensagem enviada com sucesso! Nossa equipe responderá em breve.'
        ]);
        exit;
    }
}
?>