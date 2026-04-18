-- Base de données: casino_access_system
-- Créé par: Casino Access System v1.0

CREATE DATABASE IF NOT EXISTS casino_access_system;
USE casino_access_system;

-- Table des codes d'accès
CREATE TABLE access_codes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME,
    is_used BOOLEAN DEFAULT FALSE,
    used_at DATETIME,
    player_ip VARCHAR(45),
    player_name VARCHAR(100)
);

-- Table des transactions
CREATE TABLE transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code_id INT,
    game_type ENUM('keno', 'slots') NOT NULL,
    bet_amount DECIMAL(10,2) NOT NULL,
    win_amount DECIMAL(10,2) DEFAULT 0.00,
    transaction_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (code_id) REFERENCES access_codes(id) ON DELETE CASCADE
);

-- Table admin (pour authentification)
CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertion admin par défaut (username: admin, password: admin123)
INSERT INTO admin_users (username, password_hash) 
VALUES ('admin', '$2y$10$YourHashedPasswordHere');

-- Index pour optimiser les recherches
CREATE INDEX idx_code ON access_codes(code);
CREATE INDEX idx_expires ON access_codes(expires_at);
CREATE INDEX idx_used ON access_codes(is_used);

-- Vue pour statistiques (optionnelle)
-- CREATE VIEW code_statistics AS
-- SELECT 
--     COUNT(*) as total_codes,
--     SUM(CASE WHEN is_used = FALSE THEN 1 ELSE 0 END) as available_codes,
--     SUM(CASE WHEN is_used = TRUE THEN 1 ELSE 0 END) as used_codes,
--     SUM(amount) as total_amount,
--     SUM(CASE WHEN is_used = FALSE THEN amount ELSE 0 END) as available_amount
-- FROM access_codes;
