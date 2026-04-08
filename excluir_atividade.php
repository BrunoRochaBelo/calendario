<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Activity Deletion (v2.0)
 * Secure Deletion · Integrated Logging · Redirect
 * ═══════════════════════════════════════════════════════ */

require_once 'functions.php';
requirePerm('excluir_eventos');

$pid = current_paroquia_id();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: atividades.php?error=invalid_id');
    exit();
}

// 1. Fetch data for logging BEFORE deletion
$stmt = $conn->prepare("SELECT nome FROM atividades WHERE id = ? AND paroquia_id = ? LIMIT 1");
$stmt->bind_param('ii', $id, $pid);
$stmt->execute();
$act = $stmt->get_result()->fetch_assoc();

if (!$act) {
    header('Location: atividades.php?error=not_found');
    exit();
}

// 2. Perform Deletion
$del = $conn->prepare("DELETE FROM atividades WHERE id = ? AND paroquia_id = ?");
$del->bind_param('ii', $id, $pid);

if ($del->execute()) {
    logAction($conn, 'EXCLUIR_ATIVIDADE', 'atividades', $id, $act['nome']);
    header('Location: atividades.php?msg=Atividade excluída com sucesso!');
} else {
    header('Location: atividades.php?error=failed_deletion');
}
exit();
?>
