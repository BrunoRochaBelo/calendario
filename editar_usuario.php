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

// Only Master can edit anyone. Supervisors can edit their own parish members (but for now let's keep it simple: only Master).
if (!$is_master) {
    header('Location: usuarios.php?error=unauthorized');
    exit();
}

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
        $sql = "UPDATE usuarios SET nome = ?, email = ?, sexo = ?, telefone = ?, paroquia_id = ?, nivel_acesso = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssiii', $data['nome'], $data['email'], $data['sexo'], $data['telefone'], $data['paroquia_id'], $data['nivel_acesso'], $id);
        
        if ($stmt->execute()) {
            // Optional: Password update logic
            if (!empty($data['nova_senha'])) {
                $hash = password_hash($data['nova_senha'], PASSWORD_DEFAULT);
                $conn->query("UPDATE usuarios SET senha = '$hash' WHERE id = $id");
            }
            
            logAction($conn, 'EDITAR_USUARIO', 'usuarios', $id, "Dados atualizados para: " . $data['nome']);
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
</head>
<body>
    <div class="bg-mesh"></div>

    <div class="app-shell">
        <?php include 'sidebar.php'; ?>

        <main class="main-content" style="display: flex; align-items: center; justify-content: center;">
            <section class="glass animate-in" style="width: 100%; max-width: 700px; padding: 4rem; border-radius: 32px;">
                <header style="text-align: center; margin-bottom: 3rem;">
                    <p style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.15em; color: var(--text-ghost); margin-bottom: 0.5rem;">GERENCIAMENTO</p>
                    <h1 class="gradient-text">Editar Perfil</h1>
                    <p style="color: var(--text-dim); font-size: 0.95rem;">Atualize as permissões e dados cadastrais de <b><?= h($user['nome']) ?></b>.</p>
                </header>

                <?php if ($error): ?> <?= alert('error', h($error)) ?> <?php endif; ?>

                <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group" style="grid-column: span 2;">
                        <label>NOME COMPLETO</label>
                        <input type="text" name="nome" value="<?= h($user['nome']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>E-MAIL INSTITUCIONAL</label>
                        <input type="email" name="email" value="<?= h($user['email']) ?>" required>
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

                    <div class="form-group">
                        <label>NÍVEL DE ACESSO</label>
                        <select name="nivel_acesso">
                            <option value="0" <?= $user['nivel_acesso'] == 0 ? 'selected' : '' ?>>Master (Desenvolvedor)</option>
                            <option value="1" <?= $user['nivel_acesso'] == 1 ? 'selected' : '' ?>>Supervisor (Bispo/Padre)</option>
                            <option value="2" <?= $user['nivel_acesso'] == 2 ? 'selected' : '' ?>>Gerente (Pascom Local)</option>
                            <option value="3" <?= $user['nivel_acesso'] == 3 ? 'selected' : '' ?>>Usuário / Visitante</option>
                        </select>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label>PARÓQUIA DESIGNADA</label>
                        <select name="paroquia_id">
                            <option value="0">Global / Master</option>
                            <?php while($p = $parishes->fetch_assoc()): ?>
                                <option value="<?= $p['id'] ?>" <?= $p['id'] == $user['paroquia_id'] ? 'selected' : '' ?>><?= h($p['nome']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group" style="grid-column: span 2; margin-top: 1rem; border-top: 1px solid var(--border); padding-top: 2rem;">
                        <label>REDEFINIR SENHA (OPCIONAL)</label>
                        <input type="password" name="nova_senha" placeholder="Deixe em branco para manter a atual">
                    </div>

                    <div style="grid-column: span 2; display: flex; gap: 1rem; margin-top: 1rem;">
                        <button type="submit" class="btn btn-primary shimmer" style="flex: 2;">Salvar Alterações</button>
                        <a href="usuarios.php" class="btn btn-ghost" style="flex: 1;">Voltar</a>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
