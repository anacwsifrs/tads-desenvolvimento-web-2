<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexao.php';
require_once 'models/LoteModel.php';

$loteModel = new LoteModel($pdo);
$loteModel->atualizarLotesExpirados();

$acao = isset($_GET['acao']) ? $_GET['acao'] : 'listar';
$rotasPublicas = [
    'login', 
    'cadastro', 
    'recuperar_senha', 
    'vitrine', 
    'detalhar', 
    'fale_conosco', 
    'termos', 
    'proposta', 
    'responder_proposta',
    'buscar_cliente'
];

if (!in_array($acao, $rotasPublicas) && !isset($_SESSION['usuario_id'])) {
    header("Location: index.php?acao=login");
    exit;
}

$rotasAdmin = [
    'listar', 
    'cadastrar',
    'salvar',
    'avaliar', 
    'salvar_proposta', 
    'editar', 
    'salvar_edicao', 
    'excluir', 
    'upload_comprovante', 
    'clientes', 
    'debitar_saldo'
];

if (in_array($acao, $rotasAdmin) && isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: index.php?acao=meus_lotes");
    exit;
}

if ($acao == 'login') {
    require_once 'views/login.php';

} elseif ($acao == 'cadastro') {
    require_once 'views/cadastro.php';

} elseif ($acao == 'logout') {
    require_once 'views/logout.php';

} elseif ($acao == 'recuperar_senha') {
    require_once 'views/recuperar_senha.php';

} elseif ($acao == 'vitrine') {
    require_once 'views/vitrine.php';

} elseif ($acao == 'detalhar') {
    require_once 'views/detalhar.php';

} elseif ($acao == 'fale_conosco') {
    require_once 'views/fale_conosco.php';

} elseif ($acao == 'termos') {
    require_once 'views/termos.php';

} elseif ($acao == 'listar') {
    $lotes = $loteModel->listarLotes();
    require_once 'views/listar.php';

} elseif ($acao == 'meus_lotes') {
    $cliente_id = $_SESSION['usuario_id'];
    $stmt = $pdo->prepare("SELECT * FROM lotes WHERE cliente_id = ? ORDER BY id DESC");
    $stmt->execute([$cliente_id]);
    $meusLotes = $stmt->fetchAll();
    require_once 'views/meus_lotes.php';

} elseif ($acao == 'cadastrar') {
    $categorias = $loteModel->listarCategorias();
    require_once 'views/cadastrar.php';

} elseif ($acao == 'buscar_cliente') {
    $cpf = isset($_GET['cpf']) ? $_GET['cpf'] : '';
    $cliente = $loteModel->buscarClientePorCpf($cpf);
    header('Content-Type: application/json');
    echo json_encode($cliente ?: []);
    exit;

} elseif ($acao == 'salvar') {
    $cpf = $_POST['cpf'];
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $pix = $_POST['pix'];
    $descricao_lote = $_POST['descricao_lote'];
    
    $pecas_categorias = isset($_POST['peca_categoria']) ? $_POST['peca_categoria'] : [];
    $pecas_descricoes = isset($_POST['peca_descricao']) ? $_POST['peca_descricao'] : [];

    $pecas = [];
    for ($i = 0; $i < count($pecas_categorias); $i++) {
        if (!empty($pecas_categorias[$i])) {
            $pecas[] = [
                'categoria_id' => $pecas_categorias[$i],
                'descricao' => $pecas_descricoes[$i]
            ];
        }
    }

    if ($loteModel->cadastrarLoteCompleto($cpf, $nome, $telefone, $pix, $descricao_lote, $pecas)) {
        echo "<script>alert('Lote e peças cadastrados com sucesso!'); window.location.href='index.php?acao=listar';</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar lote.'); window.history.back();</script>";
    }

} elseif ($acao == 'avaliar') {
    $id = $_GET['id'] ?? 0;
    $lote = $loteModel->buscarLotePorId($id);

    if ($lote && $lote['status'] >= 3) {
        echo "<script>alert('Este lote já foi avaliado e não pode ser reavaliado.'); window.location.href='index.php?acao=listar';</script>";
        exit;
    }

    require_once 'views/avaliar.php';

} elseif ($acao == 'salvar_proposta') {
    $lote_id = $_POST['lote_id'];
    $valor_total = $_POST['valor_total'];
    $pecas_avaliadas = isset($_POST['pecas']) ? $_POST['pecas'] : [];
    
    if ($loteModel->salvarProposta($lote_id, $valor_total, $pecas_avaliadas)) {
        echo "<script>alert('Proposta salva! O lote agora aguarda a resposta do cliente.'); window.location.href='index.php?acao=listar';</script>";
    } else {
        echo "<script>alert('Erro ao salvar proposta.'); window.history.back();</script>";
    }

} elseif ($acao == 'proposta') {
    $token = isset($_GET['token']) ? $_GET['token'] : '';
    $lote = $loteModel->buscarLotePorToken($token);

    if (!$lote) {
        die("<h3>Link inválido ou lote não encontrado.</h3>");
    }
    
    $valor_pix = $lote['valor_proposta'];
    $valor_credito = $valor_pix + ($valor_pix * 0.15);

    require_once 'views/proposta.php';

} elseif ($acao == 'responder_proposta') {
    $lote_id = $_POST['lote_id'];
    $decisao = $_POST['decisao'];
    $token = $_POST['token'];
    
    if ($loteModel->responderProposta($lote_id, $decisao)) {
        echo "<script>alert('Sua resposta foi registrada com sucesso!'); window.location.href='index.php?acao=proposta&token=$token';</script>";
    } else {
        echo "<script>alert('Erro ao registrar resposta.'); window.history.back();</script>";
    }

} elseif ($acao == 'editar') {
    $id = $_GET['id'] ?? 0;
    $lote = $loteModel->buscarLotePorId($id);
    require_once 'views/editar.php';

} elseif ($acao == 'salvar_edicao') {
    $lote_id = $_POST['lote_id'];
    $cpf = $_POST['cpf'];
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $pix = $_POST['pix'];
    $descricao = $_POST['descricao_lote'];

    if ($loteModel->atualizarLote($lote_id, $descricao, $cpf, $nome, $telefone, $pix)) {
        echo "<script>alert('Lote atualizado com sucesso!'); window.location.href='index.php?acao=listar';</script>";
    } else {
        echo "<script>alert('Erro ao atualizar lote.'); window.history.back();</script>";
    }

} elseif ($acao == 'excluir') {
    $id = $_GET['id'] ?? 0;
    if ($loteModel->excluirLote($id)) {
        echo "<script>alert('Lote excluído com sucesso!'); window.location.href='index.php?acao=listar';</script>";
    } else {
        echo "<script>alert('Erro ao excluir lote.'); window.location.href='index.php?acao=listar';</script>";
    }

} elseif ($acao == 'upload_comprovante') {
    $lote_id = $_POST['lote_id'];

    if (isset($_FILES['comprovante']) && $_FILES['comprovante']['error'] == 0) {
        $extensao = pathinfo($_FILES['comprovante']['name'], PATHINFO_EXTENSION);
        $novo_nome = "comprovante_" . $lote_id . "_" . time() . "." . $extensao;
        
        $diretorio = "uploads/";
        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0777, true);
        }

        if (move_uploaded_file($_FILES['comprovante']['tmp_name'], $diretorio . $novo_nome)) {
            $loteModel->salvarComprovante($lote_id, $novo_nome);
            echo "<script>alert('Comprovante anexado com sucesso!'); window.location.href='index.php?acao=listar';</script>";
        } else {
            echo "<script>alert('Erro ao salvar o arquivo do comprovante.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Nenhum arquivo enviado.'); window.history.back();</script>";
    }

} elseif ($acao == 'clientes') { 
    $clientes = $loteModel->listarClientes();
    require_once 'views/clientes.php';

} elseif ($acao == 'debitar_saldo') {
    $cliente_id = $_POST['cliente_id'];
    $valor_gasto = $_POST['valor_gasto'];
    $justificativa = $_POST['justificativa'];

    if ($loteModel->debitarSaldoCliente($cliente_id, $valor_gasto)) {
        echo "<script>alert('Saldo debitado com sucesso! Motivo: $justificativa'); window.location.href='index.php?acao=clientes';</script>";
    } else {
        echo "<script>alert('Erro ao debitar saldo.'); window.history.back();</script>";
    }

} else {
    header("Location: index.php?acao=vitrine");
    exit;
}
?>