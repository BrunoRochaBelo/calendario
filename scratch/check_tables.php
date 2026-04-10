<?php
require 'config.php';
$res = $conn->query('DESCRIBE atividade_evento_inscricoes');
while($row = $res->fetch_assoc()) {
    print_r($row);
}
$res2 = $conn->query('DESCRIBE inscricoes');
while($row = $res2->fetch_assoc()) {
    print_r($row);
}
?>
