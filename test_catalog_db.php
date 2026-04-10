<?php
require_once 'config.php';
require_once 'functions.php';

echo "Testing gerenciar_catalogo logic...\n";

// Use Rangel's user if possible to simulate
$rangel = $conn->query("SELECT * FROM usuarios WHERE email LIKE '%rangel%' OR nome LIKE '%rangel%' LIMIT 1")->fetch_assoc();
if ($rangel) {
    echo "Found user rangel: {$rangel['id']}\n";
    $_SESSION['usuario_id'] = $rangel['id'];
    $_SESSION['usuario_nivel'] = $rangel['perfil_id'] ?? 1; // mock
    $_SESSION['paroquia_id'] = $rangel['paroquia_id'] ?: 1;
} else {
    echo "User rangel not found.\n";
    $_SESSION['paroquia_id'] = 1;
}
$pid = current_paroquia_id();

// Create fake entry
$stmt = $conn->prepare("INSERT INTO atividades_catalogo (paroquia_id, nome, descricao, ativo) VALUES (?, 'Teste Bug', 'Desc', 1)");
$stmt->bind_param('i', $pid);
$stmt->execute();
$id = $conn->insert_id;
echo "Inserted dummy ID: $id\n";

// Test UPDATE
$nome = 'Teste Bug Editado';
$descricao = 'Editado desc';
$dup = $conn->prepare("SELECT id FROM atividades_catalogo WHERE paroquia_id = ? AND nome = ? AND id != ?");
$dup->bind_param('isi', $pid, $nome, $id);
$dup->execute();
if ($dup->get_result()->fetch_assoc()) {
    echo "Duplicate error!\n";
} else {
    $stmt = $conn->prepare("UPDATE atividades_catalogo SET nome = ?, descricao = ? WHERE id = ? AND paroquia_id = ?");
    $stmt->bind_param('ssii', $nome, $descricao, $id, $pid);
    if ($stmt->execute()) {
        echo "Update SUCCESS!\n";
    } else {
        echo "Update FAILED: " . $conn->error . "\n";
    }
}

// Test DELETE
$stmt = $conn->prepare("DELETE FROM atividades_catalogo WHERE id = ? AND paroquia_id = ?");
$stmt->bind_param('ii', $id, $pid);
if ($stmt->execute()) {
    echo "Delete SUCCESS!\n";
} else {
    echo "Delete FAILED: " . $conn->error . "\n";
}

?>
