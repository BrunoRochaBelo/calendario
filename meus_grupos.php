<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Meus Grupos de Trabalho (v1.0)
 * Filtro pessoal do calendário por grupo de trabalho
 * ═══════════════════════════════════════════════════════
 */
require_once 'functions.php';
requireLogin();

$pid = current_paroquia_id();
$uid = (int)($_SESSION['usuario_id'] ?? 0);

// Busca os grupos que o usuário pertence
$stmtG = $conn->prepare(
    "SELECT g.id, g.nome, g.descricao, g.cor, g.ativo,
            (SELECT COUNT(*) FROM usuario_grupos WHERE grupo_id = g.id) AS total_membros,
            (SELECT COUNT(*) FROM atividade_grupos ag 
             INNER JOIN atividades a ON a.id = ag.atividade_id
             WHERE ag.grupo_id = g.id AND a.paroquia_id = ?) AS total_eventos
     FROM grupos_trabalho g
     INNER JOIN usuario_grupos ug ON ug.grupo_id = g.id
     WHERE ug.usuario_id = ? AND g.paroquia_id = ? AND g.ativo = 1
     ORDER BY g.nome ASC"
);
$stmtG->bind_param('iii', $pid, $uid, $pid);
$stmtG->execute();
$grupos = $stmtG->get_result()->fetch_all(MYSQLI_ASSOC);

// Filtros ativos da sessão
$filtroAtual = $_SESSION['filtro_grupos'] ?? null; // null = todos ativos
function isGrupoAtivo(int $id, $filtro): bool {
    if ($filtro === null) return true;
    return in_array($id, $filtro, true);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Meus Grupos de Trabalho – PASCOM</title>
    <link rel="stylesheet" href="style.css?v=2.4.5"
        <link rel="stylesheet" href="css/responsive.css?v=2.4.5">
    <style>
        .app-shell { display: flex; min-height: 100vh; }
        .main-content { flex: 1; margin-left: var(--sidebar-w); padding: 3rem; transition: margin 0.3s; }

        @media (max-width: 1024px) {
            .main-content { margin-left: 0; padding: 1.5rem; padding-top: 5rem; }
            .page-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
            .grupos-grid { grid-template-columns: 1fr; }
        }

        .page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem; flex-wrap: wrap; gap: 1.5rem; }
        .header-actions { display: flex; gap: 0.75rem; align-items: center; }

        .filter-info { background: rgba(var(--primary-rgb), 0.05); border: 1px solid rgba(var(--primary-rgb), 0.15); padding: 1rem 1.5rem; border-radius: 14px; font-size: 0.82rem; color: var(--text-dim); margin-bottom: 2rem; display: flex; align-items: center; gap: 0.8rem; }
        .filter-info svg { color: var(--primary); flex-shrink: 0; }

        .grupos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; }

        .grupo-card {
            padding: 1.8rem; border-radius: 20px; border-top-width: 4px;
            display: flex; flex-direction: column; gap: 1rem;
            transition: all 0.3s var(--ease); cursor: default;
        }
        .grupo-card:hover { transform: translateY(-4px); box-shadow: var(--sh-lg); }
        .grupo-card.inactive-card { opacity: 0.5; filter: grayscale(0.6); }

        .card-header { display: flex; align-items: center; gap: 1rem; }
        .card-icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 900; font-size: 1.1rem; flex-shrink: 0; }
        .card-title h3 { font-size: 1.05rem; font-weight: 800; margin-bottom: 0.1rem; }
        .card-title p { font-size: 0.68rem; color: var(--text-ghost); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }

        .card-stats { display: flex; gap: 1.5rem; padding-top: 0.8rem; border-top: 1px solid var(--border); }
        .stat { display: flex; flex-direction: column; gap: 0.1rem; }
        .stat-val { font-size: 1.2rem; font-weight: 900; color: var(--text); }
        .stat-lbl { font-size: 0.65rem; font-weight: 700; color: var(--text-ghost); text-transform: uppercase; letter-spacing: 0.05em; }

        /* Toggle Switch */
        .toggle-row { display: flex; align-items: center; justify-content: space-between; margin-top: auto; }
        .toggle-label { font-size: 0.82rem; font-weight: 600; color: var(--text-dim); }
        .toggle-switch { position: relative; width: 52px; height: 28px; cursor: pointer; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; position: absolute; }
        .toggle-track {
            position: absolute; inset: 0; border-radius: 100px;
            background: var(--panel-hi); border: 1px solid var(--border);
            transition: all 0.3s; cursor: pointer;
        }
        .toggle-track::after {
            content: ''; position: absolute; top: 3px; left: 3px;
            width: 20px; height: 20px; border-radius: 50%;
            background: var(--text-ghost); transition: all 0.3s;
        }
        .toggle-switch input:checked + .toggle-track { background: var(--grp-color, var(--primary)); border-color: var(--grp-color, var(--primary)); }
        .toggle-switch input:checked + .toggle-track::after { transform: translateX(24px); background: #fff; }

        /* Geral card */
        .card-geral { background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-top: 4px solid #64748b; }

        .empty-state { text-align: center; padding: 5rem 2rem; color: var(--text-dim); }
        .empty-state svg { opacity: 0.3; margin-bottom: 1.5rem; }

        /* ── View Modes ────────────────────────────────────────── */
        .view-controls { display: flex; gap: 0.5rem; background: var(--panel); padding: 0.4rem; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 1.5rem; width: fit-content; }
        .view-btn { 
            padding: 0.5rem; border-radius: 8px; border: none; background: transparent; color: var(--text-dim); cursor: pointer; display: flex; align-items: center; transition: all var(--anim);
        }
        .view-btn:hover { background: var(--panel-hi); color: var(--text); }
        .view-btn.active { background: var(--primary); color: #fff; box-shadow: var(--sh-primary); }

        /* LIST VIEW */
        .grupos-grid.view-list { grid-template-columns: 1fr; gap: 0.8rem; }
        .view-list .grupo-card { 
            flex-direction: row; align-items: center; padding: 1rem 1.5rem; gap: 2rem; 
        }
        .view-list .card-header { min-width: 250px; flex-shrink: 0; }
        .view-list .grupo-card > p { flex: 2; min-height: auto; margin: 0; display: flex; align-items: center; }
        .view-list .card-stats { flex: 1; border: none; padding: 0; margin: 0; justify-content: flex-start; }
        .view-list .toggle-row { flex: 1; margin: 0; justify-content: flex-end; gap: 1rem; }
        .view-list .toggle-label { display: none; }

        /* COMPACT VIEW */
        .grupos-grid.view-compact { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1rem; }
        .view-compact .grupo-card { padding: 1.2rem; gap: 1rem; }
        .view-compact .card-icon { width: 36px; height: 36px; font-size: 1rem; }
        .view-compact .grupo-card > p { display: none; }
        .view-compact .card-stats { display: none; }
        .view-compact .toggle-row { padding-top: 1rem; border-top: 1px solid var(--border); margin-top: 0.5rem; }

        @media (max-width: 900px) {
            .view-list .grupo-card { flex-direction: column; align-items: flex-start; gap: 1rem; }
            .view-list .card-stats { padding-top: 1rem; border-top: 1px solid var(--border); width: 100%; justify-content: flex-start; }
            .view-list .toggle-row { width: 100%; justify-content: space-between; }
            .view-list .toggle-label { display: block; }
        }
    </style>
</head>
<body>
<div class="bg-mesh"></div>
<div class="app-shell">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <header class="page-header animate-in">
            <div>
                <p style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.15em; color: var(--primary);">FILTRO DO CALENDÁRIO</p>
                <h1 class="gradient-text">Meus Grupos de Trabalho</h1>
                <p style="font-size: 0.85rem; color: var(--text-dim); margin-top: 0.5rem;">Ative ou desative grupos para controlar o que aparece no calendário.</p>
            </div>
            <div class="header-actions">
                <button type="button" id="btnTodos" class="btn btn-ghost" onclick="setTodos(true)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Mostrar Todos
                </button>
                <button type="button" id="btnNenhum" class="btn btn-ghost" style="color: #ef4444;" onclick="setTodos(false)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Ocultar Todos
                </button>
                <a href="index.php" class="btn btn-primary shimmer">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                    Ver Calendário
                </a>
            </div>
        </header>

        <div class="filter-info animate-in" style="animation-delay:0.05s;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>Os eventos de grupos <strong>desativados</strong> ficam ocultos no calendário principal. A preferência é salva automaticamente ao alternar o botão.</span>
        </div>

        <div class="view-controls animate-in" style="animation-delay: 0.05s;">
            <button onclick="setView('grid')" id="btn-grid" class="view-btn" title="Grelha">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            </button>
            <button onclick="setView('list')" id="btn-list" class="view-btn" title="Lista">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
            </button>
            <button onclick="setView('compact')" id="btn-compact" class="view-btn" title="Compacto">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
            </button>
        </div>

        <?php if (empty($grupos)): ?>
        <div class="empty-state">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M8 15h8M9 9h.01M15 9h.01"/></svg>
            <h3 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 0.5rem;">Nenhum grupo encontrado</h3>
            <p>Você ainda não foi adicionado a nenhum grupo de trabalho nesta paróquia.</p>
        </div>
        <?php else: ?>
        <div id="gruposContainer" class="grupos-grid animate-in" style="animation-delay:0.1s;">


            <?php foreach ($grupos as $g): ?>
            <?php $ativo = isGrupoAtivo((int)$g['id'], $filtroAtual); ?>
            <article class="glass grupo-card <?= !$ativo ? 'inactive-card' : '' ?>" id="card-<?= $g['id'] ?>"
                     style="border-top-color: <?= h($g['cor']) ?>; --grp-color: <?= h($g['cor']) ?>;">
                <div class="card-header">
                    <div class="card-icon" style="background: <?= h($g['cor']) ?>;">
                        <?= mb_strtoupper(mb_substr(h($g['nome']), 0, 1)) ?>
                    </div>
                    <div class="card-title">
                        <h3><?= h($g['nome']) ?></h3>
                        <p><?= (int)$g['total_membros'] ?> membros</p>
                    </div>
                </div>

                <?php if (!empty($g['descricao'])): ?>
                <p style="font-size: 0.82rem; color: var(--text-dim); line-height: 1.5;"><?= h($g['descricao']) ?></p>
                <?php endif; ?>

                <div class="card-stats">
                    <div class="stat">
                        <span class="stat-val"><?= (int)$g['total_membros'] ?></span>
                        <span class="stat-lbl">Membros</span>
                    </div>
                    <div class="stat">
                        <span class="stat-val"><?= (int)$g['total_eventos'] ?></span>
                        <span class="stat-lbl">Eventos</span>
                    </div>
                </div>

                <div class="toggle-row">
                    <span class="toggle-label">Mostrar no calendário</span>
                    <label class="toggle-switch" title="Ativar/desativar <?= h($g['nome']) ?>">
                        <input type="checkbox" class="grupo-toggle" data-grupo-id="<?= (int)$g['id'] ?>" <?= $ativo ? 'checked' : '' ?>>
                        <span class="toggle-track"></span>
                    </label>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>
</div>

<script>
// ── Toggle individual ─────────────────────────────────────
document.querySelectorAll('.grupo-toggle').forEach(toggle => {
    toggle.addEventListener('change', function() {
        const grupoId = this.dataset.grupoId;
        const ativo = this.checked ? 1 : 0;
        const card = document.getElementById(grupoId === '0' ? 'card-geral' : `card-${grupoId}`);

        // Visual feedback imediato
        if (card) card.classList.toggle('inactive-card', !this.checked);

        // Salva na sessão via AJAX
        fetch('api_filtro_grupos.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `action=toggle&grupo_id=${grupoId}&ativo=${ativo}`
        }).catch(err => console.error('Erro ao salvar filtro:', err));
    });
});

// ── Mostrar Todos / Ocultar Todos ─────────────────────────
function setTodos(estado) {
    document.querySelectorAll('.grupo-toggle').forEach(t => {
        t.checked = estado;
        const grupoId = t.dataset.grupoId;
        const card = document.getElementById(grupoId === '0' ? 'card-geral' : `card-${grupoId}`);
        if (card) card.classList.toggle('inactive-card', !estado);
    });

    fetch('api_filtro_grupos.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=toggle&grupo_id=-1&ativo=${estado ? 1 : 0}`
    }).catch(err => console.error('Erro:', err));
}

// ── View Modes ──────────────────────────────────────────
function setView(mode) {
    const container = document.getElementById('gruposContainer');
    if(!container) return;
    const btns = document.querySelectorAll('.view-btn');
    
    // Toggle classes
    container.classList.remove('view-list', 'view-compact');
    if (mode === 'list') container.classList.add('view-list');
    if (mode === 'compact') container.classList.add('view-compact');

    // Active button state
    btns.forEach(b => b.classList.remove('active'));
    if(document.getElementById('btn-' + mode)) {
        document.getElementById('btn-' + mode).classList.add('active');
    }

    localStorage.setItem('mygroups-view-mode', mode);
}

// Init view mode
document.addEventListener('DOMContentLoaded', () => {
    const savedMode = localStorage.getItem('mygroups-view-mode') || 'grid';
    setView(savedMode);
});
</script>
</body>
</html>
