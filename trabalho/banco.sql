CREATE DATABASE IF NOT EXISTS brecho_db;
USE brecho_db;

CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS lotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) UNIQUE NOT NULL,
    chave_pix VARCHAR(100) NOT NULL,
    tipo_recebimento VARCHAR(30) DEFAULT 'pix',
    valor_proposta DECIMAL(10,2) DEFAULT 0.00,
    valor_credito DECIMAL(10,2) DEFAULT 0.00,
    status VARCHAR(60) DEFAULT 'Aguardando Avaliação',
    comprovante_pix VARCHAR(255) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS lote_categoria (
    lote_id INT,
    categoria_id INT,
    PRIMARY KEY (lote_id, categoria_id),
    FOREIGN KEY (lote_id) REFERENCES lotes(id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
);

INSERT INTO categorias (nome) VALUES 
('Calça Jeans'), 
('Camisetas'), 
('Casacos'), 
('Vestidos'), 
('Acessórios'),
('Bermudas e Shorts'),
('Saias'),
('Calçados Femininos'),
('Calçados Masculinos'),
('Jaquetas e Couro'),
('Blusas e Tricô'),
('Roupas Infantis'),
('Moda Praia'),
('Moda Íntima / Lingerie'),
('Ternos e Alfaiataria'),
('Agasalhos e Moletons'),
('Bolsas e Mochilas'),
('Chapéus e Bonés'),
('Bijouterias e Jóias'),
('Óculos e Relógios');

INSERT INTO lotes (codigo, chave_pix, tipo_recebimento, valor_proposta, valor_credito, status) VALUES 
('LOT-101', '12345678900', 'pix', 150.00, 180.00, 'Concluído (PIX Realizado)'),
('LOT-102', 'cliente@email.com', 'credito_loja', 200.00, 240.00, 'Proposta Aceita (Crédito na Loja)');

INSERT INTO lote_categoria (lote_id, categoria_id) VALUES 
(1, 1), (1, 2), (2, 3), (2, 10);