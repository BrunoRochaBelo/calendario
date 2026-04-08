<?php
require_once 'config.php';
requireLogin();

header('Content-Type: application/json');

$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
$year  = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
$pid   = (int)($_SESSION['paroquia_id'] ?? 1);

// Granular Privacy Control (RBAC + Flag)
$priv_level = can('ver_restritos') ? 4 : 0;

$sql = "
    SELECT 
        e.id, e.titulo, e.descricao, e.data_inicio, e.data_fim, 
        l.nome as local_nome, te.cor, te.nome as tipo_nome,
        c.nome as categoria_nome, c.nivel_privacidade
    FROM eventos e
    LEFT JOIN locais l ON e.local_id = l.id
    LEFT JOIN tipos_evento te ON e.tipo_evento_id = te.id
    LEFT JOIN categorias_evento c ON e.categoria_id = c.id
    WHERE e.paroquia_id = ? 
      AND (
        (MONTH(e.data_inicio) = ? AND YEAR(e.data_inicio) = ?) OR
        (MONTH(e.data_fim) = ? AND YEAR(e.data_fim) = ?)
      )
      AND e.status = 'ativo'
      AND c.nivel_privacidade <= ?
    ORDER BY e.data_inicio ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('iiiiii', $pid, $month, $year, $month, $year, $priv_level);
$stmt->execute();
$res = $stmt->get_result();

$events = [];
while ($row = $res->fetch_assoc()) {
    $events[] = $row;
}

echo json_encode($events);