<?php
/**
 * PASCOM - Activity Deletion (v2.0)
 * Secure Deletion · Integrated Logging · Redirect
 */

require_once 'functions.php';
requirePerm('excluir_eventos');

$pid = current_paroquia_id();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: atividades.php?error=invalid_id');
    exit();
}

$stmt = $conn->prepare("SELECT nome, data_inicio FROM atividades WHERE id = ? AND paroquia_id = ? LIMIT 1");
$stmt->bind_param('ii', $id, $pid);
$stmt->execute();
$act = $stmt->get_result()->fetch_assoc();

if (!$act) {
    header('Location: atividades.php?error=not_found');
    exit();
}

$del = $conn->prepare("DELETE FROM atividades WHERE id = ? AND paroquia_id = ?");
$del->bind_param('ii', $id, $pid);

if ($del->execute()) {
    logAction($conn, 'EXCLUIR_ATIVIDADE', 'atividades', $id, $act['nome']);
    $eventMonth = (int)date('n', strtotime((string)$act['data_inicio']));
    $eventYear = (int)date('Y', strtotime((string)$act['data_inicio']));
    header('Location: index.php?m=' . $eventMonth . '&y=' . $eventYear . '&msg=' . urlencode('Atividade excluida com sucesso!') . '&refresh=1');
} else {
    header('Location: atividades.php?error=failed_deletion');
}

exit();
