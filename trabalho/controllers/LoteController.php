<?php
require_once 'models/LoteModel.php';
require_once 'models/PecaModel.php';
require_once 'models/UsuarioModel.php';

class LoteController {
    private $loteModel;
    private $pecaModel;
    private $usuarioModel;

    public function __construct($pdo) {
        $this->loteModel = new LoteModel($pdo);
        $this->pecaModel = new PecaModel($pdo);
        $this->usuarioModel = new UsuarioModel($pdo);
    }

    public function listar() {
        $this->verificarAdmin();
        $lotes = $this->loteModel->listarLotes();
        require_once 'views/admin/listar.php';
    }

    public function meus_lotes() {
        $this->verificarLogin();
        $cliente_id = $_SESSION['usuario_id'];
        $todos_lotes = $this->loteModel->listarLotes();
        $lotes = array_filter($todos_lotes, function($l) use ($cliente_id) {
            return $l['cliente_id'] == $cliente_id;
        });

        require_once 'views/cliente/meus_lotes.php';
    }
    
    public function cadastrar() {
        $this->verificarAdmin();
        $categorias = $this->pecaModel->listarCategorias();
        require_once 'views/admin/cadastrar.php'; 
    }

    public function salvar() {
        $this->verificarAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cpf = $_POST['cpf'] ?? '';
            $nome = $_POST['nome'] ?? '';
            $email = $_POST['email'] ?? '';
            $telefone = $_POST['telefone'] ?? '';
            $pix = $_POST['pix'] ?? '';
            $descricao_lote = $_POST['descricao_lote'] ?? 'Lote sem descrição';
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
                }
            }

            echo "<script>alert('Lote cadastrado com sucesso!'); window.location.href='index.php?acao=listar';</script>";
            exit;
        }
    }

    public function avaliar() {
        $this->verificarAdmin();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?acao=listar");
            exit;
        }

        $lote = $this->loteModel->buscarLotePorId($id);
        if ($lote) {
            $lote['pecas'] = $this->pecaModel->buscarPecasPorLote($id);
        }
        
        require_once 'views/admin/avaliar.php';
    }

    public function salvar_proposta() {
        $this->verificarAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lote_id = $_POST['lote_id'];
            $valor_total = $_POST['valor_total'] ?? 0;
            $pecas_avaliadas = $_POST['pecas'] ?? [];

            foreach ($pecas_avaliadas as $peca_id => $dados) {
                $status = $dados['status'];
                $valor = ($status == 1) ? $dados['valor'] : null;
                $this->pecaModel->avaliarPeca($peca_id, $lote_id, $status, $valor);
            }

            $this->loteModel->salvarProposta($lote_id, $valor_total);

            echo "<script>alert('Proposta salva com sucesso!'); window.location.href='index.php?acao=listar';</script>";
            exit;
        }
    }

    public function proposta() {
        $this->verificarLogin();
        $id = $_GET['id'] ?? 0;
        $lote = $this->loteModel->buscarLotePorId($id);

        if (!$lote) {
            die("<h3>Lote não encontrado.</h3>");
        }

        if ($_SESSION['usuario_tipo'] !== 'admin' && $_SESSION['usuario_id'] != $lote['cliente_id']) {
            die("<h3>Acesso não autorizado.</h3>");
        }
        
        $lote['pecas'] = $this->pecaModel->buscarPecasPorLote($lote['id']);
        require_once 'views/cliente/proposta.php';
    }

    public function responder_proposta() {
        $this->verificarLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lote_id = $_POST['lote_id'] ?? 0;
            $decisao = $_POST['decisao'] ?? 0;

            $lote = $this->loteModel->buscarLotePorId($lote_id);
            if (!$lote || ($_SESSION['usuario_tipo'] !== 'admin' && $_SESSION['usuario_id'] != $lote['cliente_id'])) {
                die("<h3>Acesso negado.</h3>");
            }

            $this->loteModel->atualizarStatusLote($lote_id, $decisao);
            echo "<script>alert('Sua resposta foi registrada com sucesso!'); window.location.href='index.php?acao=proposta&id=$lote_id';</script>";
            exit;
        }
    }

    public function editar() {
        $this->verificarAdmin();
        $id = $_GET['id'] ?? 0;
        $lote = $this->loteModel->buscarLotePorId($id);
        require_once 'views/admin/editar.php';
    }

    public function salvar_edicao() {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lote_id = $_POST['lote_id'];
            $descricao = $_POST['descricao_lote'];
            
            $this->loteModel->atualizarDescricaoLote($lote_id, $descricao);
            echo "<script>alert('Lote atualizado com sucesso!'); window.location.href='index.php?acao=listar';</script>";
            exit;
        }
    }

    public function excluir() {
        $this->verificarAdmin();
        $id = $_GET['id'] ?? 0;
        $this->loteModel->excluirLote($id);
        echo "<script>alert('Lote excluído!'); window.location.href='index.php?acao=listar';</script>";
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

    public function upload_comprovante() {
        $this->verificarAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['comprovante'])) {
            $lote_id = $_POST['lote_id'];
            
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

                echo "<script>alert('Comprovante enviado com sucesso!'); window.location.href='index.php?acao=listar';</script>";
            } else {
                echo "<script>alert('Erro ao enviar comprovante.'); window.history.back();</script>";
            }
        }
    }
    
    private function verificarLogin() {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?acao=login");
            exit;
        }
    }

    private function verificarAdmin() {
        $this->verificarLogin();
        if ($_SESSION['usuario_tipo'] !== 'admin') {
            header("Location: index.php?acao=meus_lotes");
            exit;
        }
    }
}
?>