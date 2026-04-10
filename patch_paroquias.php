<?php
$f = 'c:/xampp/htdocs/calender/paroquias.php';
$c = file_get_contents($f);
$c = str_replace(
    'onclick=\'editPq(<?= json_encode($row) ?>)\'',
    'onclick="editPq(<?= htmlspecialchars(json_encode($row), ENT_QUOTES, \'UTF-8\') ?>)"',
    $c
);
$c = str_replace("classList.add('show')", "classList.add('active')", $c);
$c = str_replace("classList.remove('show')", "classList.remove('active')", $c);
file_put_contents($f, $c);
echo "Paroquias Fixed!";
