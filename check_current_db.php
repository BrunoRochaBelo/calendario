<?php
require_once 'conexao.php';
$res = $conn->query("SHOW TABLES");
$tables = [];
while ($row = $res->fetch_array()) {
    $tables[] = $row[0];
}
echo "Tables in " . $db_config['name'] . ":\n";
foreach ($tables as $t) {
    echo "- $t\n";
    $cols = $conn->query("SHOW COLUMNS FROM `$t` ");
    while ($c = $cols->fetch_assoc()) {
        echo "  . " . $c['Field'] . " (" . $c['Type'] . ")\n";
    }
}
?>
