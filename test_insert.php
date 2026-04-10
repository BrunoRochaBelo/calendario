<?php
require_once 'functions.php';

// Mock session
$_SESSION['usuario_id'] = 1;

$pid = 1;
$nome = 'Teste Nova Atividade';
$local = null;
$tipo = null;
$descricao = 'Descricao teste';
$data_inicio = '2026-04-10';
$hora_inicio = '10:00';
$uid = 1;
$restrito = 0;
$cor = '#ff0000';

$sql = "INSERT INTO atividades (nome, paroquia_id, local_id, tipo_atividade_id, descricao, data_inicio, hora_inicio, criador_id, restrito, cor) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
echo "Types: siiisssiis\n";
$stmt->bind_param('siiisssiis', $nome, $pid, $local, $tipo, $descricao, $data_inicio, $hora_inicio, $uid, $restrito, $cor);

if ($stmt->execute()) {
    echo "Success! ID: " . $conn->insert_id . "\n";
} else {
    echo "Error: " . $stmt->error . "\n";
}
