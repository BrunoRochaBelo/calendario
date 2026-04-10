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
        ensureUserPermissionsMaterialized($conn);
        ensureWorkingGroupsTables($conn);
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

function ensureUserPermissionColumns(mysqli $db): void {
    static $checked = false;
    if ($checked) {
        return;
    }
    $checked = true;

    $cols = [
        'perm_ver_calendario' => "TINYINT(1) NULL DEFAULT NULL",
        'perm_criar_eventos' => "TINYINT(1) NULL DEFAULT NULL",
        'perm_editar_eventos' => "TINYINT(1) NULL DEFAULT NULL",
        'perm_excluir_eventos' => "TINYINT(1) NULL DEFAULT NULL",
        'perm_ver_restritos' => "TINYINT(1) NULL DEFAULT NULL",
        'perm_cadastrar_usuario' => "TINYINT(1) NULL DEFAULT NULL",
        'perm_admin_usuarios' => "TINYINT(1) NULL DEFAULT NULL",
        'perm_admin_sistema' => "TINYINT(1) NULL DEFAULT NULL",
        'perm_ver_logs' => "TINYINT(1) NULL DEFAULT NULL",
        'perm_gerenciar_catalogo' => "TINYINT(1) NULL DEFAULT NULL",
        'perm_gerenciar_grupos' => "TINYINT(1) NULL DEFAULT NULL",
    ];

    foreach ($cols as $col => $def) {
        $exists = $db->query("SHOW COLUMNS FROM `usuarios` LIKE '{$col}'");
        if ($exists && $exists->num_rows > 0) {
            continue;
        }
        $db->query("ALTER TABLE `usuarios` ADD COLUMN `{$col}` {$def}");
    }
}

function ensureUserProfileNameColumn(mysqli $db): void {
    static $checked = false;
    if ($checked) {
        return;
    }
    $checked = true;

    $exists = $db->query("SHOW COLUMNS FROM `usuarios` LIKE 'perfil_nome'");
    if ($exists && $exists->num_rows > 0) {
        return;
    }

    $db->query("ALTER TABLE `usuarios` ADD COLUMN `perfil_nome` VARCHAR(50) NULL DEFAULT NULL");
}

function ensureUserPermissionsMaterialized(mysqli $db): void {
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    ensureUserPermissionColumns($db);
    ensureUserProfileNameColumn($db);

    // Normaliza NULL -> 0 (permissões devem existir na tabela usuarios)
    $db->query("
        UPDATE usuarios
        SET
            perm_ver_calendario = COALESCE(perm_ver_calendario, 0),
            perm_criar_eventos  = COALESCE(perm_criar_eventos, 0),
            perm_editar_eventos = COALESCE(perm_editar_eventos, 0),
            perm_excluir_eventos= COALESCE(perm_excluir_eventos, 0),
            perm_ver_restritos  = COALESCE(perm_ver_restritos, 0),
            perm_cadastrar_usuario = COALESCE(perm_cadastrar_usuario, 0),
            perm_admin_usuarios = COALESCE(perm_admin_usuarios, 0),
            perm_admin_sistema  = COALESCE(perm_admin_sistema, 0),
            perm_ver_logs       = COALESCE(perm_ver_logs, 0),
            perm_gerenciar_catalogo = COALESCE(perm_gerenciar_catalogo, 0),
            perm_gerenciar_grupos = COALESCE(perm_gerenciar_grupos, 0)
    ");
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

function ensureUserLastLoginColumn(mysqli $db): void {
    static $checked = false;
    if ($checked) {
        return;
    }
    $checked = true;

    $exists = $db->query("SHOW COLUMNS FROM `usuarios` LIKE 'ultimo_login'");
    if ($exists && $exists->num_rows > 0) {
        return;
    }

    $db->query("ALTER TABLE `usuarios` ADD COLUMN `ultimo_login` TIMESTAMP NULL DEFAULT NULL");
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
    ensureUserPermissionsMaterialized($db);
    ensurePerfisHierarchyRemoved($db);

    $sql = "
        SELECT 
            COALESCE(u.perm_ver_calendario, 0) as ver_calendario,
            COALESCE(u.perm_criar_eventos, 0) as criar_eventos,
            COALESCE(u.perm_editar_eventos, 0) as editar_eventos,
            COALESCE(u.perm_excluir_eventos, 0) as excluir_eventos,
            COALESCE(u.perm_ver_restritos, 0) as ver_restritos,
            COALESCE(u.perm_cadastrar_usuario, 0) as cadastrar_usuario,
            COALESCE(u.perm_admin_usuarios, 0) as admin_usuarios,
            COALESCE(u.perm_admin_sistema, 0) as admin_sistema,
            COALESCE(u.perm_ver_logs, 0) as ver_logs,
            COALESCE(u.perm_gerenciar_catalogo, 0) as gerenciar_catalogo,
            COALESCE(u.perm_gerenciar_grupos, 0) as gerenciar_grupos
        FROM usuarios u
        WHERE u.id = ?
    ";
    
    $stmt = $db->prepare($sql);
    if (!$stmt) return [];
    
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: [];
}

function can(string $permission): bool {
    if (has_level(0) || ($_SESSION['usuario_id'] ?? 0) === 1) {
        return true;
    }
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

function ensureAuthThrottleTable(mysqli $db): void {
    static $checked = false;
    if ($checked) {
        return;
    }
    $checked = true;

    $db->query("
        CREATE TABLE IF NOT EXISTS auth_throttle (
            id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            scope VARCHAR(50) NOT NULL,
            identifier VARCHAR(191) NOT NULL,
            attempts TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
            locked_until DATETIME NULL DEFAULT NULL,
            last_attempt_at DATETIME NULL DEFAULT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_scope_identifier (scope, identifier)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}

function authThrottleIdentifier(string $value): string {
    $normalized = mb_strtolower(trim($value));
    return $normalized !== '' ? $normalized : 'unknown';
}

function authThrottleKey(string $scope, string $identifier): string {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
    return authThrottleIdentifier($scope) . '|' . authThrottleIdentifier($identifier) . '|' . $ip;
}

function authThrottleSecondsLeft(?string $lockedUntil): int {
    if (empty($lockedUntil)) {
        return 0;
    }
    $ts = strtotime((string)$lockedUntil);
    if ($ts === false) {
        return 0;
    }
    $diff = $ts - time();
    return $diff > 0 ? $diff : 0;
}

function authThrottleState(mysqli $db, string $scope, string $identifier, int $maxAttempts = 3): array {
    ensureAuthThrottleTable($db);

    $scope = authThrottleIdentifier($scope);
    $identifier = authThrottleIdentifier($identifier);
    $stmt = $db->prepare('SELECT attempts, locked_until FROM auth_throttle WHERE scope = ? AND identifier = ? LIMIT 1');
    if (!$stmt) {
        return ['allowed' => true, 'attempts' => 0, 'remaining' => $maxAttempts, 'locked_until' => null, 'seconds_left' => 0];
    }

    $stmt->bind_param('ss', $scope, $identifier);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if (!$row) {
        return ['allowed' => true, 'attempts' => 0, 'remaining' => $maxAttempts, 'locked_until' => null, 'seconds_left' => 0];
    }

    $attempts = (int)($row['attempts'] ?? 0);
    $lockedUntil = trim((string)($row['locked_until'] ?? ''));
    $secondsLeft = authThrottleSecondsLeft($lockedUntil ?: null);

    if ($secondsLeft > 0) {
        return [
            'allowed' => false,
            'attempts' => $attempts,
            'remaining' => 0,
            'locked_until' => $lockedUntil,
            'seconds_left' => $secondsLeft,
        ];
    }

    return [
        'allowed' => true,
        'attempts' => $attempts,
        'remaining' => max(0, $maxAttempts - $attempts),
        'locked_until' => null,
        'seconds_left' => 0,
    ];
}

function authThrottleRegisterFailure(mysqli $db, string $scope, string $identifier, int $maxAttempts = 3, int $lockMinutes = 5): array {
    ensureAuthThrottleTable($db);

    $scope = authThrottleIdentifier($scope);
    $identifier = authThrottleIdentifier($identifier);
    $state = authThrottleState($db, $scope, $identifier, $maxAttempts);

    if (!$state['allowed']) {
        return $state;
    }

    $attempts = min($maxAttempts, ((int)$state['attempts']) + 1);
    $lockedUntil = null;
    if ($attempts >= $maxAttempts) {
        $lockedUntil = date('Y-m-d H:i:s', time() + ($lockMinutes * 60));
    }

    $stmt = $db->prepare('SELECT id FROM auth_throttle WHERE scope = ? AND identifier = ? LIMIT 1');
    if (!$stmt) {
        return [
            'allowed' => $attempts < $maxAttempts,
            'attempts' => $attempts,
            'remaining' => max(0, $maxAttempts - $attempts),
            'locked_until' => $lockedUntil,
            'seconds_left' => authThrottleSecondsLeft($lockedUntil),
        ];
    }

    $stmt->bind_param('ss', $scope, $identifier);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    $scopeEsc = $db->real_escape_string($scope);
    $identifierEsc = $db->real_escape_string($identifier);

    if ($row) {
        $id = (int)$row['id'];
        $lockedSql = $lockedUntil ? "'" . $db->real_escape_string($lockedUntil) . "'" : 'NULL';
        $db->query("UPDATE auth_throttle SET attempts = {$attempts}, locked_until = {$lockedSql}, last_attempt_at = NOW() WHERE id = {$id}");
    } else {
        $lockedSql = $lockedUntil ? "'" . $db->real_escape_string($lockedUntil) . "'" : 'NULL';
        $db->query("INSERT INTO auth_throttle (scope, identifier, attempts, locked_until, last_attempt_at) VALUES ('{$scopeEsc}', '{$identifierEsc}', {$attempts}, {$lockedSql}, NOW())");
    }

    return [
        'allowed' => $attempts < $maxAttempts,
        'attempts' => $attempts,
        'remaining' => max(0, $maxAttempts - $attempts),
        'locked_until' => $lockedUntil,
        'seconds_left' => authThrottleSecondsLeft($lockedUntil),
    ];
}

function authThrottleReset(mysqli $db, string $scope, string $identifier): void {
    ensureAuthThrottleTable($db);

    $scope = authThrottleIdentifier($scope);
    $identifier = authThrottleIdentifier($identifier);
    $stmt = $db->prepare('DELETE FROM auth_throttle WHERE scope = ? AND identifier = ?');
    if ($stmt) {
        $stmt->bind_param('ss', $scope, $identifier);
        $stmt->execute();
    }
}

function ensureWorkingGroupsTables(mysqli $db): void {
    static $checked = false;
    if ($checked) return;
    $checked = true;

    // Table for Groups
    $db->query("
        CREATE TABLE IF NOT EXISTS grupos_trabalho (
            id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            paroquia_id INT(10) UNSIGNED NOT NULL,
            nome VARCHAR(100) NOT NULL,
            descricao TEXT NULL,
            cor VARCHAR(7) DEFAULT '#3b82f6',
            ativo TINYINT(1) NOT NULL DEFAULT 1,
            visivel TINYINT(1) NOT NULL DEFAULT 1,
            data_criacao TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY fk_grupo_paroquia (paroquia_id),
            CONSTRAINT fk_grupo_paroquia FOREIGN KEY (paroquia_id) REFERENCES paroquias (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Table for N:N Relationship
    $db->query("
        CREATE TABLE IF NOT EXISTS usuario_grupos (
            usuario_id INT(10) UNSIGNED NOT NULL,
            grupo_id INT(10) UNSIGNED NOT NULL,
            data_atribuicao TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (usuario_id, grupo_id),
            KEY fk_ug_usuario (usuario_id),
            KEY fk_ug_grupo (grupo_id),
            CONSTRAINT fk_ug_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE,
            CONSTRAINT fk_ug_grupo FOREIGN KEY (grupo_id) REFERENCES grupos_trabalho (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}
?>
