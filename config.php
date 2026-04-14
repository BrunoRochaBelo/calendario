<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Core Configuration & Security (v2.0)
 * Session Management · Global Includes
 * ═══════════════════════════════════════════════════════ */

// 1. Session & Environment Security
date_default_timezone_set('America/Sao_Paulo');
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
// SameSite ajuda contra CSRF basico; Secure so quando estiver em HTTPS
$is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ((int)($_SERVER['SERVER_PORT'] ?? 0) === 443);
ini_set('session.cookie_samesite', 'Lax');
if ($is_https) {
    ini_set('session.cookie_secure', 1);
}
session_start();

// 2. Load Database Connection
require_once __DIR__ . '/conexao.php';

// 2.1. Schema mutations guard
// Nao executar ALTER/CREATE/DROP automaticamente em runtime.
// Ajustes de schema devem ser feitos manualmente no banco.
if (!defined('DB_SCHEMA_MUTATIONS_ENABLED')) {
    define('DB_SCHEMA_MUTATIONS_ENABLED', false);
}

// 3. Global Security Headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");

// 4. Módulos da Aplicação (Extratos do antigo functions.php / config.php)
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/schema.php';
require_once __DIR__ . '/includes/auth_context.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/rbac.php';
require_once __DIR__ . '/includes/groups.php';
require_once __DIR__ . '/includes/notifications.php';
require_once __DIR__ . '/includes/activities.php';
