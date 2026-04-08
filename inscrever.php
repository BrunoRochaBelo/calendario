<?php
require_once 'functions.php';
requireLogin();

if (!ensureInscricoesTable($conn)) {
    json_response(false, 'Não foi possível preparar a estrutura de inscrições.');
}

$aid = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
$action = strtolower(trim($_POST['action'] ?? $_GET['action'] ?? ''));
$userId = (int)($_SESSION['usuario_id'] ?? 0);
$pid = current_paroquia_id();

$isAjax = (
    (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
    (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
);

if ($aid <= 0 || !in_array($action, ['join', 'leave'], true)) {
    if ($isAjax) {
        json_response(false, 'Requisição inválida.');
    }
    header('Location: index.php?error=invalid_request');
    exit();
}

$stmt = $conn->prepare("
    SELECT a.id, a.nome, a.data_inicio, a.hora_inicio, a.paroquia_id
    FROM atividades a
    WHERE a.id = ? AND a.paroquia_id = ?
    LIMIT 1
");
$stmt->bind_param('ii', $aid, $pid);
$stmt->execute();
$activity = $stmt->get_result()->fetch_assoc();

if (!$activity) {
    if ($isAjax) {
        json_response(false, 'Atividade não encontrada.');
    }
    header('Location: index.php?error=activity_not_found');
    exit();
}

if ($action === 'join') {
    $insert = $conn->prepare("
        INSERT INTO inscricoes (atividade_id, usuario_id, data_inscricao)
        VALUES (?, ?, NOW())
        ON DUPLICATE KEY UPDATE data_inscricao = data_inscricao
    ");
    $insert->bind_param('ii', $aid, $userId);
    $ok = $insert->execute();

    if ($ok) {
        logAction($conn, 'INSCREVER_ATIVIDADE', 'inscricoes', $aid, [
            'atividade_id' => $aid,
            'usuario_id' => $userId,
        ]);
        if ($isAjax) {
            json_response(true, 'Inscrição realizada com sucesso.');
        }
        header('Location: ver_atividade.php?id=' . $aid . '&msg=' . urlencode('Inscrição realizada com sucesso.'));
        exit();
    }

    if ($isAjax) {
        json_response(false, 'Não foi possível concluir a inscrição.');
    }
    header('Location: ver_atividade.php?id=' . $aid . '&error=' . urlencode('Não foi possível concluir a inscrição.'));
    exit();
}

$check = $conn->prepare("SELECT id FROM inscricoes WHERE atividade_id = ? AND usuario_id = ? LIMIT 1");
$check->bind_param('ii', $aid, $userId);
$check->execute();
$existing = $check->get_result()->fetch_assoc();

if (!$existing) {
    if ($isAjax) {
        json_response(false, 'Você não está inscrito nesta atividade.');
    }
    header('Location: ver_atividade.php?id=' . $aid . '&error=' . urlencode('Você não está inscrito nesta atividade.'));
    exit();
}

$deadlineTs = activityStartTimestamp($activity) - 86400;
if (time() > $deadlineTs && !canBypassEnrollmentDeadline()) {
    $message = 'Somente usuários de nível 3 ou superior podem desistir com menos de 24 horas de antecedência.';
    if ($isAjax) {
        json_response(false, $message);
    }
    header('Location: ver_atividade.php?id=' . $aid . '&error=' . urlencode($message));
    exit();
}

$delete = $conn->prepare("DELETE FROM inscricoes WHERE atividade_id = ? AND usuario_id = ?");
$delete->bind_param('ii', $aid, $userId);
$ok = $delete->execute();

if ($ok) {
    logAction($conn, 'CANCELAR_INSCRICAO_ATIVIDADE', 'inscricoes', $aid, [
        'atividade_id' => $aid,
        'usuario_id' => $userId,
    ]);
    if ($isAjax) {
        json_response(true, 'Participação cancelada com sucesso.');
    }
    header('Location: ver_atividade.php?id=' . $aid . '&msg=' . urlencode('Participação cancelada com sucesso.'));
    exit();
}

if ($isAjax) {
    json_response(false, 'Não foi possível cancelar a participação.');
}

header('Location: ver_atividade.php?id=' . $aid . '&error=' . urlencode('Não foi possível cancelar a participação.'));
