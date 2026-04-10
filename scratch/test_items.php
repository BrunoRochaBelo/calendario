<?php
require 'config.php';
require 'functions.php';

$id = 13;
$items = getEventActivityItems($conn, $id, 1);
echo "Items for Activity 13:\n";
print_r($items);

$id = 10;
$items = getEventActivityItems($conn, $id, 1);
echo "Items for Activity 10:\n";
print_r($items);
