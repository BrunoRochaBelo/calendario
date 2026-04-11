<?php
$conn = new mysqli('localhost', 'root', '', 'u596929139_calen');
$conn->query("DROP TABLE IF EXISTS sistema_config");
echo "Tabela sistema_config removida com sucesso.\n";
