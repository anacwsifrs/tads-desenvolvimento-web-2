<?php
require_once 'models/LogModel.php';
require_once 'helpers/EmailHelper.php';

class ContatoController {
    private $pdo;
    private $logModel;
    private $emailHelper;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->logModel = new LogModel($pdo);
        $this->emailHelper = new EmailHelper($pdo);
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
        }

        $emailBrecho = $_ENV['SMTP_USER'] ?? 'intent.brecho.contato@gmail.com';
        $htmlAdmin = "
            <p>Nova mensagem recebida pelo formulário <strong>Fale Conosco</strong>:</p>
            <p><strong>Nome:</strong> {$nome}<br>
            <strong>E-mail:</strong> {$email}<br>
            <strong>Assunto:</strong> {$assunto}</p>
            <div style='background:#f5f5f4; padding:12px; border-radius:8px; margin-top:12px;'>
                " . nl2br(htmlspecialchars($mensagem)) . "
            </div>
        ";
        $this->emailHelper->enviar($emailBrecho, 'Equipe Intent Brechó', "[Fale Conosco] {$assunto}", $htmlAdmin);

        $htmlUsuario = "
            <p>Olá, <strong>{$nome}</strong>!</p>
            <p>Recebemos sua mensagem com o assunto <strong>\"{$assunto}\"</strong> e nossa equipe responderá em breve.</p>
            <p style='color:#78716c; font-size:13px;'>Cópia da sua mensagem:<br><em>" . nl2br(htmlspecialchars($mensagem)) . "</em></p>
        ";
        $this->emailHelper->enviar($email, $nome, "Recebemos sua mensagem - Intent Brechó", $htmlUsuario);

        $this->logModel->registrar(
            $_SESSION['usuario_id'] ?? null,
            'FALE_CONOSCO',
            "Mensagem recebida de {$nome} ({$email}) — Assunto: \"{$assunto}\"."
        );

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Mensagem enviada com sucesso! Enviamos uma cópia de confirmação para o seu e-mail.'
        ]);
        exit;
    }
}
?>