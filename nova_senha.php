<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Security / New Password (v2.0)
 * Credential Reset · Secure Hashing · Premium UI
 * ═══════════════════════════════════════════════════════ */

require_once 'functions.php';

// Safety Check: User must have passed the recovery gate
if (empty($_SESSION['reset_user_id'])) {
    header('Location: recuperar_senha.php');
    exit();
}

$msg = '';
$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senha = $_POST['senha'] ?? '';
    $conf = $_POST['confirmar'] ?? '';
    
    if (strlen($senha) < 6) {
        $error = 'A senha precisa ter no mínimo 6 caracteres.';
    } elseif ($senha !== $conf) {
        $error = 'As senhas não coincidem.';
    } else {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');
        $stmt->bind_param('si', $hash, $_SESSION['reset_user_id']);
        
        if ($stmt->execute()) {
            logAction($conn, 'RESET_SENHA', 'usuarios', $_SESSION['reset_user_id'], 'Senha redefinida via fluxo de recuperação');
            unset($_SESSION['reset_user_id']);
            $msg = 'Senha atualizada com sucesso!';
            $success = true;
        } else {
            $error = 'Erro crítico ao atualizar senha. Tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Nova Senha — PASCOM</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #000; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .reset-card { width: 100%; max-width: 450px; padding: 4rem; border-radius: 32px; text-align: center; }
        
        .success-icon { width: 80px; height: 80px; border-radius: 50%; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; }
    </style>
</head>
<body>
    <div class="bg-mesh"></div>

    <main class="glass reset-card animate-in">
        <?php if ($success): ?>
            <div class="success-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <h1 style="font-size: 1.8rem; font-weight: 900; margin-bottom: 1rem;">Tudo Pronto!</h1>
            <p style="color: var(--text-ghost); margin-bottom: 2.5rem;">Sua senha foi redefinida com segurança. Você já pode acessar o portal.</p>
            <a href="login.php" class="btn btn-primary shimmer" style="width: 100%; display: block; padding: 1.2rem; border-radius: 18px; text-decoration: none;">Ir para o Login</a>
        <?php else: ?>
            <div style="margin-bottom: 3rem;">
                <h1 style="font-size: 1.8rem; font-weight: 900; margin-bottom: 0.5rem; letter-spacing: -0.02em;">Nova Senha</h1>
                <p style="color: var(--text-ghost); font-size: 0.9rem;">Defina sua nova credencial de acesso.</p>
            </div>

            <?php if ($error): ?>
                <div style="padding: 1rem; background: rgba(239, 68, 68, 0.1); border-radius: 12px; color: #ef4444; font-size: 0.85rem; font-weight: 700; margin-bottom: 2rem;">
                    <?= h($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" style="display: grid; gap: 1.5rem; text-align: left;">
                <div class="form-group">
                    <label>NOVA SENHA</label>
                    <input type="password" name="senha" placeholder="Mínimo 6 caracteres" required autofocus>
                </div>

                <div class="form-group">
                    <label>CONFIRMAR SENHA</label>
                    <input type="password" name="confirmar" placeholder="Repita a senha" required>
                </div>

                <button type="submit" class="btn btn-primary shimmer" style="width: 100%; padding: 1.2rem; font-size: 1rem; border-radius: 18px; margin-top: 1rem;">
                    Atualizar Senha
                </button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>
