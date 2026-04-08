<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Activity Edition (v2.0)
 * Glassmorphism Design · Real-time Feedback · CRUD
 * ═══════════════════════════════════════════════════════ */

require_once 'functions.php';
requirePerm('editar_eventos');

$pid = current_paroquia_id();
$id = (int)($_GET['id'] ?? 0);
$error = '';
$success = '';

// 1. Fetch Current Data
$stmt = $conn->prepare("SELECT * FROM atividades WHERE id = ? AND paroquia_id = ? LIMIT 1");
$stmt->bind_param('ii', $id, $pid);
$stmt->execute();
$activity = $stmt->get_result()->fetch_assoc();

if (!$activity) {
    header('Location: atividades.php?error=not_found');
    exit();
}

// 2. Handle Update Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = sanitize_post($_POST);
    
    if (empty($data['nome']) || empty($data['data_inicio'])) {
        $error = 'Nome e data de início são obrigatórios.';
    } else {
        $sql = "UPDATE atividades SET 
                nome = ?, local_id = ?, tipo_atividade_id = ?, 
                descricao = ?, data_inicio = ?, hora_inicio = ? 
                WHERE id = ? AND paroquia_id = ?";
        
        $stmt = $conn->prepare($sql);
        $local = !empty($data['local_id']) ? (int)$data['local_id'] : null;
        $tipo = !empty($data['tipo_id']) ? (int)$data['tipo_id'] : null;
        
        $stmt->bind_param('siisssii', 
            $data['nome'], $local, $tipo, 
            $data['descricao'], $data['data_inicio'], $data['hora_inicio'], 
            $id, $pid
        );
        
        if ($stmt->execute()) {
            logAction($conn, 'EDITAR_ATIVIDADE', 'atividades', $id, $data['nome']);
            header('Location: atividades.php?msg=Alterações salvas com sucesso!');
            exit();
        } else {
            $error = 'Erro ao atualizar: ' . $conn->error;
        }
    }
}

// 3. Fetch Helper Data
$locais = $conn->query("SELECT id, nome_local FROM locais_paroquia WHERE paroquia_id = $pid ORDER BY nome_local");
$tipos  = $conn->query("SELECT id, nome_tipo FROM tipos_atividade WHERE paroquia_id = $pid ORDER BY nome_tipo");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Editar Atividade – PASCOM</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .app-shell { display: flex; min-height: 100vh; }
        .main-content { flex: 1; margin-left: var(--sidebar-w); padding: 3rem; display: flex; justify-content: center; align-items: flex-start; }
        
        .form-container { width: 100%; max-width: 650px; margin-top: 2rem; }
        .form-header { margin-bottom: 3rem; text-align: center; }
        .form-header h1 { font-size: 2.2rem; font-weight: 900; }

        .glass-form { padding: 3.5rem; border-radius: var(--r-lg); }
        .form-grid { display: grid; gap: 1.8rem; }
        
        .form-group { display: flex; flex-direction: column; gap: 0.7rem; }
        .form-group label { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: var(--text-ghost); letter-spacing: 0.1em; }
        
        .row-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }

        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 1.5rem; }
            .glass-form { padding: 2rem; }
            .row-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="bg-mesh"></div>

    <div class="app-shell">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="form-container animate-in">
                <header class="form-header">
                    <p style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.2em; color: var(--primary);">MODIFICAÇÃO</p>
                    <h1 class="gradient-text">Editar Atividade</h1>
                </header>

                <?php if ($error): ?>
                    <?= alert('error', h($error)) ?>
                <?php endif; ?>

                <form method="POST" class="glass glass-form">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Identificação do Evento</label>
                            <input type="text" name="nome" value="<?= h($activity['nome']) ?>" required autofocus>
                        </div>

                        <div class="row-grid">
                            <div class="form-group">
                                <label>Data de Início</label>
                                <input type="date" name="data_inicio" value="<?= h($activity['data_inicio']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Horário</label>
                                <input type="time" name="hora_inicio" value="<?= formatTime($activity['hora_inicio']) ?>">
                            </div>
                        </div>

                        <div class="row-grid">
                            <div class="form-group">
                                <label>Localização</label>
                                <select name="local_id">
                                    <option value="">Selecione um local</option>
                                    <?php while ($l = $locais->fetch_assoc()): ?>
                                        <option value="<?= $l['id'] ?>" <?= $l['id'] == $activity['local_id'] ? 'selected' : '' ?>>
                                            <?= h($l['nome_local']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Categoria</label>
                                <select name="tipo_id">
                                    <option value="">Selecione o tipo</option>
                                    <?php while ($t = $tipos->fetch_assoc()): ?>
                                        <option value="<?= $t['id'] ?>" <?= $t['id'] == $activity['tipo_id'] ? 'selected' : '' ?>>
                                            <?= h($t['nome_tipo']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Notas Adicionais</label>
                            <textarea name="descricao" rows="4"><?= h($activity['descricao']) ?></textarea>
                        </div>

                        <div style="display: flex; gap: 1.2rem; margin-top: 1rem;">
                            <button type="submit" class="btn btn-primary shimmer" style="flex: 2; height: 55px;">Salvar Alterações</button>
                            <a href="excluir_atividade.php?id=<?= $id ?>" class="btn btn-ghost" style="flex: 1; height: 55px; line-height: 55px; text-align: center; color: #ef4444;" onclick="return confirm('Tem certeza que deseja excluir permanentemente este evento?')">Excluir</a>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
