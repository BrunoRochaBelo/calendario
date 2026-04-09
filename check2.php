<?php
require 'conexao.php';
$out = "";
$res=$conn->query('SHOW CREATE TABLE usuarios');
if($res) $out .= "\n--- USUARIOS ---\n" . $res->fetch_row()[1] . "\n";
$res=$conn->query('SHOW CREATE TABLE perfis');
if($res) $out .= "\n--- PERFIS ---\n" . $res->fetch_row()[1] . "\n";
file_put_contents('schema2.txt', $out);
?>

