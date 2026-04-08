<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Modern Sidebar Component (v2.0)
 * Glassmorphism · Premium Navigation · Active Highlighting
 * ═══════════════════════════════════════════════════════ */

require_once 'config.php';
requireLogin();

$nome = $_SESSION['usuario_nome'] ?? 'Usuário';
$initials = mb_strtoupper(mb_substr($nome, 0, 1));

// Navigation logic
$current_page = basename($_SERVER['PHP_SELF']);
function is_active(string $page): string {
    global $current_page;
    return ($current_page === $page) ? 'nav-item active' : 'nav-item';
}
?>

<aside class="sidebar animate-in">
    <div class="sidebar-header">
        <div class="brand">
            <div class="brand-logo">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div class="brand-text">
                <span class="brand-name">PASCOM</span>
                <span class="brand-sub">Portal Digital</span>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-group">
            <span class="nav-label">Principal</span>
            <a href="index.php" class="<?= is_active('index.php') ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="3" x2="21" y1="10" y2="10"/><path d="M8 14h.01M12 14h.01M16 14h.01"/></svg>
                <span>Calendário</span>
            </a>
        </div>

        <?php if (can('admin_sistema') || can('admin_usuarios')): ?>
        <div class="nav-group">
            <span class="nav-label">Administração</span>
            <?php if (can('admin_sistema')): ?>
            <a href="locais_paroquia.php" class="<?= is_active('locais_paroquia.php') ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span>Locais</span>
            </a>
            <a href="tipos_atividade.php" class="<?= is_active('tipos_atividade.php') ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.27 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>
                <span>Categorias</span>
            </a>
            <?php endif; ?>
            
            <?php if (can('admin_usuarios')): ?>
            <a href="usuarios.php" class="<?= is_active('usuarios.php') ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <span>Usuários</span>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (can('ver_logs')): ?>
        <div class="nav-group">
            <span class="nav-label">Relatórios</span>
            <a href="logs.php" class="<?= is_active('logs.php') ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                <span>Logs do Sistema</span>
            </a>
        </div>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar"><?= h($initials) ?></div>
            <div class="user-details">
                <span class="user-name"><?= h($nome) ?></span>
                <span class="user-status">Online</span>
            </div>
        </div>
        <a href="logout.php" class="logout-btn" title="Sair do Sistema">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
        </a>
    </div>
</aside>

<style>
:root {
    --sidebar-w: 280px;
}

.sidebar {
    position: fixed; top: 0; left: 0; width: var(--sidebar-w); height: 100vh;
    background: rgba(13, 14, 26, 0.8); backdrop-filter: blur(25px);
    border-right: 1px solid var(--border); display: flex; flex-direction: column;
    z-index: 1000;
}

.sidebar-header { padding: 2rem 1.5rem; }
.brand { display: flex; align-items: center; gap: 1rem; }
.brand-logo { 
    width: 42px; height: 42px; border-radius: 12px; 
    background: linear-gradient(135deg, var(--primary), var(--accent));
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 8px 16px rgba(var(--primary-rgb), 0.2);
}
.brand-text { display: flex; flex-direction: column; }
.brand-name { font-weight: 900; font-size: 1.1rem; letter-spacing: -0.01em; color: var(--text); }
.brand-sub { font-size: 0.75rem; color: var(--text-dim); font-weight: 600; }

.sidebar-nav { flex: 1; overflow-y: auto; padding: 1rem; }
.nav-group { margin-bottom: 2rem; }
.nav-label { 
    display: block; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; 
    color: var(--text-ghost); letter-spacing: 0.12em; margin-bottom: 0.8rem; padding-left: 0.8rem;
}
.nav-item {
    display: flex; align-items: center; gap: 1rem; padding: 0.85rem 1rem;
    border-radius: var(--r-md); color: var(--text-dim); text-decoration: none;
    font-size: 0.9rem; font-weight: 600; transition: all var(--anim);
    margin-bottom: 0.25rem;
}
.nav-item:hover { background: var(--panel-hi); color: var(--text); transform: translateX(4px); }
.nav-item.active { 
    background: rgba(var(--primary-rgb), 0.1); color: var(--primary); 
    box-shadow: inset 0 0 0 1px rgba(var(--primary-rgb), 0.2);
}
.nav-item.active svg { filter: drop-shadow(0 0 5px var(--primary)); }

.sidebar-footer { 
    padding: 1.5rem; border-top: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
}
.user-info { display: flex; align-items: center; gap: 0.8rem; }
.user-avatar { 
    width: 38px; height: 38px; border-radius: 10px; 
    background: var(--panel-hi); border: 1px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 0.9rem; color: var(--primary);
}
.user-details { display: flex; flex-direction: column; }
.user-name { font-size: 0.85rem; font-weight: 700; color: var(--text); }
.user-status { font-size: 0.7rem; color: #22c55e; font-weight: 700; display: flex; align-items: center; gap: 0.3rem; }
.user-status::before { content: ''; width: 6px; height: 6px; background: #22c55e; border-radius: 50%; box-shadow: 0 0 8px #22c55e; }

.logout-btn { 
    color: var(--text-ghost); transition: all 0.2s; 
    padding: 0.5rem; border-radius: 8px;
}
.logout-btn:hover { color: #ef4444; background: rgba(239, 68, 68, 0.1); }

@media (max-width: 1024px) {
    .sidebar { transform: translateX(-100%); }
    .sidebar.open { transform: translateX(0); }
}
</style>
