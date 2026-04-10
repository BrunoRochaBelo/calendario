<?php
require 'conexao.php';
$res = $conn->query("SHOW CREATE TABLE atividades");
$row = $res->fetch_array();
echo $row[1];
