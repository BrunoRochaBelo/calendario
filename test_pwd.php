<?php
require 'conexao.php';

$id = 1; // Assuming master user is ID 1
$sql = "SELECT id, email, senha FROM usuarios WHERE id = $id";
$res = $conn->query($sql);
if ($res && $res->num_rows > 0) {
    $u = $res->fetch_assoc();
    echo "Before: ID: " . $u['id'] . " | Email: " . $u['email'] . " | Hash: " . $u['senha'] . "\n";
    
    $novaSenha = 'admin123';
    $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
    $stmtPass = $conn->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');
    $stmtPass->bind_param('si', $hash, $id);
    
    if ($stmtPass->execute()) {
        echo "Successfully updated to admin123. New Hash: $hash\n";
    } else {
        echo "Update failed: " . $stmtPass->error . "\n";
    }
} else {
    echo "User 1 not found.\n";
}
?>
