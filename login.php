<?php
/**
 * ═════════════════════════════════════════════
 * PASCOM — Security Gate / Login (v2.0)
 * Premium Entry · Session Sync · Secure Auth
 * ═════════════════════════════════════════════ */

require_once 'functions.php';

// Redirect if already logged in
if (is_authenticated()) {
    header('Location: dashboard.php');
    exit();
}

$msg = $_GET['msg'] ?? '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    
    if ($email && $senha) {
        $stmt = $conn->prepare('SELECT id, nome, senha, paroquia_id, ativo FROM usuarios WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $u = $stmt->get_result()->fetch_assoc();
        
        if ($u && password_verify($senha, $u['senha'])) {
            if ($u['ativo'] != 1) {
                $error = 'Sua conta está temporariamente inativa.';
            } else {
                $_SESSION['usuario_id'] = (int)$u['id'];
                $_SESSION['usuario_nome'] = $u['nome'];
                $_SESSION['paroquia_id'] = (int)$u['paroquia_id'];
                
                $_SESSION['perms'] = loadPermissions($conn, $u['id']);
                logAction($conn, 'LOGIN', 'usuarios', $u['id'], 'Autenticação bem-sucedida');
                
                header('Location: dashboard.php');
                exit();
            }
        } else {
            $error = 'Credenciais inválidas. Verifique seus dados.';
        }
    } else {
        $error = 'Por favor, preencha todos os campos obrigatórios.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Acessar Portal — PASCOM</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #000; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; }
        
        .login-gate { width: 100%; max-width: 1000px; display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center; }
        
        .hero-side { padding: 2rem; position: relative; }
        .hero-side h2 { font-size: 3.5rem; font-weight: 900; line-height: 1; letter-spacing: -0.04em; margin-bottom: 2rem; }
        .hero-side p { font-size: 1.1rem; color: var(--text-dim); line-height: 1.6; max-width: 400px; }

        .auth-card { padding: 4rem; border-radius: 32px; position: relative; overflow: hidden; }
        .auth-card::before { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.05), transparent); pointer-events: none; }

        .brand-icon { width: 64px; height: 64px; border-radius: 20px; background: linear-gradient(135deg, var(--primary), var(--accent)); display: flex; align-items: center; justify-content: center; margin-bottom: 3rem; box-shadow: 0 20px 40px rgba(var(--primary-rgb), 0.3); }

        .field label { font-size: 0.7rem; font-weight: 800; letter-spacing: 0.1em; color: var(--text-ghost); margin-bottom: 0.8rem; display: block; }
        .field input { padding: 1.2rem; background: rgba(255,255,255,0.03); border: 1px solid var(--border); border-radius: 16px; font-size: 1rem; color: #fff; transition: all 0.3s; width: 100%; }
        .field input:focus { background: rgba(255,255,255,0.06); border-color: var(--primary); box-shadow: 0 0 20px rgba(var(--primary-rgb), 0.2); }

        @media (max-width: 900px) {
            .login-gate { grid-template-columns: 1fr; gap: 2rem; }
            .hero-side { display: none; }
            .auth-card { padding: 3rem 2rem; }
        }
    </style>
</head>
<body>
    <div class="bg-mesh"></div>

    <div class="login-gate">
        <section class="hero-side animate-in">
            <div class="brand-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <h2 class="gradient-text">Conectando<br>Sua Comunidade.</h2>
            <p>O ecossistema definitivo para a gestão e articulação paroquial. Simples, potente e focado na missão.</p>
        </section>

        <main class="auth-card glass animate-in" style="animation-delay: 0.1s;">
            <div style="margin-bottom: 3rem;">
                <h1 style="font-size: 2rem; font-weight: 900; margin-bottom: 0.5rem; letter-spacing: -0.02em;">Acessar Portal</h1>
                <p style="color: var(--text-ghost); font-size: 0.95rem;">Bem-vindo de volta! Identifique-se.</p>
            </div>

            <?php if ($error): ?>
                <div style="padding: 1rem 1.5rem; background: rgba(239, 68, 68, 0.1); border-radius: 12px; border-left: 4px solid #ef4444; color: #ef4444; font-size: 0.85rem; font-weight: 700; margin-bottom: 2rem;">
                    <?= h($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" style="display: grid; gap: 1.8rem;">
                <div class="field">
                    <label>E-MAIL INSTITUCIONAL</label>
                    <input type="email" name="email" placeholder="nome@exemplo.com" required autofocus>
                </div>

                <div class="field">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem;">
                        <label style="margin: 0;">SENHA DE ACESSO</label>
                        <a href="recuperar_senha.php" style="font-size: 0.7rem; font-weight: 800; color: var(--primary); text-decoration: none;">ESQUECI MEU ACESSO</a>
                    </div>
                    <input type="password" name="senha" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-primary shimmer" style="width: 100%; padding: 1.2rem; font-size: 1rem; border-radius: 18px;">
                    Entrar no Sistema
                </button>
            </form>

            <div style="margin-top: 3rem; text-align: center; border-top: 1px solid var(--border); padding-top: 2rem;">
                <a href="index.php" style="color: var(--text-ghost); text-decoration: none; font-size: 0.8rem; font-weight: 800; letter-spacing: 0.05em;">← VOLTAR PARA O SITE</a>
            </div>
        </main>
    </div>
</body>
</html>