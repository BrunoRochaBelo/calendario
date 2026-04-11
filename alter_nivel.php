<?php
require_once 'functions.php';

require 'conexao.php';
$q = "ALTER TABLE usuarios ADD COLUMN nivel_acesso int(11) DEFAULT 3;";
if ($conn->query($q) === TRUE) {
    echo "Sucesso";
} else {
    echo "Erro: " . $conn->error;
}
?>
