<?php
require 'conexao.php';
$conn->query("UPDATE atividades SET cor = '#3b82f6' WHERE cor IS NULL OR cor = ''");
echo "Updated " . $conn->affected_rows . " rows.";
?>
