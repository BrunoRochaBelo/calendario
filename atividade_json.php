<?php
require_once 'functions.php';
requireLogin();

header('Content-Type: application/json; charset=UTF-8');

if (!ensureInscricoesTable($conn)) {
    json_response(false, 'Não foi possível preparar a estrutura de inscrições.');
}

$pid = current_paroquia_id();
$userId = (int)($_SESSION['usuario_id'] ?? 0);
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
$activityId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$baseSql = "
    SELECT
        a.id,
        a.nome,
        a.descricao,
        a.data_inicio,
        a.hora_inicio,
        a.data_fim,
        a.hora_fim,
        a.status,
        l.nome_local AS local_nome,
        t.cor,
        t.icone,
        t.nome_tipo,
        COUNT(DISTINCT i.id) AS total_inscritos,
        MAX(CASE WHEN i.usuario_id = ? THEN 1 ELSE 0 END) AS usuario_inscrito
    FROM atividades a
    LEFT JOIN locais_paroquia l ON a.local_id = l.id
    LEFT JOIN tipos_atividade t ON a.tipo_atividade_id = t.id
    LEFT JOIN inscricoes i ON i.atividade_id = a.id
    WHERE a.paroquia_id = ?
";

if ($activityId > 0) {
    $sql = $baseSql . " AND a.id = ? GROUP BY a.id LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iii', $userId, $pid, $activityId);
    $stmt->execute();
    $activity = $stmt->get_result()->fetch_assoc();

    if (!$activity) {
        json_response(false, 'Atividade não encontrada.');
    }

    $participants = [];
    $partsStmt = $conn->prepare("
        SELECT u.nome, i.data_inscricao
        FROM inscricoes i
        INNER JOIN usuarios u ON u.id = i.usuario_id
        WHERE i.atividade_id = ?
        ORDER BY u.nome ASC
    ");
    $partsStmt->bind_param('i', $activityId);
    $partsStmt->execute();
    $partsRes = $partsStmt->get_result();
    while ($row = $partsRes->fetch_assoc()) {
        $participants[] = $row;
    }

    $startTs = activityStartTimestamp($activity);
    $deadlineTs = $startTs - 86400;
    $now = time();

    json_response(true, '', [
        'activity' => [
            'id' => (int)$activity['id'],
            'nome' => $activity['nome'],
            'descricao' => $activity['descricao'],
            'data_inicio' => $activity['data_inicio'],
            'hora_inicio' => $activity['hora_inicio'],
            'data_fim' => $activity['data_fim'],
            'hora_fim' => $activity['hora_fim'],
            'local_nome' => $activity['local_nome'],
            'cor' => $activity['cor'],
            'icone' => $activity['icone'],
            'nome_tipo' => $activity['nome_tipo'],
            'total_inscritos' => (int)$activity['total_inscritos'],
            'usuario_inscrito' => (int)$activity['usuario_inscrito'] === 1,
            'can_interact' => canInteractWithActivity(),
            'can_cancel_after_deadline' => canBypassEnrollmentDeadline(),
            'can_cancel_now' => $now <= $deadlineTs || canBypassEnrollmentDeadline(),
            'deadline_message' => 'Somente usuários de nível 3 ou superior podem desistir com menos de 24 horas de antecedência.',
            'participants' => $participants,
        ]
    ]);
}

$sql = $baseSql . "
      AND (
        (MONTH(a.data_inicio) = ? AND YEAR(a.data_inicio) = ?)
        OR (a.data_fim IS NOT NULL AND MONTH(a.data_fim) = ? AND YEAR(a.data_fim) = ?)
      )
    GROUP BY a.id
    ORDER BY a.data_inicio ASC, a.hora_inicio ASC, a.nome ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('iiiiii', $userId, $pid, $month, $year, $month, $year);
$stmt->execute();
$res = $stmt->get_result();

$events = [];
while ($row = $res->fetch_assoc()) {
    $events[] = [
        'id' => (int)$row['id'],
        'nome' => $row['nome'],
        'titulo' => $row['nome'],
        'descricao' => $row['descricao'],
        'data_inicio' => $row['data_inicio'],
        'hora_inicio' => $row['hora_inicio'],
        'data_fim' => $row['data_fim'],
        'hora_fim' => $row['hora_fim'],
        'local_nome' => $row['local_nome'],
        'cor' => $row['cor'],
        'icone' => $row['icone'],
        'tipo_nome' => $row['nome_tipo'],
        'total_inscritos' => (int)$row['total_inscritos'],
        'usuario_inscrito' => (int)$row['usuario_inscrito'],
    ];
}

echo json_encode($events, JSON_UNESCAPED_UNICODE);
