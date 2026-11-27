CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    sobrenome VARCHAR(100) NOT NULL DEFAULT '',
    email VARCHAR(150) NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    carga_diaria_minutos SMALLINT UNSIGNED NOT NULL DEFAULT 480,
    tolerancia_minutos TINYINT UNSIGNED NOT NULL DEFAULT 10,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_usuarios_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS registros_ponto (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    data DATE NOT NULL,
    tipo ENUM('normal', 'atestado', 'folga', 'feriado', 'falta') NOT NULL DEFAULT 'normal',
    entrada TIME NULL,
    saida_intervalo TIME NULL,
    retorno_intervalo TIME NULL,
    saida TIME NULL,
    foto_entrada VARCHAR(255) NULL,
    foto_saida_intervalo VARCHAR(255) NULL,
    foto_retorno_intervalo VARCHAR(255) NULL,
    foto_saida VARCHAR(255) NULL,
    observacao VARCHAR(255) NULL,
    UNIQUE KEY uk_usuario_data (usuario_id, data),
    CONSTRAINT fk_registro_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
