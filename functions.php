<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Global Utility Functions (v2.0)
 * Date Formatting · Sanity Checks · Parish Helpers
 * ═══════════════════════════════════════════════════════ */

require_once __DIR__ . '/config.php';

/**
 * Formata uma data para o padrão brasileiro (d/m/Y)
 */
function formatDate(string $date): string {
    if (empty($date)) return '—';
    try {
        $dt = new DateTime($date);
        return $dt->format('d/m/Y');
    } catch (Exception $e) {
        return h($date);
    }
}

/**
 * Formata um horário para o padrão (H:i)
 */
function formatTime(?string $time): string {
    if (empty($time)) return '00:00';
    return substr((string)$time, 0, 5);
}

/**
 * Retorna o nível de acesso do usuário atual de forma legível
 */
function getAccessLabel(int $level): string {
    $labels = [
        0 => 'Master',
        1 => 'Gerente',
        2 => 'Usuário',
        3 => 'Visitante'
    ];
    return $labels[$level] ?? 'Desconhecido';
}

/**
 * Retorna o ID da paróquia atual da sessão
 */
function current_paroquia_id(): int {
    return (int)($_SESSION['paroquia_id'] ?? 0);
}

function userCanSwitchParish(): bool {
    return (bool)(
        ($_SESSION['usuario_id'] ?? 0) === 1 ||
        can('admin_sistema') ||
        (int)($_SESSION['usuario_nivel'] ?? -1) === 0
    );
}

function canInteractWithActivity(): bool {
    return is_authenticated() && can('ver_calendario');
}

function canBypassEnrollmentDeadline(): bool {
    return (bool)(
        can('admin_sistema') ||
        ($_SESSION['usuario_id'] ?? 0) === 1 ||
        (int)($_SESSION['usuario_nivel'] ?? 0) >= 3
    );
}

function ensureInscricoesTable(mysqli $db): bool {
    static $checked = false;

    if ($checked) {
        return true;
    }

    $checked = true;
    $exists = $db->query("SHOW TABLES LIKE 'inscricoes'");
    if ($exists && $exists->num_rows > 0) {
        return true;
    }

    $sql = "
        CREATE TABLE inscricoes (
            id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            atividade_id INT(10) UNSIGNED NOT NULL,
            usuario_id INT(10) UNSIGNED NOT NULL,
            data_inscricao TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_inscricao_atividade_usuario (atividade_id, usuario_id),
            KEY fk_inscricao_atividade (atividade_id),
            KEY fk_inscricao_usuario (usuario_id),
            CONSTRAINT fk_inscricao_atividade FOREIGN KEY (atividade_id) REFERENCES atividades (id) ON DELETE CASCADE,
            CONSTRAINT fk_inscricao_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    return (bool)$db->query($sql);
}

function ensureEventActivitiesStructure(mysqli $db): bool {
    static $checked = false;

    if ($checked) {
        return true;
    }

    $checked = true;

    $catalogSql = "
        CREATE TABLE IF NOT EXISTS atividades_catalogo (
            id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            paroquia_id INT(10) UNSIGNED NOT NULL,
            nome VARCHAR(150) NOT NULL,
            descricao TEXT NULL,
            ativo TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_atividade_catalogo_nome (paroquia_id, nome),
            KEY fk_atividade_catalogo_paroquia (paroquia_id),
            CONSTRAINT fk_atividade_catalogo_paroquia FOREIGN KEY (paroquia_id) REFERENCES paroquias (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $itemsSql = "
        CREATE TABLE IF NOT EXISTS atividade_evento_itens (
            id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            evento_id INT(10) UNSIGNED NOT NULL,
            atividade_catalogo_id INT(10) UNSIGNED NOT NULL,
            ordem INT(10) UNSIGNED NOT NULL DEFAULT 0,
            data_criacao TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            ultima_atualizacao TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_evento_atividade_catalogo (evento_id, atividade_catalogo_id),
            KEY fk_atividade_evento_item_evento (evento_id),
            KEY fk_atividade_evento_item_catalogo (atividade_catalogo_id),
            CONSTRAINT fk_atividade_evento_item_evento FOREIGN KEY (evento_id) REFERENCES atividades (id) ON DELETE CASCADE,
            CONSTRAINT fk_atividade_evento_item_catalogo FOREIGN KEY (atividade_catalogo_id) REFERENCES atividades_catalogo (id) ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $enrollSql = "
        CREATE TABLE IF NOT EXISTS atividade_evento_inscricoes (
            id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            evento_item_id INT(10) UNSIGNED NOT NULL,
            usuario_id INT(10) UNSIGNED NOT NULL,
            data_inscricao TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_evento_item_usuario (evento_item_id, usuario_id),
            KEY fk_atividade_evento_inscricao_item (evento_item_id),
            KEY fk_atividade_evento_inscricao_usuario (usuario_id),
            CONSTRAINT fk_atividade_evento_inscricao_item FOREIGN KEY (evento_item_id) REFERENCES atividade_evento_itens (id) ON DELETE CASCADE,
            CONSTRAINT fk_atividade_evento_inscricao_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $ok = (bool)$db->query($catalogSql) && (bool)$db->query($itemsSql) && (bool)$db->query($enrollSql);

    // Remove timestamp columns from atividades_catalogo if they exist (cleanup)
    $colCheck = $db->query("SHOW COLUMNS FROM `atividades_catalogo` LIKE 'data_criacao'");
    if ($colCheck && $colCheck->num_rows > 0) {
        $db->query("ALTER TABLE `atividades_catalogo` DROP COLUMN `data_criacao`");
    }
    $colCheck2 = $db->query("SHOW COLUMNS FROM `atividades_catalogo` LIKE 'ultima_atualizacao'");
    if ($colCheck2 && $colCheck2->num_rows > 0) {
        $db->query("ALTER TABLE `atividades_catalogo` DROP COLUMN `ultima_atualizacao`");
    }

    return $ok;
}

function seedDefaultEventActivities(mysqli $db, int $paroquiaId): void {
    if ($paroquiaId <= 0 || !ensureEventActivitiesStructure($db)) {
        return;
    }

    $check = $db->prepare("SELECT id FROM atividades_catalogo WHERE paroquia_id = ? LIMIT 1");
    if (!$check) {
        return;
    }
    $check->bind_param('i', $paroquiaId);
    $check->execute();
    $exists = $check->get_result()->fetch_assoc();
    $check->close();

    if ($exists) {
        return;
    }

    $defaults = [
        ['Liturgia', 'Organização litúrgica do evento'],
        ['Leitura', 'Leitores e proclamadores'],
        ['Canto', 'Equipe de música e canto'],
        ['Acolhida', 'Recepção e apoio aos participantes'],
        ['Comunicação', 'Cobertura, avisos e apoio da PASCOM'],
    ];

    $insert = $db->prepare("INSERT INTO atividades_catalogo (paroquia_id, nome, descricao) VALUES (?, ?, ?)");
    if (!$insert) {
        return;
    }

    foreach ($defaults as [$nome, $descricao]) {
        $insert->bind_param('iss', $paroquiaId, $nome, $descricao);
        $insert->execute();
    }
    $insert->close();
}

function getEventActivityCatalog(mysqli $db, int $paroquiaId): array {
    if ($paroquiaId <= 0 || !ensureEventActivitiesStructure($db)) {
        return [];
    }

    seedDefaultEventActivities($db, $paroquiaId);

    $stmt = $db->prepare("
        SELECT id, nome, descricao
        FROM atividades_catalogo
        WHERE paroquia_id = ? AND ativo = 1
        ORDER BY nome ASC
    ");
    if (!$stmt) {
        return [];
    }

    $stmt->bind_param('i', $paroquiaId);
    $stmt->execute();
    $result = $stmt->get_result();
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    $stmt->close();

    return $items;
}

function normalizeEventActivityCatalogIds(mixed $rawIds): array {
    if (!is_array($rawIds)) {
        return [];
    }

    $normalized = [];
    foreach ($rawIds as $rawId) {
        $id = (int)$rawId;
        if ($id > 0) {
            $normalized[] = $id;
        }
    }

    return array_values(array_unique($normalized));
}

function saveEventActivityItems(mysqli $db, int $eventoId, int $paroquiaId, mixed $rawIds): void {
    if ($eventoId <= 0 || $paroquiaId <= 0 || !ensureEventActivitiesStructure($db)) {
        return;
    }

    $ids = normalizeEventActivityCatalogIds($rawIds);

    $delete = $db->prepare("DELETE FROM atividade_evento_itens WHERE evento_id = ?");
    if ($delete) {
        $delete->bind_param('i', $eventoId);
        $delete->execute();
        $delete->close();
    }

    if (!$ids) {
        return;
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids) + 1);
    $params = array_merge([$paroquiaId], $ids);

    $sql = "
        SELECT id
        FROM atividades_catalogo
        WHERE paroquia_id = ? AND ativo = 1 AND id IN ($placeholders)
        ORDER BY nome ASC
    ";
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        return;
    }

    $bindValues = [];
    $bindValues[] = &$types;
    foreach ($params as $key => $value) {
        $bindValues[] = &$params[$key];
    }
    call_user_func_array([$stmt, 'bind_param'], $bindValues);
    $stmt->execute();
    $result = $stmt->get_result();

    $validIds = [];
    while ($row = $result->fetch_assoc()) {
        $validIds[] = (int)$row['id'];
    }
    $stmt->close();

    if (!$validIds) {
        return;
    }

    $insert = $db->prepare("
        INSERT INTO atividade_evento_itens (evento_id, atividade_catalogo_id, ordem)
        VALUES (?, ?, ?)
    ");
    if (!$insert) {
        return;
    }

    foreach ($ids as $ordem => $catalogId) {
        if (!in_array($catalogId, $validIds, true)) {
            continue;
        }
        $posicao = $ordem + 1;
        $insert->bind_param('iii', $eventoId, $catalogId, $posicao);
        $insert->execute();
    }
    $insert->close();
}

function getEventActivityItems(mysqli $db, int $eventoId, int $usuarioId = 0): array {
    if ($eventoId <= 0 || !ensureEventActivitiesStructure($db)) {
        return [];
    }

    $stmt = $db->prepare("
        SELECT
            ei.id,
            ei.atividade_catalogo_id,
            ei.ordem,
            ac.nome,
            ac.descricao,
            (
                SELECT COUNT(*)
                FROM atividade_evento_inscricoes aei_count
                WHERE aei_count.evento_item_id = ei.id
            ) AS total_inscritos,
            EXISTS(
                SELECT 1
                FROM atividade_evento_inscricoes aei_user
                WHERE aei_user.evento_item_id = ei.id AND aei_user.usuario_id = ?
            ) AS usuario_inscrito
        FROM atividade_evento_itens ei
        INNER JOIN atividades_catalogo ac ON ac.id = ei.atividade_catalogo_id
        WHERE ei.evento_id = ?
        ORDER BY ei.ordem ASC, ac.nome ASC
    ");
    if (!$stmt) {
        return [];
    }

    $stmt->bind_param('ii', $usuarioId, $eventoId);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    $ids = [];
    while ($row = $result->fetch_assoc()) {
        $row['id'] = (int)$row['id'];
        $row['atividade_catalogo_id'] = (int)$row['atividade_catalogo_id'];
        $row['ordem'] = (int)$row['ordem'];
        $row['total_inscritos'] = (int)$row['total_inscritos'];
        $row['usuario_inscrito'] = (int)$row['usuario_inscrito'] === 1;
        $row['participants'] = [];
        $items[$row['id']] = $row;
        $ids[] = $row['id'];
    }
    $stmt->close();

    if (!$ids) {
        return [];
    }

    $participantIds = implode(',', array_map('intval', $ids));
    $participantsRes = $db->query("
        SELECT aei.evento_item_id, u.nome, u.foto_perfil
        FROM atividade_evento_inscricoes aei
        INNER JOIN usuarios u ON u.id = aei.usuario_id
        WHERE aei.evento_item_id IN ($participantIds)
        ORDER BY u.nome ASC
    ");

    if ($participantsRes) {
        while ($participant = $participantsRes->fetch_assoc()) {
            $itemId = (int)$participant['evento_item_id'];
            if (isset($items[$itemId])) {
                $items[$itemId]['participants'][] = [
                    'nome' => $participant['nome'],
                    'foto_perfil' => $participant['foto_perfil'],
                ];
            }
        }
    }

    return array_values($items);
}

function activityStartTimestamp(array $activity): int {
    $date = $activity['data_inicio'] ?? date('Y-m-d');
    $time = $activity['hora_inicio'] ?? '00:00:00';
    return strtotime(trim($date . ' ' . $time));
}

/**
 * Sanitiza inputs de formulário em massa
 */
function sanitize_post(array $data): array {
    $sanitized = [];
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $sanitized[$key] = sanitize_post($value);
        } else {
            $sanitized[$key] = trim($value);
        }
    }
    return $sanitized;
}

/**
 * Gera um alerta HTML estilizado para o sistema
 */
function alert(string $type, string $message): string {
    $colors = [
        'success' => 'rgba(34, 197, 94, 0.1), #22c55e',
        'error'   => 'rgba(239, 68, 68, 0.1), #ef4444',
        'info'    => 'rgba(59, 130, 246, 0.1), #3b82f6'
    ];
    $style = $colors[$type] ?? $colors['info'];
    list($bg, $border) = explode(', ', $style);
    
    return "<div style='background:{$bg}; border:1px solid {$border}; color:{$border}; padding:1rem; border-radius:var(--r-md); margin-bottom:1.5rem; font-size:0.85rem; font-weight:700; text-align:center;'>{$message}</div>";
}

/**
 * Retorna todos os grupos de trabalho de uma paróquia
 */
function getWorkingGroups(mysqli $db, int $paroquiaId, bool $incluirInativos = false): array {
    $sql = "SELECT * FROM grupos_trabalho WHERE paroquia_id = ? " . ($incluirInativos ? "" : "AND ativo = 1") . " ORDER BY nome ASC";
    $stmt = $db->prepare($sql);
    if (!$stmt) return [];
    $stmt->bind_param('i', $paroquiaId);
    $stmt->execute();
    $res = $stmt->get_result();
    $grupos = [];
    while ($row = $res->fetch_assoc()) {
        $grupos[] = $row;
    }
    return $grupos;
}

/**
 * Retorna os IDs dos grupos aos quais um usuário pertence
 */
function getUserGroups(mysqli $db, int $userId): array {
    $sql = "SELECT grupo_id FROM usuario_grupos WHERE usuario_id = ?";
    $stmt = $db->prepare($sql);
    if (!$stmt) return [];
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    $ids = [];
    while ($row = $res->fetch_assoc()) {
        $ids[] = (int)$row['grupo_id'];
    }
    return $ids;
}

/**
 * Salva as associações de um usuário com grupos de trabalho
 */
function saveUserGroups(mysqli $db, int $userId, array $groupIds): bool {
    // Primeiro remove todas as associações atuais
    $stmtDel = $db->prepare("DELETE FROM usuario_grupos WHERE usuario_id = ?");
    if ($stmtDel) {
        $stmtDel->bind_param('i', $userId);
        $stmtDel->execute();
    }

    if (empty($groupIds)) return true;

    // Depois insere as novas
    $stmtIns = $db->prepare("INSERT INTO usuario_grupos (usuario_id, grupo_id) VALUES (?, ?)");
    if (!$stmtIns) return false;

    foreach ($groupIds as $gid) {
        $gid = (int)$gid;
        if ($gid > 0) {
            $stmtIns->bind_param('ii', $userId, $gid);
            $stmtIns->execute();
        }
    }
    return true;
}

/**
 * Garante que o grupo padrão 'Todos' exista na paróquia
 * e que todos os usuários da paróquia estejam nele.
 */
function ensureDefaultVisitorGroup(mysqli $db, int $paroquiaId): void {
    if ($paroquiaId <= 0) return;

    // Migrar nome antigo 'Visitante' -> 'Todos' se existir
    $db->query("UPDATE grupos_trabalho SET nome = 'Todos', descricao = 'Grupo padrão — todos os membros da paróquia', visivel = 1 WHERE paroquia_id = $paroquiaId AND nome = 'Visitante'");
    
    // Verificar se 'Todos' já existe
    $check = $db->prepare("SELECT id FROM grupos_trabalho WHERE paroquia_id = ? AND nome = 'Todos' LIMIT 1");
    if (!$check) return;
    $check->bind_param('i', $paroquiaId);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $grupoId = (int)$row['id'];
    } else {
        // Criar grupo 'Todos'
        $stmt = $db->prepare("INSERT INTO grupos_trabalho (paroquia_id, nome, descricao, cor, visivel) VALUES (?, 'Todos', 'Grupo padrão — todos os membros da paróquia', '#3b82f6', 1)");
        if (!$stmt) return;
        $stmt->bind_param('i', $paroquiaId);
        $stmt->execute();
        $grupoId = (int)$db->insert_id;
    }

    if ($grupoId <= 0) return;

    // Adicionar todos os usuários da paróquia que ainda não estão no grupo
    $db->query("
        INSERT IGNORE INTO usuario_grupos (usuario_id, grupo_id)
        SELECT u.id, $grupoId
        FROM usuarios u
        WHERE u.paroquia_id = $paroquiaId
          AND u.id NOT IN (SELECT usuario_id FROM usuario_grupos WHERE grupo_id = $grupoId)
    ");
}

/**
 * Salva as associações de um usuário com grupos de trabalho, 
 * respeitando o escopo do administrador logado (merge inteligente).
 */
function saveUserGroupsScoped(mysqli $db, int $userId, array $postGroupIds, array $manageableGroupIds): bool {
    // 1. Converter tudo para int para segurança
    $postGroupIds = array_map('intval', $postGroupIds);
    $manageableGroupIds = array_map('intval', $manageableGroupIds);

    // 2. Buscar grupos ATUAIS do usuário no banco
    $currentTargetGroups = getUserGroups($db, $userId);

    // 3. Identificar grupos que o admin NÃO pode gerenciar (devem ser mantidos)
    $groupsToKeep = array_diff($currentTargetGroups, $manageableGroupIds);

    // 4. Identificar grupos que o admin QUER associar (devem estar no escopo dele)
    $groupsToAdd = array_intersect($postGroupIds, $manageableGroupIds);

    // 5. Novo conjunto final = Mantidos + Adicionados
    $finalGroupIds = array_unique(array_merge($groupsToKeep, $groupsToAdd));

    // 6. Aplicar salvamento padrão
    return saveUserGroups($db, $userId, $finalGroupIds);
}

/**
 * Garante que a tabela atividade_grupos exista.
 */
function ensureAtividadeGruposTable(mysqli $db): void {
    static $checked = false;
    if ($checked) return;
    $checked = true;

    $db->query("
        CREATE TABLE IF NOT EXISTS atividade_grupos (
            atividade_id INT(10) UNSIGNED NOT NULL,
            grupo_id INT(10) UNSIGNED NOT NULL,
            PRIMARY KEY (atividade_id, grupo_id),
            KEY fk_ag_atividade (atividade_id),
            KEY fk_ag_grupo (grupo_id),
            CONSTRAINT fk_ag_atividade FOREIGN KEY (atividade_id) REFERENCES atividades (id) ON DELETE CASCADE,
            CONSTRAINT fk_ag_grupo FOREIGN KEY (grupo_id) REFERENCES grupos_trabalho (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}

/**
 * Salva a associação de uma atividade com grupos.
 */
function saveActivityGroups(mysqli $db, int $atividadeId, array $groupIds): bool {
    ensureAtividadeGruposTable($db);
    
    $stmtDel = $db->prepare("DELETE FROM atividade_grupos WHERE atividade_id = ?");
    if ($stmtDel) {
        $stmtDel->bind_param('i', $atividadeId);
        $stmtDel->execute();
    }

    if (empty($groupIds)) return true;

    $stmtIns = $db->prepare("INSERT INTO atividade_grupos (atividade_id, grupo_id) VALUES (?, ?)");
    if (!$stmtIns) return false;

    foreach ($groupIds as $gid) {
        $gid = (int)$gid;
        if ($gid > 0) {
            $stmtIns->bind_param('ii', $atividadeId, $gid);
            $stmtIns->execute();
        }
    }
    return true;
}

/**
 * Retorna os IDs dos grupos associados a uma atividade.
 */
function getActivityGroups(mysqli $db, int $atividadeId): array {
    ensureAtividadeGruposTable($db);
    $sql = "SELECT grupo_id FROM atividade_grupos WHERE atividade_id = ?";
    $stmt = $db->prepare($sql);
    if (!$stmt) return [];
    $stmt->bind_param('i', $atividadeId);
    $stmt->execute();
    $res = $stmt->get_result();
    $ids = [];
    while ($row = $res->fetch_assoc()) {
        $ids[] = (int)$row['grupo_id'];
    }
    return $ids;
}
?>
