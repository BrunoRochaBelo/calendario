<?php
require 'conexao.php';
$res=$conn->query('SHOW CREATE TABLE usuarios');
echo "\n--- USUARIOS ---\n" . $res->fetch_row()[1] . "\n";
$res=$conn->query('SHOW CREATE TABLE perfis');
if($res) echo "\n--- PERFIS ---\n" . $res->fetch_row()[1] . "\n";
?>
