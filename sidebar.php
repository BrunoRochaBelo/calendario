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

$parish_name = 'Igreja Católica';
$pid = $_SESSION['paroquia_id'] ?? 0;
if ($pid > 0) {
    $stmt = $conn->prepare("SELECT nome FROM paroquias WHERE id = ?");
    $stmt->bind_param('i', $pid);
    $stmt->execute();
    $resParish = $stmt->get_result();
    if ($resParish && $resParish->num_rows > 0) {
        $parish_name = $resParish->fetch_assoc()['nome'];
    }
}

$all_parishes = [];
if ($_SESSION['usuario_id'] == 1) {
    $p_res = $conn->query("SELECT id, nome FROM paroquias ORDER BY nome");
    if ($p_res) {
        while ($p_row = $p_res->fetch_assoc()) {
            $all_parishes[] = $p_row;
        }
    }
}

// Navigation logic
$current_page = basename($_SERVER['PHP_SELF']);
function is_active(string $page): string {
    global $current_page;
    return ($current_page === $page) ? 'nav-item active' : 'nav-item';
}
?>

<button class="menu-trigger" onclick="toggleSidebar()">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
</button>

<aside class="sidebar" id="mainSidebar">
    <div class="sidebar-header">
        <div class="brand">
            <!-- ... same brand content ... -->
            <div class="brand-logo">
                <?php 
                $icon_path = "img/paroquia_{$pid}.png";
                if ($pid > 0 && file_exists(__DIR__ . '/' . $icon_path)): 
                ?>
                    <img src="<?= $icon_path ?>?v=<?= time() ?>" alt="Paróquia" style="width: 100%; height: 100%; border-radius: 12px; object-fit: cover;">
                <?php else: ?>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <?php endif; ?>
            </div>
            <div class="brand-text">
                <span class="brand-name">PASCOM</span>
                <?php if ($_SESSION['usuario_id'] == 1): ?>
                    <select class="brand-sub-select" onchange="window.location.href='select_paroquia.php?id='+this.value">
                        <?php if ($pid == 0): ?><option value="0" selected>Selecionar...</option><?php endif; ?>
                        <?php foreach($all_parishes as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= $p['id'] == $pid ? 'selected' : '' ?>><?= h($p['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <span class="brand-sub" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px; display: block;"><?= h($parish_name) ?></span>
                <?php endif; ?>
            </div>
        </div>
        <button class="close-sidebar" onclick="toggleSidebar()">
             <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- ... rest of sidebar ... -->
    <nav class="sidebar-nav">
        <!-- ... same nav content ... -->
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
            <?php if ($_SESSION['usuario_id'] == 1): ?>
            <a href="paroquias.php" class="<?= is_active('paroquias.php') ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"/><path d="M9 21v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"/></svg>
                <span>Sedes/Contextos</span>
            </a>
            <?php endif; ?>
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

<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

<style>
:root {
    --sidebar-w: 280px;
}

.sidebar {
    position: fixed; top: 0; left: 0; width: var(--sidebar-w); height: 100vh;
    background: rgba(13, 14, 26, 0.9); backdrop-filter: blur(25px);
    border-right: 1px solid var(--border); display: flex; flex-direction: column;
    z-index: 2100; transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}

.sidebar-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);
    z-index: 2050; opacity: 0; pointer-events: none; transition: all 0.4s;
}

.sidebar.open + .sidebar-overlay {
    opacity: 1; pointer-events: auto;
}

.menu-trigger {
    position: fixed; top: 1.5rem; left: 1.5rem; z-index: 1000;
    width: 48px; height: 48px; border-radius: 12px;
    background: var(--panel-hi); border: 1px solid var(--border);
    color: var(--text); display: none; align-items: center; justify-content: center;
    cursor: pointer; transition: all 0.3s;
}
.menu-trigger:hover { background: var(--border); transform: scale(1.05); }

.close-sidebar {
    background: transparent; border: none; color: var(--text-ghost);
    cursor: pointer; display: none; padding: 0.5rem; transition: 0.2s;
}
.close-sidebar:hover { color: #ef4444; }

.sidebar-header { padding: 2rem 1.5rem; display: flex; justify-content: space-between; align-items: center; }
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
.brand-sub-select { 
    font-size: 0.75rem; color: var(--text-dim); font-weight: 600;
    background: rgba(255,255,255,0.05); border: 1px solid var(--border);
    border-radius: 4px; padding: 0.1rem 0.3rem; outline: none;
    max-width: 140px; cursor: pointer; transition: all 0.2s;
}
.brand-sub-select:hover { border-color: var(--primary); background: rgba(var(--primary-rgb), 0.1); }
.brand-sub-select option { background: #1a1b2e; color: #fff; }

.sidebar-nav { flex: 1; overflow-y: auto; padding: 1rem; }
/* ... existing nav styles ... */
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
.user-status::before { content: ''; width: 6px; height: 6px; background: #22c55e; border-radius: 50%; }

.logout-btn { 
    color: var(--text-ghost); transition: all 0.2s; 
    padding: 0.5rem; border-radius: 8px;
}
.logout-btn:hover { color: #ef4444; background: rgba(239, 68, 68, 0.1); }

@media (max-width: 1024px) {
    .sidebar { transform: translateX(-100%); width: 260px; }
    .sidebar.open { transform: translateX(0); }
    .menu-trigger, .close-sidebar { display: flex; }
}
</style>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('mainSidebar');
    sidebar.classList.toggle('open');
}
</script>

