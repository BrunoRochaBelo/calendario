<?php
require_once 'config.php';
$rangel = $conn->query("SELECT * FROM usuarios WHERE id = 10")->fetch_assoc();
var_dump($rangel['perm_gerenciar_catalogo']);
var_dump($rangel['perfil_id']);
$perms = loadPermissions($conn, 10);
var_dump($perms);
?>
