<?php
require_once 'functions.php';
requireLogin();

$map = [
    'resumida' => 'analise_sistema_resumida.doc',
    'detalhada' => 'analise_sistema_detalhada.doc',
];

$id = strtolower(trim((string)($_GET['id'] ?? '')));
if (!isset($map[$id])) {
    http_response_code(404);
    die('Documento nao encontrado.');
}

$filename = $map[$id];
$path = __DIR__ . DIRECTORY_SEPARATOR . $filename;

if (!is_file($path)) {
    http_response_code(404);
    die('Arquivo nao encontrado.');
}

header('Content-Type: application/msword');
header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
header('Content-Length: ' . filesize($path));
header('X-Content-Type-Options: nosniff');
readfile($path);
exit();
