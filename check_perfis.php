<?php
require 'conexao.php';
$r=$conn->query('SELECT id, nome FROM perfis');
while($row=$r->fetch_assoc()) echo $row['id'] . ' - ' . $row['nome'] . "\n";
?>
