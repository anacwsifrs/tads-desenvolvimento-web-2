<?php
require_once 'models/LoteModel.php';
require_once 'models/PecaModel.php';
require_once 'models/UsuarioModel.php';
require_once 'models/LogModel.php';

class LoteController {
    private $loteModel;
    private $pecaModel;
    private $usuarioModel;
    private $logModel;

    public function __construct($pdo) {
        $this->loteModel = new LoteModel($pdo);
        $this->pecaModel = new PecaModel($pdo);
        $this->usuarioModel = new UsuarioModel($pdo);
        $this->logModel = new LogModel($pdo);
    }
    
    public function cadastrar() {
        $this->verificarAdmin();
        $categorias = $this->pecaModel->listarCategorias();
        require_once 'views/admin/cadastrar.php'; 
    }

    public function listar() {
        $this->verificarAdmin();
        @ob_clean();
        header('Content-Type: application/json');
        echo json_encode($this->loteModel->listarLotes());
        exit;
    }

    public function editar() {
        $this->verificarAdmin();
        @ob_clean();
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? 0;
        $lote = $this->loteModel->buscarLotePorId($id);
        if ($lote) {
            echo json_encode($lote);
        } else {
            http_response_code(404);
            echo json_encode(['erro' => 'Lote não encontrado.']);
        }
        exit;
    }

    public function salvar_edicao() {
        $this->verificarAdmin();
        @ob_clean();
        header('Content-Type: application/json');

        $dados = json_decode(file_get_contents('php://input'), true);
        $lote_id = $dados['lote_id'] ?? $_POST['lote_id'] ?? 0;
        $descricao = trim($dados['descricao_lote'] ?? $_POST['descricao_lote'] ?? '');

        if ($this->loteModel->atualizarDescricaoLote($lote_id, $descricao)) {
            $this->logModel->registrar($_SESSION['usuario_id'], 'EDITAR_LOTE', "Descrição do lote #{$lote_id} atualizada para: \"{$descricao}\".");
            echo json_encode(['sucesso' => true, 'mensagem' => 'Lote atualizado com sucesso!']);
        } else {
            http_response_code(500);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar lote.']);
        }
        exit;
    }

    public function excluir() {
        $this->verificarAdmin();
        @ob_clean();
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? 0;
        if ($this->loteModel->excluirLote($id)) {
            $this->logModel->registrar($_SESSION['usuario_id'], 'EXCLUIR_LOTE', "Lote #{$id} foi excluído permanentemente.");
            echo json_encode(['sucesso' => true, 'mensagem' => 'Lote excluído com sucesso!']);
        } else {
            http_response_code(500);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao excluir lote.']);
        }
        exit;
    }

    public function upload_comprovante() {
        $this->verificarAdmin();
        @ob_clean();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['comprovante'])) {
            $lote_id = $_POST['lote_id'] ?? 0;
            $diretorio_comp = 'uploads/comprovantes/';
            if (!is_dir($diretorio_comp)) {
                mkdir($diretorio_comp, 0777, true);
            }

            $extensao = pathinfo($_FILES['comprovante']['name'], PATHINFO_EXTENSION);
            $nome_arquivo = "comprovante_{$lote_id}_" . time() . "." . $extensao;

            if (move_uploaded_file($_FILES['comprovante']['tmp_name'], $diretorio_comp . $nome_arquivo)) {
                $caminho_banco = 'comprovantes/' . $nome_arquivo;
                $this->loteModel->salvarComprovante($lote_id, $caminho_banco);
                $this->loteModel->atualizarStatusLote($lote_id, 4);

                $this->logModel->registrar($_SESSION['usuario_id'], 'COMPROVANTE_LOTE', "Admin anexou comprovante de pagamento PIX ao lote #{$lote_id}.");

                echo json_encode([
                    'sucesso' => true,
                    'mensagem' => 'Comprovante enviado com sucesso!',
                    'comprovante' => $caminho_banco
                ]);
                exit;
            }
        }

        http_response_code(400);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao enviar comprovante.']);
        exit;
    }

    public function salvar() {
        $this->verificarAdmin();
        @ob_clean();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido.']);
            exit;
        }

        $cpf = trim($_POST['cpf'] ?? '');
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $pix = trim($_POST['pix'] ?? '');
        $descricao_lote = trim($_POST['descricao_lote'] ?? '') ?: 'Lote sem descrição';

        if (empty($cpf) || empty($nome)) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'CPF e Nome do fornecedor são obrigatórios.']);
            exit;
        }

        $cliente = $this->usuarioModel->buscarClientePorCpf($cpf);
        
        if ($cliente) {
            $cliente_id = $cliente['id'];
            $this->usuarioModel->atualizarUsuarioBasico($cliente_id, $nome, $telefone, $pix);
        } else {
            $apenas_numeros_cpf = preg_replace('/[^0-9]/', '', $cpf);
            $senha_temporaria = 'brecho' . substr($apenas_numeros_cpf, 0, 4);
            $senha_hash = password_hash($senha_temporaria, PASSWORD_DEFAULT);
            $cliente_id = $this->usuarioModel->criarUsuario($cpf, $nome, $email, $senha_hash, $telefone, $pix);
        }

        $lote_id = $this->loteModel->criarLote($cliente_id, $descricao_lote);
        $diretorio_pecas = 'uploads/pecas/';

        if (!is_dir($diretorio_pecas)) {
            mkdir($diretorio_pecas, 0777, true);
        }

        $qtdPecasCadastradas = 0;

        if (isset($_POST['peca_categoria']) && is_array($_POST['peca_categoria'])) {
            $total_pecas = count($_POST['peca_categoria']);

            for ($i = 0; $i < $total_pecas; $i++) {
                if (empty($_POST['peca_categoria'][$i])) continue;

                $categoria_id = $_POST['peca_categoria'][$i];
                $descricao = $_POST['peca_descricao'][$i] ?? '';
                $marca = $_POST['peca_marca'][$i] ?? '';
                $tamanho = $_POST['peca_tamanho'][$i] ?? '';
                $cor = $_POST['peca_cor'][$i] ?? '';
                $estado = $_POST['peca_estado'][$i] ?? '';
                $observacoes = $_POST['peca_observacoes'][$i] ?? '';
                $caminho_foto = '';

                if (isset($_FILES['peca_foto']['name'][$i]) && $_FILES['peca_foto']['error'][$i] === 0) {
                    $extensao = pathinfo($_FILES['peca_foto']['name'][$i], PATHINFO_EXTENSION);
                    $nome_arquivo = "peca_{$lote_id}_" . time() . "_{$i}." . $extensao;
                    $caminho_completo = $diretorio_pecas . $nome_arquivo;

                    if (move_uploaded_file($_FILES['peca_foto']['tmp_name'][$i], $caminho_completo)) {
                        $caminho_foto = 'pecas/' . $nome_arquivo;
                    }
                }

                $this->pecaModel->adicionarPeca(
                    $lote_id, $categoria_id, $descricao, $marca, 
                    $tamanho, $cor, $estado, $observacoes, $caminho_foto
                );
                $qtdPecasCadastradas++;
            }
        }

        $this->logModel->registrar(
            $_SESSION['usuario_id'],
            'CADASTRAR_LOTE',
            "Lote #{$lote_id} cadastrado para o fornecedor {$nome} com {$qtdPecasCadastradas} peça(s)."
        );

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Lote cadastrado com sucesso!',
            'lote_id' => $lote_id
        ]);
        exit;
    }

    public function avaliar() {
        $this->verificarAdmin();
        @ob_clean();
        header('Content-Type: application/json');

        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID do lote não informado.']);
            exit;
        }

        $lote = $this->loteModel->buscarLotePorId($id);
        if (!$lote) {
            http_response_code(404);
            echo json_encode(['erro' => 'Lote não encontrado.']);
            exit;
        }

        $lote['pecas'] = $this->pecaModel->buscarPecasPorLote($id);
        echo json_encode($lote);
        exit;
    }

    public function salvar_proposta() {
        $this->verificarAdmin();
        @ob_clean();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido.']);
            exit;
        }

        $dados = json_decode(file_get_contents('php://input'), true);
        $lote_id = $dados['lote_id'] ?? $_POST['lote_id'] ?? null;
        $valor_total = $dados['valor_total'] ?? $_POST['valor_total'] ?? 0;
        $pecas_avaliadas = $dados['pecas'] ?? $_POST['pecas'] ?? [];

        if (!$lote_id) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Lote inválido.']);
            exit;
        }

        foreach ($pecas_avaliadas as $peca_id => $item) {
            $status = $item['status'];
            $valor = ($status == 1) ? $item['valor'] : null;
            $this->pecaModel->avaliarPeca($peca_id, $lote_id, $status, $valor);
        }

        $this->loteModel->salvarProposta($lote_id, $valor_total);

        $valorFormatado = number_format((float)$valor_total, 2, ',', '.');
        $this->logModel->registrar(
            $_SESSION['usuario_id'],
            'ENVIAR_PROPOSTA',
            "Proposta do lote #{$lote_id} finalizada no valor de R$ {$valorFormatado}."
        );

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Proposta salva e enviada ao cliente com sucesso!'
        ]);
        exit;
    }

    public function meus_lotes() {
        $this->verificarLogin();
        @ob_clean();
        header('Content-Type: application/json');

        $cliente_id = $_SESSION['usuario_id'];
        $todos_lotes = $this->loteModel->listarLotes();
        $lotes = array_values(array_filter($todos_lotes, function($l) use ($cliente_id) {
            return $l['cliente_id'] == $cliente_id;
        }));

        echo json_encode($lotes);
        exit;
    }

    public function proposta() {
        $this->verificarLogin();
        @ob_clean();
        header('Content-Type: application/json');

        $id = $_GET['id'] ?? 0;
        $lote = $this->loteModel->buscarLotePorId($id);

        if (!$lote) {
            http_response_code(404);
            echo json_encode(['erro' => 'Lote não encontrado.']);
            exit;
        }

        if ($_SESSION['usuario_tipo'] !== 'admin' && $_SESSION['usuario_id'] != $lote['cliente_id']) {
            http_response_code(403);
            echo json_encode(['erro' => 'Acesso não autorizado.']);
            exit;
        }

        $lote['pecas'] = $this->pecaModel->buscarPecasPorLote($lote['id']);
        echo json_encode($lote);
        exit;
    }

    public function responder_proposta() {
        $this->verificarLogin();
        @ob_clean();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido.']);
            exit;
        }

        $dados = json_decode(file_get_contents('php://input'), true);
        $lote_id = $dados['lote_id'] ?? $_POST['lote_id'] ?? 0;
        $decisao = intval($dados['decisao'] ?? $_POST['decisao'] ?? 0);

        $lote = $this->loteModel->buscarLotePorId($lote_id);
        if (!$lote || ($_SESSION['usuario_tipo'] !== 'admin' && $_SESSION['usuario_id'] != $lote['cliente_id'])) {
            http_response_code(403);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado.']);
            exit;
        }

        if ($decisao === 5) {
            $valor_pix = floatval($lote['valor_proposta']);
            $valor_credito = $valor_pix * 1.15;
            $this->usuarioModel->adicionarSaldoCliente($lote['cliente_id'], $valor_credito);
        }

        $this->loteModel->atualizarStatusLote($lote_id, $decisao);

        $mapaDecisao = [
            4 => 'Aceita via PIX',
            5 => 'Aceita via Crédito (+15%)',
            6 => 'Recusada',
            7 => 'Destinada para Doação'
        ];
        $textoDecisao = $mapaDecisao[$decisao] ?? "Status {$decisao}";
        $this->logModel->registrar(
            $_SESSION['usuario_id'],
            'RESPOSTA_PROPOSTA',
            "Cliente respondeu à proposta do lote #{$lote_id}: {$textoDecisao}."
        );

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Sua resposta foi registrada com sucesso!'
        ]);
        exit;
    }

    public function sugestao_ia() {
        @ob_clean();
        header('Content-Type: application/json');

        function carregarEnv($caminho) {
            if (!file_exists($caminho)) {
                return false;
            }
            $linhas = file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($linhas as $linha) {
                if (strpos(trim($linha), '#') === 0) continue; 
                list($nome, $valor) = explode('=', $linha, 2);
                $_ENV[trim($nome)] = trim($valor);
            }
            return true;
        }

        carregarEnv(__DIR__ . '/../.env');
        $apiKey = $_ENV['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY');

        if (!$apiKey) {
            echo json_encode(['erro' => 'Chave de API do Gemini não configurada no ficheiro .env.']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        $categoria   = $data['categoria'] ?? 'Não especificada';
        $marca       = $data['marca'] ?? 'Não informada';
        $tamanho     = $data['tamanho'] ?? 'Não informado';
        $cor         = $data['cor'] ?? 'Não informada';
        $estado      = $data['estado'] ?? 'Usado';
        $descricao   = $data['descricao'] ?? '';
        $observacoes = $data['observacoes'] ?? '';

        if (empty($descricao) && empty($categoria)) {
            echo json_encode(['erro' => 'Informações da peça insuficientes.']);
            exit;
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-lite-latest:generateContent?key=" . $apiKey;

        $prompt = "Você é um especialista em precificação de roupas em brechó. Analise os detalhes desta peça:\n" .
                  "- Categoria: {$categoria}\n" .
                  "- Marca: {$marca}\n" .
                  "- Tamanho: {$tamanho}\n" .
                  "- Cor: {$cor}\n" .
                  "- Estado de Conservação: {$estado}\n" .
                  "- Descrição: {$descricao}\n" .
                  "- Observações de Triagem: {$observacoes}\n\n" .
                  "Com base nesses atributos, sugira um valor justo em Reais (R$) para compra no brechó e faça um comentário curto sobre o apelo comercial dela. Responda em texto simples e conciso.";

        $payload = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];

        $opcoes = [
            'http' => [
                'header'  => "Content-Type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($payload),
                'ignore_errors' => true
            ]
        ];

        $contexto = stream_context_create($opcoes);
        $response = file_get_contents($url, false, $contexto);

        if ($response === FALSE) {
            echo json_encode(['erro' => 'Falha ao conectar com o Google Gemini.']);
            exit;
        }

        echo $response;
        exit;
    }
    
    private function verificarLogin() {
        if (!isset($_SESSION['usuario_id'])) {
            @ob_clean();
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão expirada. Faça login novamente.']);
            exit;
        }
    }

    private function verificarAdmin() {
        $this->verificarLogin();
        if ($_SESSION['usuario_tipo'] !== 'admin') {
            @ob_clean();
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso restrito a administradores.']);
            exit;
        }
    }
}
?>