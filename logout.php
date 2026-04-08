<?php
/**
 * ═════════════════════════════════════════════
 * PASCOM — Session Termination (v2.0)
 * Audit Logging · Secure Logout
 * ═════════════════════════════════════════════ */

require_once 'functions.php';

if (is_authenticated()) {
    logAction($conn, 'LOGOUT', 'usuarios', $_SESSION['usuario_id'], 'Sessão encerrada pelo usuário');
}

session_unset();
session_destroy();

header('Location: login.php?msg=Você saiu com segurança.');
exit();
?>
