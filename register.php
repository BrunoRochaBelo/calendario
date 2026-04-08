<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Admin Console / User Registration (v2.0)
 * Onboarding · RBAC Setup · Premium UI
 * ═══════════════════════════════════════════════════════ */

require_once 'functions.php';
requireLogin();

requirePerm('admin_usuarios');

$pid = current_paroquia_id();
$msg = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = sanitize_post($_POST);
    
    if (empty($data['nome']) || empty($data['email']) || empty($data['senha'])) {
        $error = 'Por favor, preencha nome, e-mail e senha.';
    } else {
        $hash = password_hash($data['senha'], PASSWORD_DEFAULT);
        $perfil_id = 9; // Força Visitante padrão
        $target_pid = (int)($data['paroquia_id'] ?: $pid);
        $dt_nasc = !empty($data['data_nascimento']) ? $data['data_nascimento'] : null;
        
        $sql = "INSERT INTO usuarios (nome, email, senha, sexo, telefone, data_nascimento, paroquia_id, perfil_id, ativo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssssii', $data['nome'], $data['email'], $hash, $data['sexo'], $data['telefone'], $dt_nasc, $target_pid, $perfil_id);
        
        if ($stmt->execute()) {
            logAction($conn, 'REGISTRAR_USUARIO', 'usuarios', $conn->insert_id, ['novo' => $data]);
            header("Location: register.php?msg=Usuário cadastrado com sucesso!");
            exit();
        } else {
            $error = 'Erro ao cadastrar. Talvez este e-mail já esteja em uso.';
        }
    }
}

// Fetch Parishes for the dropdown
$parishes = $conn->query("SELECT id, nome FROM paroquias ORDER BY nome");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Cadastrar Usuário — PASCOM</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .app-shell { display: flex; min-height: 100vh; }
        .main-content { flex: 1; margin-left: var(--sidebar-w); padding: 3rem; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: margin 0.3s; }
        
        @media (max-width: 1024px) {
            .main-content { margin-left: 0; padding: 1.5rem; padding-top: 5rem; }
            .form-grid { grid-template-columns: 1fr; }
            .full-row { grid-column: span 1; }
            .form-container { padding: 2.5rem; }
        }
        
        .form-container { width: 100%; max-width: 700px; padding: 4rem; border-radius: 32px; }
        .form-header { margin-bottom: 3rem; text-align: center; }
        .form-header h1 { font-size: 2.2rem; font-weight: 900; margin-bottom: 0.5rem; letter-spacing: -0.03em; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .full-row { grid-column: span 2; }

        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
            .full-row { grid-column: span 1; }
            .main-content { padding: 1.5rem; }
            .form-container { padding: 2rem; }
        }
    </style>
</head>
<body>
    <div class="bg-mesh"></div>

    <div class="app-shell">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <?php if ($msg): ?> <?= alert('success', h($msg)) ?> <?php endif; ?>
            <?php if ($error): ?> <?= alert('error', h($error)) ?> <?php endif; ?>

            <section class="glass form-container animate-in">
                <header class="form-header">
                    <p style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.15em; color: var(--text-ghost); margin-bottom: 0.5rem;">CONTROLE DE ACESSO</p>
                    <h1 class="gradient-text">Novo Membro da Equipe</h1>
                    <p style="color: var(--text-dim); font-size: 0.95rem;">Configure as credenciais e o nível de acesso para o novo usuário.</p>
                </header>

                <form method="POST" class="form-grid">
                    <div class="form-group full-row">
                        <label>NOME COMPLETO</label>
                        <input type="text" name="nome" placeholder="Ex: João da Silva" required>
                    </div>

                    <div class="form-group">
                        <label>E-MAIL DE ACESSO</label>
                        <input type="email" name="email" placeholder="nome@paroquia.com" required>
                    </div>

                    <div class="form-group">
                        <label>TELEFONE / WHATSAPP</label>
                        <input type="text" name="telefone" placeholder="(00) 00000-0000">
                    </div>

                    <div class="form-group">
                        <label>DATA DE NASCIMENTO</label>
                        <input type="date" name="data_nascimento">
                    </div>

                    <div class="form-group">
                        <label>GÊNERO</label>
                        <select name="sexo">
                            <option value="M">Masculino</option>
                            <option value="F">Feminino</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>

                    <input type="hidden" name="perfil_id" value="9">

                    <div class="form-group full-row">
                        <label>PARÓQUIA DESIGNADA</label>
                        <select name="paroquia_id">
                            <?php while($p = $parishes->fetch_assoc()): ?>
                                <option value="<?= $p['id'] ?>" <?= $p['id'] == $pid ? 'selected' : '' ?>><?= h($p['nome']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group full-row" style="margin-bottom: 1rem;">
                        <label>SENHA TEMPORÁRIA</label>
                        <input type="password" name="senha" placeholder="••••••••" required>
                    </div>

                    <div class="full-row" style="display: flex; gap: 1rem; margin-top: 1rem;">
                        <button type="submit" class="btn btn-primary shimmer" style="flex: 2;">Finalizar Cadastro</button>
                        <a href="index.php" class="btn btn-ghost" style="flex: 1;">Cancelar</a>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
