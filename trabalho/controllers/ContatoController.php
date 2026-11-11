<?php
class ContatoController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function fale_conosco() {
        $mensagem_status = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = trim($_POST['nome'] ?? '');
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
            $assunto = trim($_POST['assunto'] ?? '');
            $mensagem = trim($_POST['mensagem'] ?? '');

            if (!$email || empty($nome) || empty($assunto) || empty($mensagem)) {
                $mensagem_status = "<p style='color:red; font-weight:bold; background: #f8d7da; padding: 10px; border-radius: 5px;'>Preencha todos os campos corretamente!</p>";
            } else {
                $stmt = $this->pdo->prepare("INSERT INTO mensagens_contato (nome, email, assunto, mensagem) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nome, $email, $assunto, $mensagem]);

                $para = "atendimento@brechoecomoda.com.br";
                $headers = "From: " . $email . "\r\n" .
                           "Reply-To: " . $email . "\r\n" .
                           "X-Mailer: PHP/" . phpversion();

                @mail($para, "Contato Site: " . $assunto, $mensagem, $headers);

                $mensagem_status = "<p style='color:green; font-weight:bold; background: #d4edda; padding: 10px; border-radius: 5px;'>Mensagem enviada com sucesso! Em breve entraremos em contato.</p>";
            }
        }
        
        require_once 'views/public/fale_conosco.php';
    }
}
?>