<?php
require 'conexao.php';
$res = $conn->query("SHOW COLUMNS FROM atividades LIKE 'cor'");
echo "Qtd colunas cor: " . $res->num_rows . "\n";
