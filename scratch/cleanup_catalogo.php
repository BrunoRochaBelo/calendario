<?php
$conn = new mysqli('localhost', 'root', '', 'u596929139_calen');
if ($conn->connect_error) die("Erro: " . $conn->connect_error);

$cols = $conn->query("SHOW COLUMNS FROM atividades_catalogo");
$existing = [];
while($r = $cols->fetch_assoc()) $existing[] = $r['Field'];

if (in_array('data_criacao', $existing)) {
    $conn->query("ALTER TABLE atividades_catalogo DROP COLUMN data_criacao");
}
if (in_array('ultima_atualizacao', $existing)) {
    $conn->query("ALTER TABLE atividades_catalogo DROP COLUMN ultima_atualizacao");
}

echo "Colunas processadas com sucesso.";
$conn->close();
?>
