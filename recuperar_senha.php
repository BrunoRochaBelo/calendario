<?php
require_once 'functions.php';

$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string)($_POST['email'] ?? ''));
    $palavraChave = trim((string)($_POST['palavra_chave'] ?? ''));

    if ($email === '' || $palavraChave === '') {
        $error = 'Preencha e-mail e palavra-chave para continuar.';
    } else {
        $throttleKey = authThrottleKey('recovery', $email);
        $throttle = authThrottleState($conn, 'recovery', $throttleKey, 3);

        if (!$throttle['allowed']) {
            $mins = max(1, (int)ceil(((int)($throttle['seconds_left'] ?? 0)) / 60));
            $error = "Muitas tentativas. Tente novamente em {$mins} minuto(s).";
        } else {
            $stmt = $conn->prepare('SELECT id, palavra_chave FROM usuarios WHERE email = ? AND ativo = 1 LIMIT 1');
            $user = null;

            if ($stmt) {
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();
            }

            $storedKey = trim((string)($user['palavra_chave'] ?? ''));
            $keyMatches = $storedKey !== '' && hash_equals($storedKey, $palavraChave);

            if ($user && $keyMatches) {
                authThrottleReset($conn, 'recovery', $throttleKey);
                session_regenerate_id(true);
                $_SESSION['reset_user_id'] = (int)$user['id'];
                header('Location: nova_senha.php');
                exit();
            }

            $throttle = authThrottleRegisterFailure($conn, 'recovery', $throttleKey, 3, 5);
            if (!$throttle['allowed']) {
                $error = 'Muitas tentativas. Tente novamente em 5 minutos.';
            } else {
                $remaining = (int)($throttle['remaining'] ?? 0);
                $error = 'Nao foi possivel validar os dados informados.' . ($remaining > 0 ? " Restam {$remaining} tentativa(s)." : '');
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Recuperar Acesso - PASCOM</title>
    <link rel="stylesheet" href="style.css?v=2.4.5"
        <link rel="stylesheet" href="css/responsive.css?v=2.4.5">
    <style>
        body {
            background: #000;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .recovery-card {
            width: 100%;
            max-width: 480px;
            padding: 3.5rem;
            border-radius: 32px;
            position: relative;
        }
        .brand-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: var(--panel-hi);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2.5rem;
            color: var(--primary);
        }
        .field-stack {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .field-wrap {
            position: relative;
        }
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
        .note-limit {
            margin-top: 0.6rem;
            color: var(--text-ghost);
            font-size: 0.78rem;
            line-height: 1.4;
        }
        @media (max-width: 600px) {
            .recovery-card {
                padding: 3rem 2rem;
            }
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
            <p style="color: var(--text-ghost); font-size: 0.9rem;">Informe seu e-mail e a palavra-chave fornecida pelo master.</p>
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
                    <input type="email" name="email" placeholder="seu@email.com" required autocomplete="username">
                </div>

                <div class="form-group">
                    <label>PALAVRA-CHAVE</label>
                    <div class="field-wrap">
                        <input type="password" name="palavra_chave" id="recoveryKey" placeholder="Sua palavra de segurança" required autocomplete="current-password">
                        <button type="button" class="toggle-pass" data-target="recoveryKey">Mostrar</button>
                    </div>
                    <div class="note-limit">Se a palavra-chave estiver vazia no cadastro, o reset nao sera permitido.</div>
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
