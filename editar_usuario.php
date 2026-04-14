<?php
require_once 'functions.php';
requireLogin();
requirePerm('admin_usuarios');
ensureUserPhotoColumn($conn);
ensurePermissionColumns($conn);
ensureUserPermissionsMaterialized($conn);

$id = (int)($_GET['id'] ?? 0);
$is_master = has_level(0);
$my_user_id = (int)($_SESSION['usuario_id'] ?? 0);
$my_level = (int)($_SESSION['usuario_nivel'] ?? 99);
$can_manage_users_by_level = $is_master || $my_level <= 3;
$my_pid = (int)($_SESSION['paroquia_id'] ?? 0);
$is_self = ($id === $my_user_id);
$my_group_ids = getUserGroups($conn, $my_user_id, $my_pid);
$todos_group_id = getDefaultTodosGroupId($conn, $my_pid);
if ($todos_group_id > 0) {
    $my_group_ids = array_values(array_filter(
        $my_group_ids,
        static fn(int $gid): bool => $gid !== $todos_group_id
    ));
}

$stmt = $conn->prepare('SELECT * FROM usuarios WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header('Location: usuarios.php?error=notfound');
    exit();
}

if (!$is_master && !$is_self && ((int)$user['nivel_acesso'] === 0 || (int)$user['id'] === 1)) {
    header('Location: usuarios.php?error=unauthorized');
    exit();
}

if (
    !$is_master &&
    (int)$user['id'] !== $my_user_id &&
    (
        (int)$user['paroquia_id'] !== $my_pid ||
        (int)$user['nivel_acesso'] < $my_level
    )
) {
    header('Location: usuarios.php?error=unauthorized');
    exit();
}

if (
    !$is_master &&
    !$is_self
) {
    if (empty($my_group_ids)) {
        header('Location: usuarios.php?error=unauthorized');
        exit();
    }

    $target_group_ids = getUserGroups($conn, (int)$user['id'], $my_pid);
    if ($todos_group_id > 0) {
        $target_group_ids = array_values(array_filter(
            $target_group_ids,
            static fn(int $gid): bool => $gid !== $todos_group_id
        ));
    }
    $has_common_group = !empty(array_intersect($my_group_ids, $target_group_ids));
    if (!$has_common_group) {
        header('Location: usuarios.php?error=unauthorized');
        exit();
    }
}

$can_manage_target = $is_master || $is_self || (
    !$is_master &&
    $can_manage_users_by_level &&
    (int)$user['id'] !== $my_user_id &&
    (int)$user['paroquia_id'] === $my_pid &&
    (int)$user['nivel_acesso'] > $my_level
);
$is_same_level_peer = !$is_master && !$is_self && (int)$user['nivel_acesso'] === $my_level;

$can_edit_photo_for_target = $is_master || $is_self || $can_manage_target;
$can_edit_email_for_target = $is_self || $can_manage_target;
$can_edit_parish_for_target = $is_master;
$can_edit_password_for_target = $is_self; // apenas o próprio usuário pode redefinir sua senha
$can_edit_keyword_for_target = $can_manage_target || $is_self;
$can_delete_target = !$is_self && $can_manage_target;

$visiblePermissions = [
    'perm_ver_calendario' => $is_master || can('ver_calendario'),
    'perm_criar_eventos' => $is_master || can('criar_eventos'),
    'perm_editar_eventos' => $is_master || can('editar_eventos'),
    'perm_excluir_eventos' => $is_master || can('excluir_eventos'),
    'perm_ver_restritos' => $is_master || can('ver_restritos'),
    'perm_cadastrar_usuario' => $is_master || can('cadastrar_usuario'),
    'perm_admin_usuarios' => $is_master || can('admin_usuarios'),
    'perm_admin_sistema' => $is_master || can('admin_sistema'),
    'perm_ver_logs' => $is_master || can('ver_logs'),
    'perm_gerenciar_catalogo' => $is_master || can('gerenciar_catalogo'),
    'perm_gerenciar_grupos' => $is_master || can('gerenciar_grupos'),
];

$permissionLabels = [
    'perm_ver_calendario' => 'Ver Calendario',
    'perm_criar_eventos' => 'Criar Eventos',
    'perm_editar_eventos' => 'Editar Eventos',
    'perm_excluir_eventos' => 'Excluir Eventos',
    'perm_ver_restritos' => 'Ver Restritos',
    'perm_cadastrar_usuario' => 'Cadastrar Usuario',
    'perm_admin_usuarios' => 'Gerenciar Usuarios',
    'perm_admin_sistema' => 'Setup de Sistema (Paroquias)',
    'perm_ver_logs' => 'Acesso a Logs',
    'perm_gerenciar_catalogo' => 'Gerenciar Catalogo',
    'perm_gerenciar_grupos' => 'Gerenciar Grupos de Trabalho',
];

$max_access_level = 7;
$allowed_access_levels = selectable_access_levels_for_user($my_level, $is_master, $max_access_level);
$my_perfil_id = current_user_perfil_id($conn);
$perfis_options = list_perfis_for_user($conn, $my_perfil_id, $is_master);
$allowedPerfilMap = [];
foreach ($perfis_options as $p) {
    $allowedPerfilMap[(int)$p['id']] = $p;
}
$selected_nivel_acesso = isset($_POST['nivel_acesso']) ? (int)$_POST['nivel_acesso'] : (int)($user['nivel_acesso'] ?? $max_access_level);
$selected_perfil_id = isset($_POST['perfil_id']) ? (int)$_POST['perfil_id'] : (int)($user['perfil_id'] ?? pick_default_perfil_id($perfis_options, 9));

$msg = $_GET['msg'] ?? '';
$error = '';
$deleteConfirmPending = ((int)($_GET['delete_confirm'] ?? 0) === 1 && (int)($_SESSION['pending_delete_user_id'] ?? 0) === $id);

// --- CONTEXTO DE GRUPOS (Mover para cima para uso no POST) ---
$allGroupsRaw = getWorkingGroups($conn, (int)($user['paroquia_id'] ?? 0), true);
$myGroups = getUserGroups($conn, $id);

$admin_uid_ctx = (int)($_SESSION['usuario_id'] ?? 0);
$is_master_global_ctx = has_level(0) || $admin_uid_ctx === 1;
$adminGroups_ctx = getUserGroups($conn, $admin_uid_ctx);

$allGroups_ctx = [];
$hiddenGroupsCount_ctx = 0;
foreach ($allGroupsRaw as $g) {
    if ($is_master_global_ctx || in_array((int)$g['id'], $adminGroups_ctx, true)) {
        $allGroups_ctx[] = $g;
    } elseif (in_array((int)$g['id'], $myGroups, true)) {
        $hiddenGroupsCount_ctx++;
    }
}
// -----------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf_token();
    $data = sanitize_post($_POST);

    if (!$is_self && !$can_manage_target) {
        $error = 'Voce nao tem permissao para editar este usuario.';
    } elseif ($is_same_level_peer) {
        $error = 'Voce nao pode editar ou excluir usuarios do mesmo nivel.';
    } elseif (isset($data['delete_request'])) {
        if (!$can_delete_target) {
            $error = 'Voce nao tem permissao para excluir este usuario.';
        } else {
            $_SESSION['pending_delete_user_id'] = $id;
            $_SESSION['pending_delete_user_name'] = (string)($user['nome'] ?? '');
            header('Location: editar_usuario.php?id=' . $id . '&delete_confirm=1');
            exit();
        }
    } elseif (isset($data['final_delete'])) {
        $pendingId = (int)($_SESSION['pending_delete_user_id'] ?? 0);
        $confirmText = strtoupper(trim((string)($data['delete_confirm_text'] ?? '')));

        if (!$can_delete_target || $pendingId !== $id) {
            $error = 'Confirmacao de exclusao invalida.';
        } elseif ($confirmText !== 'EXCLUIR USUARIO') {
            $error = 'Digite EXCLUIR USUARIO para confirmar a exclusao.';
        } else {
            $res = db_query($conn, "SELECT * FROM usuarios WHERE id = ?", [$id]);
            $oldState = $res ? $res->fetch_assoc() : null;
            $oldPhoto = trim((string)($oldState['foto_perfil'] ?? ''));
            if ($oldPhoto !== '' && str_starts_with($oldPhoto, 'img/usuarios/')) {
                $oldPhotoFs = __DIR__ . '/' . $oldPhoto;
                if (is_file($oldPhotoFs)) {
                    @unlink($oldPhotoFs);
                }
            }

            $stmtDelete = $conn->prepare('DELETE FROM usuarios WHERE id = ?');
            if ($stmtDelete) {
                $stmtDelete->bind_param('i', $id);
                if ($stmtDelete->execute()) {
                    unset($_SESSION['pending_delete_user_id'], $_SESSION['pending_delete_user_name']);
                    logAction($conn, 'EXCLUIR_USUARIO', 'usuarios', $id, $oldState ?: []);
                    header('Location: usuarios.php?msg=Usuario excluido com sucesso!');
                    exit();
                }
            }

            $error = 'Nao foi possivel excluir o usuario.';
        }
    } else {
        $nome = trim((string)($data['nome'] ?? ''));
        $email = trim((string)($data['email'] ?? ''));
        $telefone = trim((string)($data['telefone'] ?? ''));
        $sexo = trim((string)($data['sexo'] ?? ''));
        $dt_nasc = !empty($data['data_nascimento']) ? $data['data_nascimento'] : null;
        $novaSenha = (string)($data['nova_senha'] ?? '');
        $confirmarNovaSenha = (string)($data['confirmar_nova_senha'] ?? '');
        $palavraChave = trim((string)($data['palavra_chave'] ?? ''));
        $paroquiaId = $can_edit_parish_for_target ? (int)($data['paroquia_id'] ?? $user['paroquia_id']) : (int)$user['paroquia_id'];
        $perfil_id = (int)($user['perfil_id'] ?? 3);
        $perfil_nome = (string)($user['perfil_nome'] ?? '');
        $nivel_acesso = (int)($user['nivel_acesso'] ?? $max_access_level);

        if ($can_manage_target && !$is_self) {
            $nivel_raw = trim((string)($data['nivel_acesso'] ?? ''));
            if ($nivel_raw !== '') {
                $nivel_candidate = (int)$nivel_raw;
                if ($nivel_candidate < 0 || $nivel_candidate > $max_access_level) {
                    $error = 'Nivel de acesso invalido.';
                } elseif (!$is_master && $nivel_candidate < $my_level) {
                    $error = 'Nivel de acesso invalido para o seu usuario.';
                } else {
                    $nivel_acesso = $nivel_candidate;
                }
            }

            $perfil_raw = trim((string)($data['perfil_id'] ?? ''));
            if ($perfil_raw !== '') {
                $perfil_candidate = (int)$perfil_raw;
                if (!isset($allowedPerfilMap[$perfil_candidate])) {
                    $error = 'Perfil selecionado invalido para o seu nivel.';
                } else {
                    $perfil_id = $perfil_candidate;
                    $perfil_nome = (string)($allowedPerfilMap[$perfil_candidate]['nome'] ?? $perfil_nome);
                }
            }
        }

        if ($nome === '') {
            $error = 'Nome obrigatorio.';
        } elseif ($can_edit_email_for_target && $email === '') {
            $error = 'E-mail obrigatorio.';
        } elseif ($can_edit_password_for_target && ($novaSenha !== '' || $confirmarNovaSenha !== '') && strlen($novaSenha) < 6) {
            $error = 'A nova senha precisa ter no minimo 6 caracteres.';
        } elseif ($can_edit_password_for_target && $novaSenha !== '' && $novaSenha !== $confirmarNovaSenha) {
            $error = 'As senhas informadas nao coincidem.';
        } else {
            $resOld = db_query($conn, "SELECT * FROM usuarios WHERE id = ?", [$id]);
            $oldState = $resOld ? $resOld->fetch_assoc() : null;
            $emailToSave = $can_edit_email_for_target ? $email : (string)($user['email'] ?? '');

            $permValues = [];
            foreach ($visiblePermissions as $field => $visible) {
                $permValues[$field] = $visible ? (isset($_POST[$field]) ? 1 : 0) : (int)($user[$field] ?? 0);
            }

            $sql = "UPDATE usuarios SET 
                    nome = ?, email = ?, sexo = ?, telefone = ?, data_nascimento = ?, 
                    paroquia_id = ?, perfil_id = ?, perfil_nome = ?, nivel_acesso = ?, 
                    perm_ver_calendario = ?, perm_criar_eventos = ?, perm_editar_eventos = ?, perm_excluir_eventos = ?,
                    perm_ver_restritos = ?, perm_cadastrar_usuario = ?, perm_admin_usuarios = ?, perm_admin_sistema = ?, perm_ver_logs = ?,
                    perm_gerenciar_catalogo = ?, perm_gerenciar_grupos = ?
                    WHERE id = ?";

            $stmtUpdate = $conn->prepare($sql);
            if ($stmtUpdate) {
                $stmtUpdate->bind_param(
                    'sssssiisiiiiiiiiiiiii',
                    $nome,
                    $emailToSave,
                    $sexo,
                    $telefone,
                    $dt_nasc,
                    $paroquiaId,
                    $perfil_id,
                    $perfil_nome,
                    $nivel_acesso,
                    $permValues['perm_ver_calendario'],
                    $permValues['perm_criar_eventos'],
                    $permValues['perm_editar_eventos'],
                    $permValues['perm_excluir_eventos'],
                    $permValues['perm_ver_restritos'],
                    $permValues['perm_cadastrar_usuario'],
                    $permValues['perm_admin_usuarios'],
                    $permValues['perm_admin_sistema'],
                    $permValues['perm_ver_logs'],
                    $permValues['perm_gerenciar_catalogo'],
                    $permValues['perm_gerenciar_grupos'],
                    $id
                );

                if ($stmtUpdate->execute()) {
                    if ($can_edit_keyword_for_target && $palavraChave !== '') {
                        $stmtKey = $conn->prepare('UPDATE usuarios SET palavra_chave = ? WHERE id = ?');
                        if ($stmtKey) {
                            $stmtKey->bind_param('si', $palavraChave, $id);
                            $stmtKey->execute();
                        }
                    }

                    if ($can_edit_password_for_target && $novaSenha !== '') {
                        $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
                        $stmtPass = $conn->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');
                        if ($stmtPass) {
                            $stmtPass->bind_param('si', $hash, $id);
                            $stmtPass->execute();
                        }
                    }

                    if ($can_edit_photo_for_target && isset($_POST['remover_foto']) && $_POST['remover_foto'] === '1') {
                        $oldPhoto = trim((string)($oldState['foto_perfil'] ?? ''));
                        if ($oldPhoto !== '' && str_starts_with($oldPhoto, 'img/usuarios/')) {
                            $oldPhotoFs = __DIR__ . '/' . $oldPhoto;
                            if (is_file($oldPhotoFs)) {
                                @unlink($oldPhotoFs);
                            }
                        }
                        $stmtRemovePhoto = $conn->prepare('UPDATE usuarios SET foto_perfil = NULL WHERE id = ?');
                        if ($stmtRemovePhoto) {
                            $stmtRemovePhoto->bind_param('i', $id);
                            $stmtRemovePhoto->execute();
                        }
                        if ($is_self) {
                            $_SESSION['usuario_foto'] = '';
                        }
                    }

                    if (
                        $can_edit_photo_for_target &&
                        isset($_FILES['foto_perfil']) &&
                        (int)($_FILES['foto_perfil']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK
                    ) {
                        $tmpPath = (string)$_FILES['foto_perfil']['tmp_name'];
                        if (is_uploaded_file($tmpPath)) {
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $mime = $finfo ? (string)finfo_file($finfo, $tmpPath) : '';
                            if ($finfo) {
                                finfo_close($finfo);
                            }
                            if (str_starts_with($mime, 'image/')) {
                                $uploadDir = __DIR__ . '/img/usuarios';
                                if (!is_dir($uploadDir)) {
                                    @mkdir($uploadDir, 0777, true);
                                }
                                if (is_dir($uploadDir) && is_writable($uploadDir)) {
                                    $ext = strtolower(pathinfo((string)($_FILES['foto_perfil']['name'] ?? ''), PATHINFO_EXTENSION));
                                    if ($ext === '') {
                                        $ext = preg_replace('/[^a-z0-9]+/i', '', substr($mime, 6));
                                    }
                                    if ($ext === '') {
                                        $ext = 'img';
                                    }
                                    $fileName = 'user_' . $id . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                                    $targetPath = $uploadDir . '/' . $fileName;
                                    if (move_uploaded_file($tmpPath, $targetPath)) {
                                        $oldPhoto = trim((string)($oldState['foto_perfil'] ?? ''));
                                        if ($oldPhoto !== '' && str_starts_with($oldPhoto, 'img/usuarios/')) {
                                            $oldPhotoFs = __DIR__ . '/' . $oldPhoto;
                                            if (is_file($oldPhotoFs)) {
                                                @unlink($oldPhotoFs);
                                            }
                                        }
                                        $newPhoto = 'img/usuarios/' . $fileName;
                                        $stmtPhoto = $conn->prepare('UPDATE usuarios SET foto_perfil = ? WHERE id = ?');
                                        if ($stmtPhoto) {
                                            $stmtPhoto->bind_param('si', $newPhoto, $id);
                                            $stmtPhoto->execute();
                                            if ($is_self) {
                                                $_SESSION['usuario_foto'] = $newPhoto;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $resNew = db_query($conn, "SELECT * FROM usuarios WHERE id = ?", [$id]);
                    $newState = $resNew ? $resNew->fetch_assoc() : null;
                    
                    // SAVE WORKING GROUPS (Scoped)
                    if ($can_manage_target) {
                        $groupIds = isset($_POST['grupos_trabalho']) && is_array($_POST['grupos_trabalho']) ? $_POST['grupos_trabalho'] : [];
                        
                        // Use context variables defined above
                        $manageableIds = $is_master_global_ctx ? array_column($allGroups_ctx, 'id') : $adminGroups_ctx;
                        
                        saveUserGroupsScoped($conn, $id, $groupIds, $manageableIds, $paroquiaId);
                        ensureDefaultVisitorGroup($conn, $paroquiaId);
                    }

                    logAction($conn, 'EDITAR_USUARIO', 'usuarios', $id, ['antigo' => $oldState, 'novo' => $newState]);
                    header('Location: usuarios.php?msg=Usuario atualizado com sucesso!');
                    exit();
                }
            }

            $error = 'Erro ao atualizar dados. O e-mail pode ja estar em uso.';
        }
    }
}

// Fetch Parishes for the dropdown
$pid = current_paroquia_id();
if (has_level(0) || ($_SESSION['usuario_id'] ?? 0) === 1) {
    $parishes = db_query($conn, "SELECT id, nome FROM paroquias ORDER BY nome");
} else {
    $parishes = db_query($conn, "SELECT id, nome FROM paroquias WHERE id = ? ORDER BY nome", [$pid]);
}

// Filter $allGroups based on what the admin can see/manage
$allGroups = $allGroups_ctx;
$hiddenGroupsCount = $hiddenGroupsCount_ctx;
$is_master_global = $is_master_global_ctx;
$adminGroups = $adminGroups_ctx;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Editar Usuario - PASCOM</title>
    <link rel="stylesheet" href="style.css?v=2.4.5">
        <link rel="stylesheet" href="css/responsive.css?v=2.4.5">

    <style>
        select option,
        select optgroup {
            background: #ffffff !important;
            color: #111827 !important;
        }
        select option:checked,
        select option:hover {
            background: #e5ecff !important;
            color: #111827 !important;
        }
        .perm-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            background: rgba(255,255,255,0.02);
            padding: 1.5rem;
            border-radius: 16px;
            border: 1px dashed var(--border);
            margin-top: 1rem;
        }
        .perm-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-size: 0.85rem;
            color: var(--text);
            font-weight: 600;
        }
        .perm-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
        }
        .field-wrap { position: relative; }
        .toggle-pass {
            position: absolute;
            right: 0.8rem;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: 0;
            color: var(--text-dim);
            font-size: 0.75rem;
            font-weight: 800;
            cursor: pointer;
            padding: 0.25rem 0.5rem;
        }
        .actions-row {
            grid-column: span 2;
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }
        .delete-wrap {
            margin-top: 1rem;
            grid-column: span 2;
            border-top: 1px solid var(--border);
            padding-top: 1.5rem;
        }
        .danger-box {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.18);
            border-radius: 18px;
            padding: 1.25rem;
        }
        @media (max-width: 768px) {
            .actions-row { flex-direction: column; }
            .actions-row .btn, .actions-row a, .actions-row form { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="bg-mesh"></div>

    <div class="app-shell">
        <?php include 'sidebar.php'; ?>

        <main class="main-content" style="align-items: center; justify-content: flex-start; overflow-y: auto;">
            <section class="glass animate-in" style="width: 100%; max-width: 800px; padding: 3rem; border-radius: 32px; margin: 2rem auto;">
                <header style="text-align: center; margin-bottom: 2.5rem;">
                    <p style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.15em; color: var(--text-ghost); margin-bottom: 0.5rem;">GESTAO GRANULAR</p>
                    <h1 class="gradient-text">Editar Usuario</h1>
                    <p style="color: var(--text-dim); font-size: 0.95rem;">Atualize os campos permitidos para <b><?= h($user['nome']) ?></b>.</p>
                </header>

                <?php if ($error): ?> <?= alert('error', h($error)) ?> <?php endif; ?>
                <?php if ($msg): ?> <?= alert('success', h($msg)) ?> <?php endif; ?>

                <?php if ($deleteConfirmPending): ?>
                    <div class="danger-box" style="margin-bottom: 1.5rem;">
                        <h3 style="margin-bottom: 0.6rem; color: #fca5a5;">Confirmacao final de exclusao</h3>
                        <p style="font-size: 0.9rem; color: var(--text-dim); margin-bottom: 1rem;">
                            Digite <b>EXCLUIR USUARIO</b> para remover permanentemente este cadastro.
                        </p>
                        <form method="POST" style="display: grid; gap: 1rem;">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>CONFIRMACAO FINAL</label>
                                <input type="text" name="delete_confirm_text" placeholder="EXCLUIR USUARIO" required>
                            </div>
                            <button type="submit" name="final_delete" value="1" class="btn btn-primary shimmer" style="background: linear-gradient(135deg, #dc2626, #ef4444);">
                                Excluir definitivamente
                            </button>
                        </form>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="edit-user-form" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <?php if (!$is_same_level_peer): ?>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>NOME COMPLETO</label>
                        <input type="text" name="nome" value="<?= h($user['nome']) ?>" required>
                    </div>

                    <?php if ($can_edit_email_for_target): ?>
                    <div class="form-group">
                        <label>E-MAIL INSTITUCIONAL</label>
                        <input type="email" name="email" value="<?= h($user['email']) ?>" required>
                    </div>
                    <?php endif; ?>

                    <?php if ($can_edit_parish_for_target): ?>
                    <div class="form-group">
                        <label>PAROQUIA DESIGNADA</label>
                        <select name="paroquia_id">
                            <option value="0">Global / Master</option>
                            <?php while ($p = $parishes->fetch_assoc()): ?>
                                <option value="<?= $p['id'] ?>" <?= $p['id'] == $user['paroquia_id'] ? 'selected' : '' ?>><?= h($p['nome']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label>TELEFONE</label>
                        <input type="text" name="telefone" value="<?= h($user['telefone']) ?>">
                    </div>

                    <div class="form-group">
                        <label>DATA DE ANIVERSARIO</label>
                        <input type="date" name="data_nascimento" value="<?= h($user['data_nascimento']) ?>">
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label>GENERO</label>
                        <select name="sexo">
                            <option value="M" <?= $user['sexo'] == 'M' || $user['sexo'] == 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                            <option value="F" <?= $user['sexo'] == 'F' || $user['sexo'] == 'Feminino' ? 'selected' : '' ?>>Feminino</option>
                        </select>
                    </div>
                    <?php else: ?>
                    <div style="grid-column: span 2; padding: 1rem 1.2rem; border: 1px solid var(--border); border-radius: 12px; color: var(--text-dim); font-size: 0.9rem;">
                        Usuarios do mesmo nivel podem ser visualizados, mas nao podem ser editados ou excluidos.
                    </div>
                    <?php endif; ?>

                    <?php if (!$is_same_level_peer && !$is_self && $can_manage_users_by_level && ($can_manage_target || array_filter($visiblePermissions))): ?>
                    <div style="grid-column: span 2; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border);">
                        <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Configurações de Acesso e Grupos</h3>

                        <?php if ($can_manage_target): ?>
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                            <div class="form-group" style="margin:0;">
                                <label>NIVEL DE ACESSO</label>
                                <select name="nivel_acesso">
                                    <?php foreach ($allowed_access_levels as $lvl): ?>
                                        <option value="<?= (int)$lvl ?>" <?= ((int)$lvl === (int)$selected_nivel_acesso) ? 'selected' : '' ?>>
                                            <?= h(getAccessLabelV2((int)$lvl)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group" style="margin:0;">
                                <label>PERFIL</label>
                                <select name="perfil_id">
                                    <?php foreach ($perfis_options as $pf): ?>
                                        <option value="<?= (int)$pf['id'] ?>" <?= ((int)$pf['id'] === (int)$selected_perfil_id) ? 'selected' : '' ?>>
                                            <?= h($pf['nome']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Permissões do Sistema -->
                        <?php if (array_filter($visiblePermissions)): ?>
                        <div style="margin-bottom: 2rem;">
                            <p style="font-size: 0.7rem; font-weight: 800; color: var(--text-ghost); margin-bottom: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">Permissões de Sistema</p>
                            <div class="perm-grid">
                                <?php foreach ($permissionLabels as $field => $label): ?>
                                    <?php if (!empty($visiblePermissions[$field])): ?>
                                        <label class="perm-item">
                                            <input type="checkbox" name="<?= h($field) ?>" id="<?= h($field) ?>" <?= !empty($user[$field]) ? 'checked' : '' ?>>
                                            <?= h($label) ?>
                                        </label>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Grupos de Trabalho -->
                        <div style="margin-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 0.8rem;">
                                <p style="font-size: 0.7rem; font-weight: 800; color: var(--text-ghost); margin-bottom: 0; text-transform: uppercase; letter-spacing: 0.05em;">Associação a Grupos de Trabalho</p>
                                <?php if ($hiddenGroupsCount_ctx > 0): ?>
                                    <span style="font-size: 0.65rem; font-weight: 800; color: #f59e0b; background: rgba(245, 158, 11, 0.1); padding: 0.2rem 0.5rem; border-radius: 4px; border: 1px solid rgba(245, 158, 11, 0.2);">
                                        + <?= $hiddenGroupsCount_ctx ?> grupo(s) em outros departamentos
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if ($can_manage_target): ?>
                                <div class="perm-grid">
                                    <?php if (empty($allGroups)): ?>
                                        <p style="font-size: 0.85rem; color: var(--text-ghost);">Nenhum grupo comum sob sua gestão.</p>
                                    <?php else: ?>
                                        <?php foreach ($allGroups as $g): ?>
                                            <label class="perm-item" style="border-left: 3px solid <?= $g['cor'] ?>; padding-left: 0.8rem;">
                                                <input type="checkbox" name="grupos_trabalho[]" value="<?= $g['id'] ?>" <?= in_array((int)$g['id'], $myGroups, true) ? 'checked' : '' ?>>
                                                <div style="display: flex; flex-direction: column;">
                                                    <span style="font-weight: 800;"><?= h($g['nome']) ?></span>
                                                    <?php if (!$g['visivel']): ?>
                                                        <span style="font-size: 0.65rem; color: #f59e0b; text-transform: uppercase;">Oculto</span>
                                                    <?php endif; ?>
                                                </div>
                                            </label>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <?php 
                                    $groupsFound = false;
                                    foreach ($allGroups as $g): 
                                        if (in_array((int)$g['id'], $myGroups, true)): 
                                            $groupsFound = true;
                                    ?>
                                        <span style="padding: 0.5rem 1rem; border-radius: 99px; background: <?= $g['cor'] ?>15; border: 1px solid <?= $g['cor'] ?>44; color: <?= $g['cor'] ?>; font-size: 0.8rem; font-weight: 800;">
                                            <?= h($g['nome']) ?>
                                        </span>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    if (!$groupsFound && $hiddenGroupsCount === 0) echo '<span style="font-size:0.85rem; color:var(--text-ghost);">Nenhum grupo associado.</span>';
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php else: ?>
                        <input type="hidden" name="perfil_id" value="<?= (int)$user['perfil_id'] ?>">
                        
                        <!-- Se for auto-edição (perfil), ainda mostra os grupos mas de forma resumida -->
                        <?php if ($is_self): ?>
                        <div style="grid-column: span 2; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border);">
                             <h3 style="margin-bottom: 1rem; color: var(--primary);">Meus Grupos de Trabalho</h3>
                             <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <?php 
                                $groupsFound = false;
                                foreach ($allGroups as $g): 
                                    if (in_array((int)$g['id'], $myGroups, true)): 
                                        $groupsFound = true;
                                ?>
                                    <span style="padding: 0.5rem 1rem; border-radius: 99px; background: <?= $g['cor'] ?>15; border: 1px solid <?= $g['cor'] ?>44; color: <?= $g['cor'] ?>; font-size: 0.8rem; font-weight: 800;">
                                        <?= h($g['nome']) ?>
                                    </span>
                                <?php 
                                    endif;
                                endforeach; 
                                if (!$groupsFound) echo '<span style="font-size:0.85rem; color:var(--text-ghost);">Você ainda não foi associado a nenhum grupo.</span>';
                                ?>
                             </div>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>


                    <?php if ($can_edit_password_for_target): ?>
                    <div class="form-group" style="grid-column: span 2; margin-top: 1rem; border-top: 1px solid var(--border); padding-top: 2rem;">
                        <label>REDEFINIR SENHA (OPCIONAL)</label>
                        <div class="field-wrap">
                            <input type="password" name="nova_senha" id="editNovaSenha" placeholder="Deixe em branco para manter a atual" autocomplete="new-password">
                            <button type="button" class="toggle-pass" data-target="editNovaSenha">Mostrar</button>
                        </div>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label>CONFIRMAR NOVA SENHA</label>
                        <div class="field-wrap">
                            <input type="password" name="confirmar_nova_senha" id="editConfirmarNovaSenha" placeholder="Repita a nova senha" autocomplete="new-password">
                            <button type="button" class="toggle-pass" data-target="editConfirmarNovaSenha">Mostrar</button>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($can_edit_photo_for_target): ?>
                        <?php if (!empty($user['foto_perfil']) && file_exists(__DIR__ . '/' . $user['foto_perfil'])): ?>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>FOTO ATUAL</label>
                            <div style="display:flex; align-items:center; gap:1rem;">
                                <img src="<?= h($user['foto_perfil']) ?>?v=<?= time() ?>" alt="Foto atual" style="width:56px; height:56px; border-radius:12px; object-fit:cover; border:1px solid var(--border);">
                                <button type="button" class="btn btn-ghost" style="height:44px;" onclick="return confirmForm(this, 'Deseja remover a foto de perfil atual?', function(f) { const input = document.createElement('input'); input.type='hidden'; input.name='remover_foto'; input.value='1'; f.appendChild(input); f.submit(); })">Remover foto atual</button>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>TROCAR FOTO DE PERFIL (OPCIONAL)</label>
                            <input type="file" name="foto_perfil" accept="image/*">
                        </div>
                    <?php endif; ?>

                    <?php if ($can_edit_keyword_for_target): ?>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>TROCAR PALAVRA-CHAVE (RECUPERACAO)</label>
                        <div class="field-wrap">
                            <input type="password" name="palavra_chave" id="editPalavraChave" placeholder="Digite a nova palavra-chave" autocomplete="new-password">
                            <button type="button" class="toggle-pass" data-target="editPalavraChave">Mostrar</button>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="actions-row">
                        <?php if (!$is_same_level_peer): ?>
                        <button type="submit" class="btn btn-primary shimmer" style="flex: 2;">Confirmar Alteracoes</button>
                        <?php endif; ?>
                        <a href="usuarios.php" class="btn btn-ghost" style="flex: 1;">Cancelar</a>
                        <?php if ($can_delete_target && !$is_same_level_peer): ?>
                            <button type="submit" name="delete_request" value="1" class="btn btn-ghost" style="flex: 1; border-color: rgba(239,68,68,0.5); color: #fca5a5;">Excluir Usuario</button>
                        <?php endif; ?>
                    </div>
                </form>

            </section>
        </main>
    </div>

    <script>
        document.querySelectorAll('.toggle-pass').forEach((btn) => {
            btn.addEventListener('click', () => {
                const target = document.getElementById(btn.dataset.target);
                if (!target) return;
                const hidden = target.type === 'password';
                target.type = hidden ? 'text' : 'password';
                btn.textContent = hidden ? 'Ocultar' : 'Mostrar';
            });
        });
    </script>
</body>
</html>
