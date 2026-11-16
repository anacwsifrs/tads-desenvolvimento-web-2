<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../libs/PHPMailer/src/PHPMailer.php')) {
    require_once __DIR__ . '/../libs/PHPMailer/src/Exception.php';
    require_once __DIR__ . '/../libs/PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/../libs/PHPMailer/src/SMTP.php';
}

require_once __DIR__ . '/../models/LogModel.php';

class EmailHelper {
    private $logModel;

    public function __construct($pdo = null) {
        if ($pdo) {
            $this->logModel = new LogModel($pdo);
        }
        $this->carregarEnv();
    }

    private function carregarEnv() {
        $caminho = __DIR__ . '/../.env';
        if (!file_exists($caminho)) return;

        $linhas = file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($linhas as $linha) {
            $linha = trim($linha);
            if ($linha === '' || strpos($linha, '#') === 0 || strpos($linha, '=') === false) {
                continue;
            }
            list($nome, $valor) = explode('=', $linha, 2);
            $nome = trim($nome);
            $valor = trim($valor);
            if (!isset($_ENV[$nome])) {
                $_ENV[$nome] = $valor;
            }
        }
    }

    public function enviar($destinatarioEmail, $destinatarioNome, $assunto, $conteudoHtml, $textoAlternativo = '') {
        $usuarioIdLog = $_SESSION['usuario_id'] ?? null;

        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            if ($this->logModel) {
                $this->logModel->registrar($usuarioIdLog, 'EMAIL_ERRO', "PHPMailer não instalado ao tentar enviar para {$destinatarioEmail}.");
            }
            return ['sucesso' => false, 'erro' => 'Biblioteca PHPMailer não encontrada no servidor.'];
        }

        $host = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
        $port = intval($_ENV['SMTP_PORT'] ?? 587);
        $user = $_ENV['SMTP_USER'] ?? '';
        $pass = $_ENV['SMTP_PASS'] ?? '';
        $fromName = $_ENV['SMTP_FROM_NAME'] ?? 'Intent Brechó';

        if (empty($user) || empty($pass)) {
            if ($this->logModel) {
                $this->logModel->registrar($usuarioIdLog, 'EMAIL_ERRO', "Credenciais SMTP não configuradas no .env.");
            }
            return ['sucesso' => false, 'erro' => 'Credenciais SMTP não configuradas no .env.'];
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $user;
            $mail->Password   = $pass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $port;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($user, $fromName);
            $mail->addAddress($destinatarioEmail, $destinatarioNome ?: $destinatarioEmail);

            $mail->isHTML(true);
            $mail->Subject = $assunto;
            $mail->Body    = $this->aplicarLayout($assunto, $conteudoHtml);
            $mail->AltBody = $textoAlternativo ?: strip_tags($conteudoHtml);

            $mail->send();

            if ($this->logModel) {
                $this->logModel->registrar(
                    $usuarioIdLog,
                    'EMAIL_ENVIADO',
                    "E-mail enviado para {$destinatarioEmail} — Assunto: \"{$assunto}\"."
                );
            }

            return ['sucesso' => true];
        } catch (Exception $e) {
            if ($this->logModel) {
                $this->logModel->registrar(
                    $usuarioIdLog,
                    'EMAIL_ERRO',
                    "Falha ao enviar e-mail para {$destinatarioEmail}: {$mail->ErrorInfo}"
                );
            }
            return ['sucesso' => false, 'erro' => $mail->ErrorInfo];
        }
    }

    private function aplicarLayout($titulo, $conteudoHtml) {
        $ano = date('Y');
        return "
        <div style='font-family: Arial, Helvetica, sans-serif; background-color: #f5f5f4; padding: 24px; color: #292524;'>
            <div style='max-width: 580px; margin: 0 auto; background-color: #ffffff; border: 1px solid #e7e5e4; border-radius: 12px; overflow: hidden;'>
                <div style='background-color: #059669; color: #ffffff; padding: 20px 24px;'>
                    <h1 style='margin: 0; font-size: 20px;'>Intent Brechó</h1>
                    <p style='margin: 4px 0 0 0; font-size: 13px; color: #d1fae5;'>{$titulo}</p>
                </div>
                <div style='padding: 24px; font-size: 14px; line-height: 1.6; color: #44403c;'>
                    {$conteudoHtml}
                </div>
                <div style='background-color: #fafaf9; border-top: 1px solid #e7e5e4; padding: 14px 24px; font-size: 12px; color: #78716c; text-align: center;'>
                    &copy; {$ano} Intent Brechó • Moda Circular &amp; Sustentável<br>
                    Este é um e-mail automático enviado pelo sistema.
                </div>
            </div>
        </div>";
    }
}
?>