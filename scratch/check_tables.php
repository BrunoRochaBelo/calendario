<?php
require_once __DIR__ . '/../conexao.php';
$res = $conn->query("SHOW TABLES");
while ($row = $res->fetch_row()) {
    echo $row[0] . PHP_EOL;
}
