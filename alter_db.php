<?php
require 'conexao.php';
$queries = [
    "ALTER TABLE locais_paroquia 
     ADD COLUMN endereco varchar(255) DEFAULT NULL,
     ADD COLUMN telefone varchar(20) DEFAULT NULL,
     ADD COLUMN responsavel varchar(255) DEFAULT NULL,
     ADD COLUMN capacidade int(10) DEFAULT NULL;",
    "ALTER TABLE usuarios 
     ADD COLUMN data_nascimento date DEFAULT NULL;"
];

foreach ($queries as $q) {
    if ($conn->query($q) === TRUE) {
        echo "Sucesso: $q\n";
    } else {
        echo "Erro: " . $conn->error . "\n";
    }
}
?>
