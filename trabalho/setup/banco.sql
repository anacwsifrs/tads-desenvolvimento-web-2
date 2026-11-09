CREATE DATABASE IF NOT EXISTS brecho_db;
USE brecho_db;

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
    status_avaliacao INT DEFAULT 0,
    valor_proposto DECIMAL(10,2) NULL,
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

INSERT INTO categorias (nome) VALUES 
('Camisa'), ('Camiseta'), ('Regata'), ('Jaqueta'), 
('Calça Jeans'), ('Bermuda'), ('Vestido'), ('Saia'), 
('Blusa de Frio'), ('Tênis'), ('Bota'), ('Bolsa');

INSERT INTO tags (nome) VALUES 
('Sem Avarias'), ('Sem Etiqueta'), ('Com Pequeno Desgaste'), ('Marca Famosa'), ('Peça Vintage');

INSERT INTO usuarios (cpf, nome, email, senha, telefone, tipo) VALUES 
('000.000.000-00', 'Administrador Brechó', '2025000434@aluno.canoas.ifrs.edu.br', '$2y$10$eE.l9XQ4jJ4dJjM5Bq7I.e123456789012345678901234567890', '11999999999', 'admin');

INSERT INTO usuarios (cpf, nome, email, senha, telefone, chave_pix, saldo_credito) VALUES 
('111.111.111-11', 'Maria Oliveira', 'cliente@email.com', '$2y$10$eE.l9XQ4jJ4dJjM5Bq7I.e123456789012345678901234567890', '11988888888', 'maria@pix.com', 0.00);