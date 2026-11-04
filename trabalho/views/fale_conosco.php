<?php
require_once 'header.php';

$mensagem_status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $assunto = trim($_POST['assunto']);
    $mensagem = trim($_POST['mensagem']);

    if (!$email || empty($nome) || empty($assunto) || empty($mensagem)) {
        $mensagem_status = "<p style='color:red;'>Preencha todos os campos corretamente!</p>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO mensagens_contato (nome, email, assunto, mensagem) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $assunto, $mensagem]);

        $para = "atendimento@brechoecomoda.com.br";
        $headers = "From: " . $email . "\r\n" .
                   "Reply-To: " . $email . "\r\n" .
                   "X-Mailer: PHP/" . phpversion();

        @mail($para, "Contato Site: " . $assunto, $mensagem, $headers);

        $mensagem_status = "<p style='color:green; font-weight:bold;'>Mensagem enviada com sucesso! Em breve entraremos em contato.</p>";
    }
}
?>

<h2>Fale Conosco</h2>
<?= $mensagem_status ?>

<form action="fale_conosco.php" method="POST" style="margin-top:15px; max-width:600px;">
    <div style="margin-bottom:10px;">
        <label>Seu Nome:</label><br>
        <input type="text" name="nome" required style="width:100%; padding:8px;">
    </div>
    <div style="margin-bottom:10px;">
        <label>Seu E-mail:</label><br>
        <input type="email" name="email" required style="width:100%; padding:8px;">
    </div>
    <div style="margin-bottom:10px;">
        <label>Assunto:</label><br>
        <input type="text" name="assunto" required style="width:100%; padding:8px;">
    </div>
    <div style="margin-bottom:10px;">
        <label>Mensagem:</label><br>
        <textarea name="mensagem" rows="5" required style="width:100%; padding:8px;"></textarea>
    </div>
    <button type="submit" style="padding:10px 20px; background:#007bff; color:white; border:none; cursor:pointer;">Enviar Mensagem</button>
</form>

<?php require_once 'footer.php'; ?>