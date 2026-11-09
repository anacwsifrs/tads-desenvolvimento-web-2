<?php
class LoteModel {
    private $conn;

    public function __construct($conexao) {
        $this->conn = $conexao;
    }

    public function buscarClientePorCpf($cpf) {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE cpf = ?");
        $stmt->execute([$cpf]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarClientes() {
        $stmt = $this->conn->query("SELECT * FROM usuarios WHERE tipo = 'cliente' ORDER BY nome ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarClientePorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function debitarSaldoCliente($cliente_id, $valor_debitar) {
        $stmt = $this->conn->prepare("UPDATE usuarios SET saldo_credito = GREATEST(0, saldo_credito - ?) WHERE id = ?");
        return $stmt->execute([$valor_debitar, $cliente_id]);
    }

    public function cadastrarLoteCompleto($cpf, $nome, $email, $telefone, $pix, $descricao_lote, $pecas) {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("SELECT id FROM usuarios WHERE cpf = ?");
            $stmt->execute([$cpf]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cliente) {
                $cliente_id = $cliente['id'];
                $stmt_up = $this->conn->prepare("UPDATE usuarios SET nome = ?, telefone = ?, chave_pix = ? WHERE id = ?");
                $stmt_up->execute([$nome, $telefone, $pix, $cliente_id]);
            } else {
                $apenas_numeros_cpf = preg_replace('/[^0-9]/', '', $cpf);
                $senha_temporaria = 'brecho' . substr($apenas_numeros_cpf, 0, 4); 
                $senha_hash = password_hash($senha_temporaria, PASSWORD_DEFAULT);

                $stmt = $this->conn->prepare("INSERT INTO usuarios (cpf, nome, email, senha, telefone, chave_pix, tipo) VALUES (?, ?, ?, ?, ?, ?, 'cliente')");
                $stmt->execute([$cpf, $nome, $email, $senha_hash, $telefone, $pix]);
                $cliente_id = $this->conn->lastInsertId();

                $assunto = "Sua conta no Brechó foi criada!";
                $mensagem = "Olá $nome,\n\nRecebemos suas peças e sua conta foi criada com sucesso em nosso sistema!\n\n";
                $mensagem .= "Seu login: $email\n";
                $mensagem .= "Sua senha provisória: $senha_temporaria\n\n";
                $mensagem .= "Recomendamos que você acesse o sistema para acompanhar sua proposta e altere sua senha no primeiro acesso.\n\nAtenciosamente,\nEquipe do Brechó";
                
                $headers = "From: nao-responda@seubrecho.com.br\r\n";
                $headers .= "Reply-To: contato@seubrecho.com.br\r\n";
                
                // O @ oculta erros caso você esteja testando em localhost sem servidor SMTP configurado (XAMPP/WAMP)
                @mail($email, $assunto, $mensagem, $headers);
            }

            $token = bin2hex(random_bytes(3));
            $stmt = $this->conn->prepare("INSERT INTO lotes (cliente_id, descricao, status, token) VALUES (?, ?, 1, ?)");
            $stmt->execute([$cliente_id, $descricao_lote, $token]);
            $lote_id = $this->conn->lastInsertId();

            if (!empty($pecas)) {
                $stmt_peca = $this->conn->prepare("INSERT INTO pecas (lote_id, categoria_id, descricao) VALUES (?, ?, ?)");
                foreach ($pecas as $peca) {
                    $stmt_peca->execute([$lote_id, $peca['categoria_id'], $peca['descricao']]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            // die("Erro no banco: " . $e->getMessage());
            return false;
        }
    }

    public function listarCategorias() {
        $stmt = $this->conn->query("SELECT * FROM categorias ORDER BY nome ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarLotePorId($id) {
        $stmt = $this->conn->prepare("
            SELECT l.*, c.cpf, c.nome as cliente_nome, c.telefone, c.chave_pix, c.saldo_credito 
            FROM lotes l 
            JOIN usuarios c ON l.cliente_id = c.id 
            WHERE l.id = ?
        ");
        $stmt->execute([$id]);
        $lote = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($lote) {
            $stmt_pecas = $this->conn->prepare("
                SELECT p.*, cat.nome as categoria_nome 
                FROM pecas p 
                JOIN categorias cat ON p.categoria_id = cat.id 
                WHERE p.lote_id = ?
            ");
            $stmt_pecas->execute([$id]);
            $lote['pecas'] = $stmt_pecas->fetchAll(PDO::FETCH_ASSOC);
        }
        return $lote;
    }

    public function salvarProposta($lote_id, $valor_total, $pecas_avaliadas) {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("
                UPDATE lotes 
                SET valor_proposta = ?, status = 3, data_proposta = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$valor_total, $lote_id]);

            $stmt_peca = $this->conn->prepare("
                UPDATE pecas 
                SET status_avaliacao = ?, valor_proposto = ? 
                WHERE id = ? AND lote_id = ?
            ");
            
            foreach ($pecas_avaliadas as $peca_id => $dados) {
                $status = $dados['status'];
                $valor = ($status == 1) ? $dados['valor'] : null; 
                $stmt_peca->execute([$status, $valor, $peca_id, $lote_id]);
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function buscarLotePorToken($token) {
        $stmt = $this->conn->prepare("
            SELECT l.*, c.id as cliente_id, c.nome as cliente_nome 
            FROM lotes l 
            JOIN usuarios c ON l.cliente_id = c.id 
            WHERE l.token = ?
        ");
        $stmt->execute([$token]);
        $lote = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($lote) {
            $stmt_pecas = $this->conn->prepare("
                SELECT p.*, cat.nome as categoria_nome 
                FROM pecas p 
                JOIN categorias cat ON p.categoria_id = cat.id 
                WHERE p.lote_id = ?
            ");
            $stmt_pecas->execute([$lote['id']]);
            $lote['pecas'] = $stmt_pecas->fetchAll(PDO::FETCH_ASSOC);
        }
        return $lote;
    }

    public function responderProposta($lote_id, $novo_status) {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("SELECT cliente_id, valor_proposta, status FROM lotes WHERE id = ?");
            $stmt->execute([$lote_id]);
            $lote = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($lote && $lote['status'] == 3) {
                $stmt_up = $this->conn->prepare("UPDATE lotes SET status = ? WHERE id = ?");
                $stmt_up->execute([$novo_status, $lote_id]);

                if ($novo_status == 5) {
                    $valor_com_bonus = $lote['valor_proposta'] + ($lote['valor_proposta'] * 0.15);
                    $stmt_saldo = $this->conn->prepare("UPDATE usuarios SET saldo_credito = saldo_credito + ? WHERE id = ?");
                    $stmt_saldo->execute([$valor_com_bonus, $lote['cliente_id']]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function salvarComprovante($lote_id, $nome_arquivo) {
        $stmt = $this->conn->prepare("UPDATE lotes SET comprovante = ? WHERE id = ?");
        return $stmt->execute([$nome_arquivo, $lote_id]);
    }

    public function listarLotes() {
        $stmt = $this->conn->query("
            SELECT l.*, c.nome as cliente_nome, c.telefone 
            FROM lotes l 
            JOIN usuarios c ON l.cliente_id = c.id 
            ORDER BY l.id DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function excluirLote($id) {
        $stmt = $this->conn->prepare("DELETE FROM lotes WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function atualizarLote($lote_id, $descricao, $cpf, $nome, $telefone, $pix) {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("SELECT cliente_id FROM lotes WHERE id = ?");
            $stmt->execute([$lote_id]);
            $cliente_id = $stmt->fetchColumn();

            $stmt_c = $this->conn->prepare("UPDATE usuarios SET cpf = ?, nome = ?, telefone = ?, chave_pix = ? WHERE id = ?");
            $stmt_c->execute([$cpf, $nome, $telefone, $pix, $cliente_id]);

            $stmt_l = $this->conn->prepare("UPDATE lotes SET descricao = ? WHERE id = ?");
            $stmt_l->execute([$descricao, $lote_id]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function atualizarLotesExpirados() {
        $stmt = $this->conn->query("SELECT id, status, data_proposta FROM lotes WHERE status IN (3, 6) AND data_proposta IS NOT NULL");
        $lotes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $hoje = new DateTime();

        foreach ($lotes as $lote) {
            $dataProposta = new DateTime($lote['data_proposta']);
            $diasUteis = 0;

            while ($dataProposta < $hoje) {
                $dataProposta->modify('+1 day');
                $diaDaSemana = $dataProposta->format('N');

                if ($diaDaSemana <= 5) {
                    $diasUteis++;
                }
            }

            if ($diasUteis > 3) {
                $stmt_up = $this->conn->prepare("UPDATE lotes SET status = 7 WHERE id = ?");
                $stmt_up->execute([$lote['id']]);
            }
        }
    }
}
?>