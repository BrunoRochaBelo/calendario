<?php
require 'conexao.php';
$res = $conn->query("DESCRIBE atividades");
while ($row = $res->fetch_assoc()) {
    echo json_encode($row) . "\n";
}
?>
