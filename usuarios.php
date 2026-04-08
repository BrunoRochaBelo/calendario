<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — User Management Console (v2.0)
 * RBAC Control · Staff Listing · Premium UI
 * ═══════════════════════════════════════════════════════ */

require_once 'functions.php';
requireLogin();
ensureUserPhotoColumn($conn);

requirePerm('admin_usuarios');

$pid = current_paroquia_id();
$can_edit = can('admin_usuarios');
$is_master = has_level(0);
$my_user_id = (int)($_SESSION['usuario_id'] ?? 0);
$my_level = (int)($_SESSION['usuario_nivel'] ?? 99);

// 1. Handle Status Toggles (AJAX/Simple POST)
if (isset($_GET['toggle_status']) && $can_edit) {
    $uid = (int)$_GET['toggle_status'];
    $checkStmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ? LIMIT 1");
    $checkStmt->bind_param('i', $uid);
    $checkStmt->execute();
    $oldState = $checkStmt->get_result()->fetch_assoc();

    if (!$oldState) {
        header('Location: usuarios.php?error=notfound');
        exit();
    }

    if (
        (int)$oldState['id'] !== $my_user_id &&
        (
            (int)$oldState['paroquia_id'] !== $pid ||
            (int)$oldState['nivel_acesso'] <= $my_level
        )
    ) {
        header('Location: usuarios.php?error=unauthorized');
        exit();
    }
    
    $conn->query("UPDATE usuarios SET ativo = 1 - ativo WHERE id = $uid");
    
    $newResult = $conn->query("SELECT * FROM usuarios WHERE id = $uid");
    $newState = $newResult->fetch_assoc();
    
    logAction($conn, 'ALTERAR_STATUS_USUARIO', 'usuarios', $uid, ['antigo' => $oldState, 'novo' => $newState]);
    header('Location: usuarios.php?msg=Status alterado com sucesso');
    exit();
}

// 2. Build Query
$where = [];
$params = [];
$types = "";

if (!$is_master) {
    $where[] = "u.paroquia_id = ?";
    $params[] = $pid;
    $types .= "i";
    $where[] = "(u.id = ? OR u.nivel_acesso > ?)";
    $params[] = $my_user_id;
    $params[] = $my_level;
    $types .= "ii";
}

$sql = "
    SELECT u.*, p.nome as paroquia_nome,
    CASE 
        WHEN u.nivel_acesso = 0 THEN 'Master'
        WHEN u.nivel_acesso = 1 THEN 'Supervisor'
        WHEN u.nivel_acesso = 2 THEN 'Gerente'
        ELSE 'Usuário'
    END as nivel_label
    FROM usuarios u
    LEFT JOIN paroquias p ON u.paroquia_id = p.id
";

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY u.nome ASC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$users = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Gestão de Usuários — PASCOM</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .app-shell { display: flex; min-height: 100vh; }
        .main-content { flex: 1; margin-left: var(--sidebar-w); padding: 3rem; transition: margin 0.3s; }
        
        @media (max-width: 1024px) {
            .main-content { margin-left: 0; padding: 1.5rem; padding-top: 5rem; }
            .header-flex { flex-direction: column; align-items: flex-start; gap: 1.5rem; }
            .user-grid { grid-template-columns: 1fr; }
            .btn-primary { width: 100%; }
        }
        
        .header-flex { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3.5rem; }
        
        .user-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; }
        
        .user-card { padding: 2rem; display: flex; flex-direction: column; gap: 1.5rem; }
        .user-card-header { display: flex; align-items: center; gap: 1.2rem; }
        
        .user-avatar-lg { width: 56px; height: 56px; border-radius: 16px; background: var(--panel-hi); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: 800; color: var(--primary); overflow: hidden; }
        .user-avatar-lg img { width: 100%; height: 100%; object-fit: cover; }
        
        .user-meta { display: flex; flex-direction: column; }
        .user-name { font-weight: 800; font-size: 1.1rem; color: var(--text); }
        .user-role { font-size: 0.75rem; font-weight: 800; color: var(--primary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.2rem; }
        
        .user-details { border-top: 1px solid var(--border); padding-top: 1.5rem; display: grid; gap: 0.8rem; }
        .detail-item { display: flex; align-items: center; gap: 0.8rem; font-size: 0.85rem; color: var(--text-dim); }
        .detail-item svg { color: var(--text-ghost); }

        .user-actions { margin-top: auto; padding-top: 1.5rem; display: flex; gap: 0.8rem; }
        
        .status-pill { font-size: 0.65rem; font-weight: 900; padding: 0.3rem 0.6rem; border-radius: 6px; text-transform: uppercase; }
        .status-pill.active { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .status-pill.inactive { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

        @media (max-width: 768px) {
            .main-content { padding: 1.5rem; }
            .header-flex { flex-direction: column; align-items: flex-start; gap: 1.5rem; }
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
                    <p style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.15em; color: var(--text-ghost);">ADMINISTRAÇÃO</p>
                    <h1 class="gradient-text">Usuários & Acessos</h1>
                </div>
                <?php if ($can_edit && (can('cadastrar_usuario') || can('admin_usuarios'))): ?>
                    <a href="register.php" class="btn btn-primary shimmer">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="16" x2="22" y1="11" y2="11"/></svg>
                        Novo Usuário
                    </a>
                <?php endif; ?>
            </header>

            <?php if (isset($_GET['msg'])): ?> <?= alert('success', h($_GET['msg'])) ?> <?php endif; ?>

            <div class="user-grid animate-in" style="animation-delay: 0.1s;">
                <?php while ($u = $users->fetch_assoc()): ?>
                <div class="glass user-card">
                    <div class="user-card-header">
                        <div class="user-avatar-lg">
                            <?php if (!empty($u['foto_perfil']) && file_exists(__DIR__ . '/' . $u['foto_perfil'])): ?>
                                <img src="<?= h($u['foto_perfil']) ?>?v=<?= time() ?>" alt="Foto">
                            <?php else: ?>
                                <?= mb_substr($u['nome'], 0, 1) ?>
                            <?php endif; ?>
                        </div>
                        <div class="user-meta">
                            <span class="user-role"><?= $u['nivel_label'] ?></span>
                            <span class="user-name"><?= h($u['nome']) ?></span>
                            <div style="margin-top: 0.4rem;">
                                <span class="status-pill <?= $u['ativo'] ? 'active' : 'inactive' ?>">
                                    <?= $u['ativo'] ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="user-details">
                        <div class="detail-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <?= h($u['email']) ?>
                        </div>
                        <div class="detail-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <?= h($u['telefone'] ?: 'N/D') ?>
                        </div>
                        <div class="detail-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            <?= h($u['paroquia_nome'] ?: 'Global / Master') ?>
                        </div>
                    </div>

                    <?php
                        $canManageThisUser = (int)$u['id'] !== $my_user_id && (int)$u['nivel_acesso'] > $my_level;
                    ?>
                    <?php if ($can_edit && $canManageThisUser): ?>
                    <div class="user-actions">
                        <a href="usuarios.php?toggle_status=<?= $u['id'] ?>" class="btn <?= $u['ativo'] ? 'btn-ghost' : 'btn-primary' ?>" style="flex: 1; font-size: 0.75rem; padding: 0.6rem;">
                            <?= $u['ativo'] ? 'Desativar' : 'Ativar' ?>
                        </a>
                        <a href="editar_usuario.php?id=<?= $u['id'] ?>" class="btn btn-ghost" style="padding: 0.6rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endwhile; ?>
            </div>
        </main>
    </div>
</body>
</html>
