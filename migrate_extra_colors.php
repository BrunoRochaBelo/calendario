<?php
require_once 'conexao.php';

$queries = [
    "ALTER TABLE atividades ADD COLUMN IF NOT EXISTS is_multi_color TINYINT(1) DEFAULT 0",
    "ALTER TABLE atividades ADD COLUMN IF NOT EXISTS is_flashing TINYINT(1) DEFAULT 0"
];

foreach ($queries as $q) {
    if ($conn->query($q)) {
        echo "Sucesso: $q\n";
    } else {
        echo "Erro: " . $conn->error . "\n";
    }
}
?>
