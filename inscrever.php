<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Event Registration (v2.0)
 * Public Entry · Data Collection · Premium UI
 * ═══════════════════════════════════════════════════════ */

require_once 'functions.php';

$aid = (int)($_GET['id'] ?? 0);
$msg = '';
$error = '';

// 1. Fetch Activity Details
$stmt = $conn->prepare("
    SELECT a.*, p.nome as paroquia_nome, t.nome as tipo_nome 
    FROM atividades a
    JOIN paroquias p ON a.paroquia_id = p.id
    JOIN tipos_atividade t ON a.tipo_id = t.id
    WHERE a.id = ? AND a.ativo = 1
");
$stmt->bind_param('i', $aid);
$stmt->execute();
$atividade = $stmt->get_result()->fetch_assoc();

if (!$atividade) {
    header('Location: index.php');
    exit();
}

// 2. Handle Registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    
    if ($nome && $email) {
        $stmt = $conn->prepare("INSERT INTO inscricoes (atividade_id, nome, email, telefone, data_inscricao) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param('isss', $aid, $nome, $email, $telefone);
        
        if ($stmt->execute()) {
            $msg = "Sua inscrição em '{$atividade['nome']}' foi realizada com sucesso!";
        } else {
            $error = "Você já está inscrito nesta atividade.";
        }
    } else {
        $error = "Por favor, preencha seu nome e e-mail.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Inscrição — <?= h($atividade['nome']) ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #000; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .registration-card { width: 100%; max-width: 600px; padding: 4rem; border-radius: 32px; position: relative; }
        
        .event-info { margin-bottom: 3rem; padding-bottom: 2rem; border-bottom: 1px solid var(--border); }
        .event-tag { display: inline-block; padding: 0.4rem 0.8rem; background: var(--panel-hi); border-radius: 8px; font-size: 0.7rem; font-weight: 800; color: var(--primary); text-transform: uppercase; margin-bottom: 1rem; }
        
        .success-box { text-align: center; }
        .success-icon { width: 72px; height: 72px; background: rgba(16, 185, 129, 0.1); color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; }
    </style>
</head>
<body>
    <div class="bg-mesh"></div>

    <main class="glass registration-card animate-in">
        <?php if ($msg): ?>
            <div class="success-box animate-in">
                <div class="success-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <h1 style="font-size: 1.8rem; font-weight: 900; margin-bottom: 1rem;">Confirmado!</h1>
                <p style="color: var(--text-ghost); margin-bottom: 2.5rem;"><?= h($msg) ?></p>
                <a href="index.php" class="btn btn-primary shimmer" style="width: 100%;">Voltar ao Início</a>
            </div>
        <?php else: ?>
            <div class="event-info">
                <span class="event-tag"><?= h($atividade['tipo_nome']) ?></span>
                <h1 style="font-size: 2rem; font-weight: 900; margin-bottom: 0.8rem; line-height: 1.1;"><?= h($atividade['nome']) ?></h1>
                <p style="color: var(--text-dim); font-size: 0.95rem;"><?= h($atividade['paroquia_nome']) ?> &bull; <?= formatDate($atividade['data_inicio']) ?></p>
            </div>

            <?php if ($error): ?> <?= alert('error', h($error)) ?> <?php endif; ?>

            <form method="POST" style="display: grid; gap: 0.5rem;">
                <div class="form-group">
                    <label>NOME COMPLETO</label>
                    <input type="text" name="nome" placeholder="Digite seu nome" required autofocus>
                </div>

                <div class="form-group">
                    <label>E-MAIL</label>
                    <input type="email" name="email" placeholder="seu@email.com" required>
                </div>

                <div class="form-group">
                    <label>TELEFONE (OPCIONAL)</label>
                    <input type="text" name="telefone" placeholder="(00) 00000-0000">
                </div>

                <button type="submit" class="btn btn-primary shimmer" style="width: 100%; padding: 1.25rem; font-size: 1rem; border-radius: 18px; margin-top: 1.5rem;">
                    Confirmar Minha Inscrição
                </button>
            </form>

            <div style="margin-top: 2rem; text-align: center;">
                <a href="index.php" style="color: var(--text-ghost); text-decoration: none; font-size: 0.8rem; font-weight: 800;">← CANCELAR E VOLTAR</a>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>