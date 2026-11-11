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

            $token = bin2hex(random_bytes(10));
            $lote_id = $this->loteModel->criarLote($cliente_id, $descricao_lote, $token);
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
        $token = $_GET['token'] ?? '';
        $lote = $this->loteModel->buscarLotePorToken($token);

        if (!$lote) {
            die("<h3>Link inválido ou lote não encontrado.</h3>");
        }
        
        $lote['pecas'] = $this->pecaModel->buscarPecasPorLote($lote['id']);
        require_once 'views/public/proposta.php';
    }

    public function responder_proposta() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lote_id = $_POST['lote_id'];
            $decisao = $_POST['decisao'];
            $token = $_POST['token'];

            $this->loteModel->atualizarStatusLote($lote_id, $decisao);
            echo "<script>alert('Sua resposta foi registrada com sucesso!'); window.location.href='index.php?acao=proposta&token=$token';</script>";
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
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        $descricao = $input['descricao'] ?? '';

        if (empty($descricao)) {
            echo json_encode(['erro' => 'Descrição não informada.']);
            exit;
        }

        $apiKey = getenv('GEMINI_API_KEY') ?: 'SUA_CHAVE_AQUI';
        $prompt = "Aja como um especialista em precificação de brechó. Sugira um valor justo de avaliação em Reais (R$) para a seguinte peça: " . $descricao;
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=" . $apiKey;
        $data = [
            "contents" => [
                ["parts" => [["text" => $prompt]]]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        curl_close($ch);

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