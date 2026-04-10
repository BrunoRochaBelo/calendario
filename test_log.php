<?php
require_once 'config.php';
require_once 'functions.php';

$_SESSION['usuario_id'] = 10;
$_SESSION['paroquia_id'] = 1;

$pid = 1;
$stmt = $conn->prepare("INSERT INTO atividades_catalogo (paroquia_id, nome, descricao, ativo) VALUES (?, 'Teste Bug', 'Desc', 1)");
$stmt->bind_param('i', $pid);
$stmt->execute();
$id = $conn->insert_id;
echo "Inserted dummy ID: $id\n";

$del = $conn->prepare("DELETE FROM atividades_catalogo WHERE id = ? AND paroquia_id = ?");
$del->bind_param('ii', $id, $pid);

if ($del->execute()) {
    echo "Delete QUERY SUCCESS!\n";
    logAction($conn, 'EXCLUIR_CATALOGO', 'atividades_catalogo', $id);
    echo "Log QUERY SUCCESS!\n";
} else {
    echo "Delete FAILED: " . $conn->error . "\n";
}
?>
