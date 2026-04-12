<?php
require_once __DIR__ . '/../conexao.php';
$res = $conn->query("SELECT * FROM perfis");
$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
