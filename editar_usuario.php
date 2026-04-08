<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Admin Console / Edit User (v2.0)
 * Profile Updates · Role Management · Premium UI
 * ═══════════════════════════════════════════════════════ */

require_once 'functions.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
$is_master = has_level(0);

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
        $perfil_id = (int)($data['perfil_id'] ?? 3);
        
        // Grab Permission flags from POST
        $p_ver_cal = isset($_POST['perm_ver_calendario']) ? 1 : 0;
        $p_cri_evt = isset($_POST['perm_criar_eventos']) ? 1 : 0;
        $p_edi_evt = isset($_POST['perm_editar_eventos']) ? 1 : 0;
        $p_exc_evt = isset($_POST['perm_excluir_eventos']) ? 1 : 0;
        $p_ver_res = isset($_POST['perm_ver_restritos']) ? 1 : 0;
        $p_adm_usu = isset($_POST['perm_admin_usuarios']) ? 1 : 0;
        $p_adm_sis = isset($_POST['perm_admin_sistema']) ? 1 : 0;
        $p_ver_log = isset($_POST['perm_ver_logs']) ? 1 : 0;
        
        $sql = "UPDATE usuarios SET 
                nome = ?, email = ?, sexo = ?, telefone = ?, data_nascimento = ?, 
                paroquia_id = ?, perfil_id = ?, 
                perm_ver_calendario = ?, perm_criar_eventos = ?, perm_editar_eventos = ?, perm_excluir_eventos = ?,
                perm_ver_restritos = ?, perm_admin_usuarios = ?, perm_admin_sistema = ?, perm_ver_logs = ? 
                WHERE id = ?";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssssiiiiiiiiiii', 
            $data['nome'], $data['email'], $data['sexo'], $data['telefone'], $dt_nasc, 
            $data['paroquia_id'], $perfil_id,
            $p_ver_cal, $p_cri_evt, $p_edi_evt, $p_exc_evt,
            $p_ver_res, $p_adm_usu, $p_adm_sis, $p_ver_log,
            $id
        );
        
        if ($stmt->execute()) {
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

// Fetch profiles explicitly with all flags to build JSON map
$res_perfis = $conn->query("SELECT * FROM perfis ORDER BY nome");
$perfis_map = [];
$perfis_html = '';
while($pf = $res_perfis->fetch_assoc()) {
    $perfis_html .= '<option value="' . $pf['id'] . '" ' . ($user['perfil_id'] == $pf['id'] ? 'selected' : '') . '>' . h($pf['nome']) . '</option>';
    $perfis_map[$pf['id']] = [
        'perm_ver_calendario' => (int)$pf['perm_ver_calendario'],
        'perm_criar_eventos' => (int)$pf['perm_criar_eventos'],
        'perm_editar_eventos' => (int)$pf['perm_editar_eventos'],
        'perm_excluir_eventos' => (int)$pf['perm_excluir_eventos'],
        'perm_ver_restritos' => (int)$pf['perm_ver_restritos'],
        'perm_admin_usuarios' => (int)$pf['perm_admin_usuarios'],
        'perm_admin_sistema' => (int)$pf['perm_admin_sistema'],
        'perm_ver_logs' => (int)$pf['perm_ver_logs']
    ];
}

$perfis_json = json_encode($perfis_map);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Editar Usuário — PASCOM</title>
    <link rel="stylesheet" href="style.css">
    <style>
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

                <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    
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

                        <div class="form-group">
                            <label>PERFIL BASE (PRESET)</label>
                            <div style="display: flex; gap: 0.5rem; align-items: center;">
                                <select name="perfil_id" id="perfil_select" style="flex: 1;">
                                    <?= $perfis_html ?>
                                </select>
                                <button type="button" onclick="applyProfile()" class="btn btn-ghost apply-btn">OK (Copiar)</button>
                            </div>
                        </div>

                        <div class="perm-grid">
                            <label class="perm-item"><input type="checkbox" name="perm_ver_calendario" id="pm_ver_calendario" <?= $user['perm_ver_calendario'] ? 'checked' : '' ?>> Ver Calendário</label>
                            <label class="perm-item"><input type="checkbox" name="perm_criar_eventos" id="pm_criar_eventos" <?= $user['perm_criar_eventos'] ? 'checked' : '' ?>> Criar Eventos</label>
                            <label class="perm-item"><input type="checkbox" name="perm_editar_eventos" id="pm_editar_eventos" <?= $user['perm_editar_eventos'] ? 'checked' : '' ?>> Editar Eventos</label>
                            <label class="perm-item"><input type="checkbox" name="perm_excluir_eventos" id="pm_excluir_eventos" <?= $user['perm_excluir_eventos'] ? 'checked' : '' ?>> Excluir Eventos</label>
                            <label class="perm-item"><input type="checkbox" name="perm_ver_restritos" id="pm_ver_restritos" <?= $user['perm_ver_restritos'] ? 'checked' : '' ?>> Ver Restritos</label>
                            <label class="perm-item"><input type="checkbox" name="perm_admin_usuarios" id="pm_admin_usuarios" <?= $user['perm_admin_usuarios'] ? 'checked' : '' ?>> Gerenciar Usuários</label>
                            <label class="perm-item"><input type="checkbox" name="perm_admin_sistema" id="pm_admin_sistema" <?= $user['perm_admin_sistema'] ? 'checked' : '' ?>> Setup de Sistema (Paróquias)</label>
                            <label class="perm-item"><input type="checkbox" name="perm_ver_logs" id="pm_ver_logs" <?= $user['perm_ver_logs'] ? 'checked' : '' ?>> Acesso a Logs</label>
                        </div>
                    </div>

                    <div class="form-group" style="grid-column: span 2; margin-top: 1rem; border-top: 1px solid var(--border); padding-top: 2rem;">
                        <label>REDEFINIR SENHA (OPCIONAL)</label>
                        <input type="password" name="nova_senha" placeholder="Deixe em branco para manter a atual">
                    </div>

                    <div style="grid-column: span 2; display: flex; gap: 1rem; margin-top: 1rem;">
                        <button type="submit" class="btn btn-primary shimmer" style="flex: 2;">Confirmar Identidade e Permissões</button>
                        <a href="usuarios.php" class="btn btn-ghost" style="flex: 1;">Cancelar</a>
                    </div>
                </form>
            </section>
        </main>
    </div>

    <script>
    const perfisMap = <?= $perfis_json ?>;
    
    function applyProfile() {
        const sel = document.getElementById('perfil_select').value;
        const perms = perfisMap[sel];
        
        if (perms) {
            document.getElementById('pm_ver_calendario').checked = perms.perm_ver_calendario === 1;
            document.getElementById('pm_criar_eventos').checked = perms.perm_criar_eventos === 1;
            document.getElementById('pm_editar_eventos').checked = perms.perm_editar_eventos === 1;
            document.getElementById('pm_excluir_eventos').checked = perms.perm_excluir_eventos === 1;
            document.getElementById('pm_ver_restritos').checked = perms.perm_ver_restritos === 1;
            document.getElementById('pm_admin_usuarios').checked = perms.perm_admin_usuarios === 1;
            document.getElementById('pm_admin_sistema').checked = perms.perm_admin_sistema === 1;
            document.getElementById('pm_ver_logs').checked = perms.perm_ver_logs === 1;
            
            // Add visual cue
            const grid = document.querySelector('.perm-grid');
            grid.style.borderColor = 'var(--primary)';
            setTimeout(() => grid.style.borderColor = 'var(--border)', 1000);
        } else {
            alert('Falha ao carregar as permissões do perfil selecionado.');
        }
    }
    </script>
</body>
</html>
