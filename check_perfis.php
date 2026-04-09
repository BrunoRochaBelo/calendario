<?php
require 'conexao.php';

$res = $conn->query("SELECT id, nome FROM perfis");
echo "Perfis no banco:\n";
while ($row = $res->fetch_assoc()) {
    echo json_encode($row) . "\n";
}

echo "\nUsuários atuais:\n";
$res2 = $conn->query("SELECT id, nome, perfil_id, perfil_nome, nivel_acesso FROM usuarios LIMIT 10");
while ($row2 = $res2->fetch_assoc()) {
    echo json_encode($row2) . "\n";
}
