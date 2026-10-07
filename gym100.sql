-- ============================================
-- Gym101% - Base de Dados
-- Importa este ficheiro no phpMyAdmin (XAMPP)
-- ============================================

CREATE DATABASE IF NOT EXISTS gym100 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gym100;

-- Utilizadores
CREATE TABLE utilizadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(80) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    foto_perfil VARCHAR(255) DEFAULT 'default.png',
    objetivo VARCHAR(100) DEFAULT NULL,
    data_registo DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Registos de progresso (peso, gordura corporal, etc.)
CREATE TABLE progresso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT NOT NULL,
    data_registo DATE NOT NULL,
    peso DECIMAL(5,2) NOT NULL,
    gordura_corporal DECIMAL(4,1) DEFAULT NULL,
    musculo DECIMAL(4,1) DEFAULT NULL,
    notas VARCHAR(255) DEFAULT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Treinos registados
CREATE TABLE treinos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    tipo VARCHAR(50) DEFAULT NULL,
    data_treino DATE NOT NULL,
    duracao_minutos INT DEFAULT NULL,
    calorias_queimadas INT DEFAULT NULL,
    notas TEXT,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Amizades entre utilizadores
CREATE TABLE amizades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT NOT NULL,
    amigo_id INT NOT NULL,
    estado ENUM('pendente','aceite') DEFAULT 'pendente',
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE CASCADE,
    FOREIGN KEY (amigo_id) REFERENCES utilizadores(id) ON DELETE CASCADE,
    UNIQUE KEY par_unico (utilizador_id, amigo_id)
) ENGINE=InnoDB;

-- Desafios / competições entre amigos
CREATE TABLE desafios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    criador_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    descricao VARCHAR(255) DEFAULT NULL,
    tipo ENUM('treinos','calorias','peso_perdido') DEFAULT 'treinos',
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (criador_id) REFERENCES utilizadores(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Participantes de cada desafio e respetiva pontuação
CREATE TABLE desafio_participantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    desafio_id INT NOT NULL,
    utilizador_id INT NOT NULL,
    pontos INT DEFAULT 0,
    FOREIGN KEY (desafio_id) REFERENCES desafios(id) ON DELETE CASCADE,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE CASCADE,
    UNIQUE KEY participante_unico (desafio_id, utilizador_id)
) ENGINE=InnoDB;

-- Refeições / plano alimentar
CREATE TABLE refeicoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    tipo ENUM('pequeno_almoco','almoco','lanche','jantar','ceia') DEFAULT 'almoco',
    data_refeicao DATE NOT NULL,
    calorias INT DEFAULT 0,
    proteina_g INT DEFAULT 0,
    hidratos_g INT DEFAULT 0,
    gordura_g INT DEFAULT 0,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Meta calórica diária definida pelo utilizador
CREATE TABLE metas_nutricionais (
    utilizador_id INT PRIMARY KEY,
    calorias_meta INT DEFAULT 2000,
    proteina_meta INT DEFAULT 150,
    hidratos_meta INT DEFAULT 200,
    gordura_meta INT DEFAULT 60,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE CASCADE
) ENGINE=InnoDB;
