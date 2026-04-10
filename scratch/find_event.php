<?php
require 'config.php';
$q = $conn->query("SELECT id, nome FROM atividades WHERE nome LIKE '%tri%' OR nome LIKE '%10%' OR id = 10");
while($r = $q->fetch_assoc()) {
    print_r($r);
}
