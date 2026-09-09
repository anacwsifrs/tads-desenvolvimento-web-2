CREATE DATABASE brecho_db;
USE brecho_db;

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL
);

CREATE TABLE lotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(20) UNIQUE NOT NULL,
    status VARCHAR(30) DEFAULT 'Aguardando Avaliação'
);

CREATE TABLE lote_categoria (
    lote_id INT,
    categoria_id INT,
    PRIMARY KEY (lote_id, categoria_id),
    FOREIGN KEY (lote_id) REFERENCES lotes(id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
);

INSERT INTO categorias (nome) VALUES ('Calça Jeans'), ('Camisetas'), ('Casacos'), ('Vestidos'), ('Acessórios');