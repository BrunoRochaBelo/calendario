<?php
require_once 'config.php';
$conn->query("
    CREATE TABLE IF NOT EXISTS sistema_config (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        chave VARCHAR(50) UNIQUE NOT NULL,
        valor TEXT,
        last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

$conn->query("INSERT INTO sistema_config (chave, valor) VALUES ('versao_sistema', '2.5.0') ON DUPLICATE KEY UPDATE valor = '2.5.0'");
$conn->query("INSERT INTO sistema_config (chave, valor) VALUES ('status_banco', 'funcional') ON DUPLICATE KEY UPDATE valor = 'funcional'");

echo "Banco de Dados atualizado para V2.5.0 com sucesso.\n";
