CREATE DATABASE IF NOT EXISTS brecho_db;
USE brecho_db;

DROP TABLE IF EXISTS logs;
DROP TABLE IF EXISTS pedido_itens;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS lote_tag;
DROP TABLE IF EXISTS tags;
DROP TABLE IF EXISTS mensagens_contato;
DROP TABLE IF EXISTS historico_saldo;
DROP TABLE IF EXISTS pecas;
DROP TABLE IF EXISTS lotes;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    chave_pix VARCHAR(100) NULL,
    foto VARCHAR(255) DEFAULT 'default.png',
    tipo ENUM('admin', 'cliente') DEFAULT 'cliente',
    saldo_credito DECIMAL(10,2) DEFAULT 0.00,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL
);

CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL
);

CREATE TABLE lotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    status INT DEFAULT 1, 
    valor_proposta DECIMAL(10,2) NULL,
    data_proposta DATETIME NULL,
    comprovante VARCHAR(255) NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE pecas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lote_id INT NOT NULL,
    categoria_id INT NOT NULL,
    descricao VARCHAR(255) NULL,
    foto_caminho VARCHAR(255) NULL,
    tamanho VARCHAR(30) NULL,
    cor VARCHAR(50) NULL,
    marca VARCHAR(100) NULL,
    estado VARCHAR(100) NULL,
    observacoes TEXT NULL,
    status_avaliacao INT DEFAULT 0,
    valor_proposto DECIMAL(10,2) NULL,
    disponivel TINYINT(1) DEFAULT 1,
    FOREIGN KEY (lote_id) REFERENCES lotes(id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT
);

CREATE TABLE lote_tag (
    lote_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (lote_id, tag_id),
    FOREIGN KEY (lote_id) REFERENCES lotes(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

CREATE TABLE mensagens_contato (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    assunto VARCHAR(150) NOT NULL,
    mensagem TEXT NOT NULL,
    enviado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    valor_total DECIMAL(10,2) NOT NULL,
    status ENUM('Aguardando PIX', 'Aprovado', 'Cancelado') DEFAULT 'Aguardando PIX',
    comprovante VARCHAR(255) NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE pedido_itens (
    pedido_id INT NOT NULL,
    peca_id INT NOT NULL,
    preco_venda DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (pedido_id, peca_id),
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (peca_id) REFERENCES pecas(id) ON DELETE RESTRICT
);

CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    acao VARCHAR(80) NOT NULL,
    descricao TEXT NOT NULL,
    ip VARCHAR(45) NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

INSERT INTO usuarios (cpf, nome, email, senha, telefone, tipo) VALUES 
('000.000.000-00', 'Administrador Brechó', 'intent.brecho.contato@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '(51) 99999-9999', 'admin');

INSERT INTO categorias (nome) VALUES 
('Camisa Social'),
('Camiseta / T-Shirt'),
('Blusa / Cropped'),
('Regata'),
('Moletom / Tricô'),
('Jaqueta / Corta-Vento'),
('Blazer / Sobretudo'),
('Calça Jeans'),
('Calça Alfaiataria / Sarja'),
('Shorts / Bermuda'),
('Saia'),
('Vestido Curto / Midi'),
('Vestido Longo'),
('Macacão / Macaquinho'),
('Tênis'),
('Bota / Coturno'),
('Sandália / Rasteirinha'),
('Sapato Social'),
('Sapatilha'),
('Bolsa / Mochila'),
('Cinto / Lenço'),
('Moda Íntima / Praia'),
('Acessórios / Bijuterias'),
('Outros');

INSERT INTO tags (nome) VALUES 
('Novo com Etiqueta'),
('Impecável / Seminovo'),
('Higienizado & Passado'),
('Marca Premium / Grife'),
('Vintage / Retrô'),
('Peça Coringa / Básica'),
('Tecido Nobre (Linho/Seda/Couro)'),
('Alfaiataria Estruturada'),
('Pequeno Desgaste de Uso'),
('Falta Botão / Zíper');