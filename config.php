<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Core Configuration & Security (v2.0)
 * Session Management · RBAC Layer · Global Helpers
 * ═══════════════════════════════════════════════════════ */

// 1. Session Security
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

// 2. Load Database Connection
require_once __DIR__ . '/conexao.php';

// 3. Global Security Headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");

// 4. Authentication Helpers
function is_authenticated(): bool {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

function requireLogin(): void {
    if (!is_authenticated()) {
        header('Location: login.php');
        exit();
    }

    global $conn;
    if (isset($conn) && $conn instanceof mysqli) {
        ensurePerfisHierarchyRemoved($conn);
    }
}

// 5. RBAC & Permissions Logic
function ensurePermissionColumns(mysqli $db): void {
    static $checked = false;
    if ($checked) {
        return;
    }
    $checked = true;

    $targets = [
        ['table' => 'usuarios', 'column' => 'perm_cadastrar_usuario'],
        ['table' => 'perfis', 'column' => 'perm_cadastrar_usuario'],
    ];

    foreach ($targets as $target) {
        $table = $target['table'];
        $column = $target['column'];
        $exists = $db->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
        if ($exists && $exists->num_rows > 0) {
            continue;
        }
        $db->query("ALTER TABLE `{$table}` ADD COLUMN `{$column}` TINYINT(1) NULL DEFAULT NULL AFTER `perm_ver_restritos`");
    }
}

function ensureUserPhotoColumn(mysqli $db): void {
    static $checked = false;
    if ($checked) {
        return;
    }
    $checked = true;

    $exists = $db->query("SHOW COLUMNS FROM `usuarios` LIKE 'foto_perfil'");
    if ($exists && $exists->num_rows > 0) {
        return;
    }

    $db->query("ALTER TABLE `usuarios` ADD COLUMN `foto_perfil` VARCHAR(255) NULL DEFAULT NULL AFTER `data_nascimento`");
}

function ensurePerfisHierarchyRemoved(mysqli $db): void {
    static $checked = false;
    if ($checked) {
        return;
    }
    $checked = true;

    $exists = $db->query("SHOW COLUMNS FROM `perfis` LIKE 'nivel_hierarquia'");
    if (!$exists || $exists->num_rows === 0) {
        return;
    }

    $db->query("ALTER TABLE `perfis` DROP COLUMN `nivel_hierarquia`");
}

function loadPermissions(mysqli $db, int $userId): array {
    ensurePermissionColumns($db);
    ensurePerfisHierarchyRemoved($db);

    $sql = "
        SELECT 
            COALESCE(u.perm_ver_calendario, p.perm_ver_calendario, 0) as ver_calendario,
            COALESCE(u.perm_criar_eventos, p.perm_criar_eventos, 0) as criar_eventos,
            COALESCE(u.perm_editar_eventos, p.perm_editar_eventos, 0) as editar_eventos,
            COALESCE(u.perm_excluir_eventos, p.perm_excluir_eventos, 0) as excluir_eventos,
            COALESCE(u.perm_ver_restritos, p.perm_ver_restritos, 0) as ver_restritos,
            COALESCE(u.perm_cadastrar_usuario, p.perm_cadastrar_usuario, 0) as cadastrar_usuario,
            COALESCE(u.perm_admin_usuarios, p.perm_admin_usuarios, 0) as admin_usuarios,
            COALESCE(u.perm_admin_sistema, p.perm_admin_sistema, 0) as admin_sistema,
            COALESCE(u.perm_ver_logs, p.perm_ver_logs, 0) as ver_logs
        FROM usuarios u
        LEFT JOIN perfis p ON u.perfil_id = p.id
        WHERE u.id = ?
    ";
    
    $stmt = $db->prepare($sql);
    if (!$stmt) return [];
    
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: [];
}

function can(string $permission): bool {
    return isset($_SESSION['perms'][$permission]) && (bool)$_SESSION['perms'][$permission] === true;
}

function has_level(int $min_level): bool {
    return isset($_SESSION['usuario_nivel']) && (int)$_SESSION['usuario_nivel'] <= $min_level;
}

function requirePerm(string $permission): void {
    requireLogin();
    if (has_level(0)) {
        return;
    }
    if (!can($permission)) {
        header('Location: index.php?error=unauthorized');
        exit();
    }
}

// 6. Global Utility Helpers
function h(mixed $v): string {
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

function json_response(bool $success, string $message = '', array $data = []): void {
    header('Content-Type: application/json');
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    exit();
}

/**
 * Centralized logging function
 */
function logAction(mysqli $db, string $acao, string $tabela = '', int $regId = 0, mixed $detalhes = ''): void {
    $uid = $_SESSION['usuario_id'] ?? null;
    $parish_id = $_SESSION['paroquia_id'] ?? null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
    
    // Auto-serialize arrays for detailed state logs
    if (is_array($detalhes) || is_object($detalhes)) {
        $detalhes = json_encode($detalhes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    
    $sql = "INSERT INTO log_alteracoes (usuario_id, acao, tabela_afetada, registro_id, detalhes_alteracao, paroquia_id, ip_origem) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('issisis', $uid, $acao, $tabela, $regId, $detalhes, $parish_id, $ip);
        $stmt->execute();
    }
}
?>
