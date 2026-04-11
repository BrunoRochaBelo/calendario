<?php
$conn = new mysqli('localhost', 'root', '', 'u596929139_calen');
$res = $conn->query("SHOW TABLES");
while ($row = $res->fetch_array()) {
    echo $row[0] . "\n";
}
