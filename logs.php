<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Audit Trail & Logs (v2.0)
 * Security Monitoring · Action Timeline · Premium UI
 * ═══════════════════════════════════════════════════════ */

require_once 'functions.php';
requireLogin();

// Restricted to Managers and Master
if (!has_level(1)) {
    header('Location: dashboard.php?error=unauthorized');
    exit();
}

$pid = current_paroquia_id();
$filter_table = $_GET['tabela'] ?? '';
$filter_user = $_GET['usuario'] ?? '';

// 1. Build Query
$where = [];
$params = [];
$types = "";

// Data isolation: Master (0) sees all, others only their Parish
if (!has_level(0)) {
    $where[] = "l.paroquia_id = ?";
    $params[] = $pid;
    $types .= "i";
}

if ($filter_table) {
    $where[] = "l.tabela_afetada LIKE ?";
    $params[] = "%$filter_table%";
    $types .= "s";
}

if ($filter_user) {
    $where[] = "u.nome LIKE ?";
    $params[] = "%$filter_user%";
    $types .= "s";
}

$sql = "
    SELECT l.*, u.nome as usuario_nome 
    FROM log_alteracoes l 
    LEFT JOIN usuarios u ON l.usuario_id = u.id
";

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY l.data_hora DESC LIMIT 100";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$logs = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Auditoria do Sistema – PASCOM</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .app-shell { display: flex; min-height: 100vh; }
        .main-content { flex: 1; margin-left: var(--sidebar-w); padding: 3rem; }
        
        .header-flex { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3.5rem; }
        
        .filter-bar { display: flex; gap: 1rem; margin-bottom: 2.5rem; }
        .filter-bar .form-group { flex: 1; }

        .timeline { display: flex; flex-direction: column; gap: 1rem; }
        .log-item { display: grid; grid-template-columns: 180px 150px 100px 1fr 200px; align-items: center; gap: 1.5rem; padding: 1.25rem 2rem; transition: background 0.2s; }
        .log-item:hover { background: rgba(255,255,255,0.03); }
        
        .log-date { font-size: 0.75rem; font-weight: 800; color: var(--text-ghost); font-family: monospace; }
        .log-user { display: flex; align-items: center; gap: 0.8rem; font-size: 0.85rem; font-weight: 700; color: var(--text); }
        .log-user .avatar { width: 24px; height: 24px; border-radius: 6px; background: var(--panel-hi); display: flex; align-items: center; justify-content: center; font-size: 0.7rem; color: var(--primary); }
        
        .log-badge { font-size: 0.65rem; font-weight: 900; padding: 0.3rem 0.6rem; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.05em; background: var(--panel-hi); color: var(--text-dim); border: 1px solid var(--border); }
        .log-badge.success { color: #10b981; border-color: rgba(16, 185, 129, 0.2); }
        .log-badge.warning { color: #f59e0b; border-color: rgba(245, 158, 11, 0.2); }
        .log-badge.danger { color: #ef4444; border-color: rgba(239, 68, 68, 0.2); }

        .log-desc { font-size: 0.85rem; color: var(--text-dim); }
        .log-id { font-size: 0.7rem; color: var(--text-ghost); font-family: monospace; text-align: right; }

        @media (max-width: 1200px) {
            .log-item { grid-template-columns: 1fr 1fr; gap: 1rem; }
            .log-desc { grid-column: span 2; }
            .log-id { display: none; }
        }
    </style>
</head>
<body>
    <div class="bg-mesh"></div>

    <div class="app-shell">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <header class="header-flex animate-in">
                <div>
                    <p style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.15em; color: var(--text-ghost);">AUDITORIA</p>
                    <h1 class="gradient-text">Logs de Atividades</h1>
                </div>
            </header>

            <form method="GET" class="glass filter-bar animate-in" style="padding: 1.5rem; border-radius: 20px; animation-delay: 0.1s;">
                <div class="form-group">
                    <label style="font-size: 0.65rem; margin-bottom: 0.5rem; display: block;">FILTRAR POR TABELA</label>
                    <input type="text" name="tabela" value="<?= h($filter_table) ?>" placeholder="Ex: atividades, locais...">
                </div>
                <div class="form-group">
                    <label style="font-size: 0.65rem; margin-bottom: 0.5rem; display: block;">FILTRAR POR USUÁRIO</label>
                    <input type="text" name="usuario" value="<?= h($filter_user) ?>" placeholder="Nome do usuário...">
                </div>
                <div style="display: flex; gap: 0.5rem; align-items: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 0.8rem 1.5rem;">Filtrar</button>
                    <a href="logs.php" class="btn btn-ghost" style="padding: 0.8rem 1.5rem;">Limpar</a>
                </div>
            </form>

            <section class="glass timeline animate-in" style="border-radius: 24px; padding: 1rem 0; animation-delay: 0.2s;">
                <?php if ($logs->num_rows > 0): ?>
                    <?php while ($l = $logs->fetch_assoc()): ?>
                    <?php 
                        $badgeClass = '';
                        if (str_contains($l['acao'], 'CRIAR')) $badgeClass = 'success';
                        if (str_contains($l['acao'], 'EDITAR')) $badgeClass = 'warning';
                        if (str_contains($l['acao'], 'EXCLUIR')) $badgeClass = 'danger';
                    ?>
                    <div class="log-item">
                        <div class="log-date"><?= date('d/m/Y H:i:s', strtotime($l['data_hora'])) ?></div>
                        <div class="log-user">
                            <div class="avatar"><?= mb_substr($l['usuario_nome'] ?: '?', 0, 1) ?></div>
                            <span><?= h($l['usuario_nome'] ?: 'Sistema') ?></span>
                        </div>
                        <div style="display: flex;"><span class="log-badge <?= $badgeClass ?>"><?= h($l['acao']) ?></span></div>
                        <div class="log-desc">
                            Na tabela <b style="color: var(--text);"><?= h($l['tabela_afetada']) ?></b>
                            <?php if ($l['detalhes_alteracao']): ?>
                                — <span style="font-style: italic; opacity: 0.8;"><?= h($l['detalhes_alteracao']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="log-id">REF_ID: <?= $l['registro_id'] ?: 'N/A' ?></div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 5rem; color: var(--text-ghost);">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 1.5rem; opacity: 0.3;"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="9"/></svg>
                        <p>Nenhum registro encontrado para os filtros aplicados.</p>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>
