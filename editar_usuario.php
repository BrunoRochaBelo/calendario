<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Admin Console / Edit User (v2.0)
 * Profile Updates · Role Management · Premium UI
 * ═══════════════════════════════════════════════════════ */

require_once 'functions.php';
requireLogin();
ensureUserPhotoColumn($conn);

$id = (int)($_GET['id'] ?? 0);
$is_master = has_level(0);
$my_user_id = (int)($_SESSION['usuario_id'] ?? 0);
$my_level = (int)($_SESSION['usuario_nivel'] ?? 99);
$my_pid = (int)($_SESSION['paroquia_id'] ?? 0);
$is_self = ($id === $my_user_id);
$can_grant_restricted = $is_master || can('ver_restritos');
$can_edit_photo_for_target = $is_master || $is_self;

requirePerm('admin_usuarios');

// 1. Fetch User Data
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
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
        (int)$user['nivel_acesso'] <= $my_level
    )
) {
    header('Location: usuarios.php?error=unauthorized');
    exit();
}

$msg = $_GET['msg'] ?? '';
$error = '';

// 2. Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = sanitize_post($_POST);
    
    if (empty($data['nome']) || empty($data['email'])) {
        $error = 'Nome e e-mail são obrigatórios.';
    } else {
        $oldResult = $conn->query("SELECT * FROM usuarios WHERE id = $id");
        $oldState = $oldResult->fetch_assoc();
        
        $dt_nasc = !empty($data['data_nascimento']) ? $data['data_nascimento'] : null;
        $perfil_id = (int)($user['perfil_id'] ?? 3);
        
        // Grab Permission flags from POST
        $p_ver_cal = isset($_POST['perm_ver_calendario']) ? 1 : 0;
        $p_cri_evt = isset($_POST['perm_criar_eventos']) ? 1 : 0;
        $p_edi_evt = isset($_POST['perm_editar_eventos']) ? 1 : 0;
        $p_exc_evt = isset($_POST['perm_excluir_eventos']) ? 1 : 0;
        $p_ver_res = $can_grant_restricted
            ? (isset($_POST['perm_ver_restritos']) ? 1 : 0)
            : (int)$user['perm_ver_restritos'];
        $p_cad_usu = isset($_POST['perm_cadastrar_usuario']) ? 1 : 0;
        $p_adm_usu = isset($_POST['perm_admin_usuarios']) ? 1 : 0;
        $p_adm_sis = isset($_POST['perm_admin_sistema']) ? 1 : 0;
        $p_ver_log = isset($_POST['perm_ver_logs']) ? 1 : 0;
        
        $sql = "UPDATE usuarios SET 
                nome = ?, email = ?, sexo = ?, telefone = ?, data_nascimento = ?, 
                paroquia_id = ?, perfil_id = ?, 
                perm_ver_calendario = ?, perm_criar_eventos = ?, perm_editar_eventos = ?, perm_excluir_eventos = ?,
                perm_ver_restritos = ?, perm_cadastrar_usuario = ?, perm_admin_usuarios = ?, perm_admin_sistema = ?, perm_ver_logs = ? 
                WHERE id = ?";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssssiiiiiiiiiiii', 
            $data['nome'], $data['email'], $data['sexo'], $data['telefone'], $dt_nasc, 
            $data['paroquia_id'], $perfil_id,
            $p_ver_cal, $p_cri_evt, $p_edi_evt, $p_exc_evt,
            $p_ver_res, $p_cad_usu, $p_adm_usu, $p_adm_sis, $p_ver_log,
            $id
        );
        
        if ($stmt->execute()) {
            if ($is_self && !empty($data['palavra_chave'])) {
                $keyword = trim((string)$data['palavra_chave']);
                if ($keyword !== '') {
                    $stmtKey = $conn->prepare("UPDATE usuarios SET palavra_chave = ? WHERE id = ?");
                    if ($stmtKey) {
                        $stmtKey->bind_param('si', $keyword, $id);
                        $stmtKey->execute();
                    }
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
                $stmtRemovePhoto = $conn->prepare("UPDATE usuarios SET foto_perfil = NULL WHERE id = ?");
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
                                $stmtPhoto = $conn->prepare("UPDATE usuarios SET foto_perfil = ? WHERE id = ?");
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

            // Optional: Password update logic
            if (!empty($data['nova_senha'])) {
                $hash = password_hash($data['nova_senha'], PASSWORD_DEFAULT);
                $conn->query("UPDATE usuarios SET senha = '$hash' WHERE id = $id");
            }
            
            // Reload user data for logging state difference properly
            $newState = $conn->query("SELECT * FROM usuarios WHERE id = $id")->fetch_assoc();
            
            logAction($conn, 'EDITAR_USUARIO', 'usuarios', $id, ['antigo' => $oldState, 'novo' => $newState]);
            header("Location: usuarios.php?msg=Usuário atualizado com sucesso!");
            exit();
        } else {
            $error = 'Erro ao atualizar dados. O e-mail pode já estar em uso.';
        }
    }
}

$parishes = $conn->query("SELECT id, nome FROM paroquias ORDER BY nome");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Editar Usuário — PASCOM</title>
    <link rel="stylesheet" href="style.css">
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
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;
            background: rgba(255,255,255,0.02); padding: 1.5rem; border-radius: 16px; border: 1px dashed var(--border);
            margin-top: 1rem;
        }
        .perm-item {
            display: flex; align-items: center; gap: 0.8rem;
            font-size: 0.85rem; color: var(--text); font-weight: 600;
        }
        .perm-item input[type="checkbox"] {
            width: 18px; height: 18px; accent-color: var(--primary); cursor: pointer;
        }
        .apply-btn { font-size: 0.75rem; padding: 0.4rem 0.8rem; border-radius: 8px; flex-shrink: 0; }
    </style>
</head>
<body>
    <div class="bg-mesh"></div>

    <div class="app-shell">
        <?php include 'sidebar.php'; ?>

        <main class="main-content" style="align-items: center; justify-content: flex-start; overflow-y: auto;">
            <section class="glass animate-in" style="width: 100%; max-width: 800px; padding: 3rem; border-radius: 32px; margin: 2rem auto;">
                <header style="text-align: center; margin-bottom: 2.5rem;">
                    <p style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.15em; color: var(--text-ghost); margin-bottom: 0.5rem;">GESTÃO GRANULAR</p>
                    <h1 class="gradient-text">Editar e Permissões</h1>
                    <p style="color: var(--text-dim); font-size: 0.95rem;">Atualize as permissões e dados cadastrais de <b><?= h($user['nome']) ?></b>.</p>
                </header>

                <?php if ($error): ?> <?= alert('error', h($error)) ?> <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    
                    <!-- Basic Info -->
                    <div class="form-group" style="grid-column: span 2;">
                        <label>NOME COMPLETO</label>
                        <input type="text" name="nome" value="<?= h($user['nome']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>E-MAIL INSTITUCIONAL</label>
                        <input type="email" name="email" value="<?= h($user['email']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>PARÓQUIA DESIGNADA</label>
                        <select name="paroquia_id">
                            <option value="0">Global / Master</option>
                            <?php while($p = $parishes->fetch_assoc()): ?>
                                <option value="<?= $p['id'] ?>" <?= $p['id'] == $user['paroquia_id'] ? 'selected' : '' ?>><?= h($p['nome']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>TELEFONE</label>
                        <input type="text" name="telefone" value="<?= h($user['telefone']) ?>">
                    </div>

                    <div class="form-group">
                        <label>DATA DE ANIVERSÁRIO</label>
                        <input type="date" name="data_nascimento" value="<?= h($user['data_nascimento']) ?>">
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label>GÊNERO</label>
                        <select name="sexo">
                            <option value="M" <?= $user['sexo'] == 'M' || $user['sexo'] == 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                            <option value="F" <?= $user['sexo'] == 'F' || $user['sexo'] == 'Feminino' ? 'selected' : '' ?>>Feminino</option>
                            <option value="Outro" <?= $user['sexo'] == 'Outro' ? 'selected' : '' ?>>Outro</option>
                        </select>
                    </div>

                    <!-- Permissions Matrix -->
                    <div style="grid-column: span 2; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border);">
                        <h3 style="margin-bottom: 1rem; color: var(--primary);">Controle de Privilégios Individuais</h3>
                        <p style="font-size: 0.8rem; color: var(--text-dim); margin-bottom: 1rem;">O usuário carregará essas permissões no sistema. Você pode aplicar o molde de um Perfil base e customizar as opções logo abaixo.</p>

                        <input type="hidden" name="perfil_id" value="<?= (int)$user['perfil_id'] ?>">

                        <div class="perm-grid">
                            <label class="perm-item"><input type="checkbox" name="perm_ver_calendario" id="pm_ver_calendario" <?= $user['perm_ver_calendario'] ? 'checked' : '' ?>> Ver Calendário</label>
                            <label class="perm-item"><input type="checkbox" name="perm_criar_eventos" id="pm_criar_eventos" <?= $user['perm_criar_eventos'] ? 'checked' : '' ?>> Criar Eventos</label>
                            <label class="perm-item"><input type="checkbox" name="perm_editar_eventos" id="pm_editar_eventos" <?= $user['perm_editar_eventos'] ? 'checked' : '' ?>> Editar Eventos</label>
                            <label class="perm-item"><input type="checkbox" name="perm_excluir_eventos" id="pm_excluir_eventos" <?= $user['perm_excluir_eventos'] ? 'checked' : '' ?>> Excluir Eventos</label>
                            <label class="perm-item">
                                <input
                                    type="checkbox"
                                    name="perm_ver_restritos"
                                    id="pm_ver_restritos"
                                    <?= $user['perm_ver_restritos'] ? 'checked' : '' ?>
                                    <?= !$can_grant_restricted ? 'disabled' : '' ?>
                                >
                                Ver Restritos
                            </label>
                            <label class="perm-item"><input type="checkbox" name="perm_cadastrar_usuario" id="pm_cadastrar_usuario" <?= $user['perm_cadastrar_usuario'] ? 'checked' : '' ?>> Cadastrar Usuário</label>
                            <label class="perm-item"><input type="checkbox" name="perm_admin_usuarios" id="pm_admin_usuarios" <?= $user['perm_admin_usuarios'] ? 'checked' : '' ?>> Gerenciar Usuários</label>
                            <label class="perm-item"><input type="checkbox" name="perm_admin_sistema" id="pm_admin_sistema" <?= $user['perm_admin_sistema'] ? 'checked' : '' ?>> Setup de Sistema (Paróquias)</label>
                            <label class="perm-item"><input type="checkbox" name="perm_ver_logs" id="pm_ver_logs" <?= $user['perm_ver_logs'] ? 'checked' : '' ?>> Acesso a Logs</label>
                        </div>
                    </div>

                    <div class="form-group" style="grid-column: span 2; margin-top: 1rem; border-top: 1px solid var(--border); padding-top: 2rem;">
                        <label>REDEFINIR SENHA (OPCIONAL)</label>
                        <input type="password" name="nova_senha" placeholder="Deixe em branco para manter a atual">
                    </div>

                    <?php if ($can_edit_photo_for_target): ?>
                    <?php if (!empty($user['foto_perfil']) && file_exists(__DIR__ . '/' . $user['foto_perfil'])): ?>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>FOTO ATUAL</label>
                        <div style="display:flex; align-items:center; gap:1rem;">
                            <img src="<?= h($user['foto_perfil']) ?>?v=<?= time() ?>" alt="Foto atual" style="width:56px; height:56px; border-radius:12px; object-fit:cover; border:1px solid var(--border);">
                            <button type="submit" name="remover_foto" value="1" class="btn btn-ghost" style="height:44px;" onclick="return confirm('Deseja remover a foto de perfil atual?')">Remover foto atual</button>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>TROCAR FOTO DE PERFIL (OPCIONAL)</label>
                        <input type="file" name="foto_perfil" accept="image/*">
                    </div>
                    <?php endif; ?>

                    <?php if ($is_self): ?>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>TROCAR PALAVRA-CHAVE (RECUPERAÇÃO)</label>
                        <input type="text" name="palavra_chave" placeholder="Digite a nova palavra-chave">
                    </div>
                    <?php endif; ?>

                    <div style="grid-column: span 2; display: flex; gap: 1rem; margin-top: 1rem;">
                        <button type="submit" class="btn btn-primary shimmer" style="flex: 2;">Confirmar Identidade e Permissões</button>
                        <a href="usuarios.php" class="btn btn-ghost" style="flex: 1;">Cancelar</a>
                    </div>
                </form>
            </section>
        </main>
    </div>

    <script>
    // presets de perfil removidos: permissões vêm apenas da tabela usuarios
    </script>
</body>
</html>
