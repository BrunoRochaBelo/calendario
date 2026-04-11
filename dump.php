<?php
require_once 'functions.php';
requireLogin();
requirePerm('admin_sistema');
require 'conexao.php';
$res = $conn->query('SHOW TABLES'); 
$out = "";
while($row = $res->fetch_row()){ 
    $out .= "Table: {$row[0]}\n"; 
    $res2 = $conn->query("SHOW CREATE TABLE {$row[0]}"); 
    if($res2) {
        $row2 = $res2->fetch_row(); 
        $out .= $row2[1]."\n\n";
    }
}
file_put_contents('schema.txt', $out);
?>
