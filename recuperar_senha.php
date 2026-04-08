<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Account Recovery (v2.0)
 * Data Validation · Security Check · Premium UI
 * ═══════════════════════════════════════════════════════ */

require_once 'functions.php';

$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $palavra_chave = trim($_POST['palavra_chave'] ?? '');

    if ($email && $palavra_chave) {
        $stmt = $conn->prepare('SELECT id FROM usuarios WHERE email = ? AND palavra_chave = ? AND ativo = 1 LIMIT 1');
        $stmt->bind_param('ss', $email, $palavra_chave);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($u = $res->fetch_assoc()) {
            $_SESSION['reset_user_id'] = $u['id'];
            header('Location: nova_senha.php');
            exit();
        } else {
            $error = 'Os dados informados não conferem com nossos registros.';
        }
    } else {
        $error = 'Por favor, preencha todos os campos para validação.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Recuperar Acesso — PASCOM</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #000; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .recovery-card { width: 100%; max-width: 480px; padding: 3.5rem; border-radius: 32px; position: relative; }
        
        .brand-icon { width: 56px; height: 56px; border-radius: 16px; background: var(--panel-hi); display: flex; align-items: center; justify-content: center; margin: 0 auto 2.5rem; color: var(--primary); }
        
        .field-stack { display: flex; flex-direction: column; gap: 1.5rem; margin-bottom: 2.5rem; }

        @media (max-width: 600px) {
            .recovery-card { padding: 3rem 2rem; }
        }
    </style>
</head>
<body>
    <div class="bg-mesh"></div>

    <main class="glass recovery-card animate-in">
        <div class="brand-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>

        <div style="text-align: center; margin-bottom: 3rem;">
            <h1 style="font-size: 1.8rem; font-weight: 900; margin-bottom: 0.5rem; letter-spacing: -0.02em;">Recuperar Acesso</h1>
            <p style="color: var(--text-ghost); font-size: 0.9rem;">Informe seu e-mail e sua palavra-chave de segurança.</p>
        </div>

        <?php if ($error): ?>
            <div style="padding: 1rem; background: rgba(239, 68, 68, 0.1); border-radius: 12px; color: #ef4444; font-size: 0.85rem; font-weight: 700; margin-bottom: 2rem; text-align: center;">
                <?= h($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="field-stack">
                <div class="form-group">
                    <label>E-MAIL CADASTRADO</label>
                    <input type="email" name="email" placeholder="seu@email.com" required>
                </div>

                <div class="form-group">
                    <label>PALAVRA-CHAVE</label>
                    <input type="password" name="palavra_chave" placeholder="Sua palavra de segurança" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary shimmer" style="width: 100%; padding: 1.2rem; font-size: 1rem; border-radius: 18px;">
                Validar Identidade
            </button>
        </form>

        <div style="margin-top: 2.5rem; text-align: center; border-top: 1px solid var(--border); padding-top: 2rem;">
            <a href="login.php" style="color: var(--text-ghost); text-decoration: none; font-size: 0.8rem; font-weight: 800;">← VOLTAR PARA O LOGIN</a>
        </div>
    </main>
</body>
</html>

</html>