<?php
require 'conexao.php';
if ($conn->query("ALTER TABLE atividades ADD COLUMN cor VARCHAR(7) DEFAULT '#3b82f6' AFTER descricao")) {
    echo "Column cor added successfully.";
} else {
    echo "Error: " . $conn->error;
}
