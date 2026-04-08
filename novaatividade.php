<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Activity Creation (v2.0)
 * Modern Forms · Glassmorphism Design · Secure Logic
 * ═══════════════════════════════════════════════════════ */

require_once 'functions.php';
requirePerm('criar_eventos');

$pid = current_paroquia_id();
$error = '';
$data_pref = $_GET['data'] ?? date('Y-m-d');

// 1. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = sanitize_post($_POST);
    
    if (empty($data['nome']) || empty($data['data_inicio'])) {
        $error = 'Nome e data de início são obrigatórios.';
    } else {
        $sql = "INSERT INTO atividades (nome, paroquia_id, local_id, tipo_atividade_id, descricao, data_inicio, hora_inicio, criador_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $local = !empty($data['local_id']) ? (int)$data['local_id'] : null;
        $tipo = !empty($data['tipo_id']) ? (int)$data['tipo_id'] : null;
        $uid = $_SESSION['usuario_id'];
        
        $stmt->bind_param('siiisssi', $data['nome'], $pid, $local, $tipo, $data['descricao'], $data['data_inicio'], $data['hora_inicio'], $uid);
        
        if ($stmt->execute()) {
            logAction($conn, 'CRIAR_ATIVIDADE', 'atividades', $conn->insert_id, $data['nome']);
            header('Location: atividades.php?msg=Atividade criada com sucesso!');
            exit();
        } else {
            $error = 'Erro interno: ' . $conn->error;
        }
    }
}

// 2. Fetch Helper Data
$locais = $conn->query("SELECT id, nome_local FROM locais_paroquia WHERE paroquia_id = $pid ORDER BY nome_local");
$tipos  = $conn->query("SELECT id, nome_tipo FROM tipos_atividade WHERE paroquia_id = $pid ORDER BY nome_tipo");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Nova Atividade – PASCOM</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .app-shell { display: flex; min-height: 100vh; }
        .main-content { flex: 1; margin-left: var(--sidebar-w); padding: 3rem; display: flex; justify-content: center; align-items: flex-start; }
        
        .form-container { width: 100%; max-width: 650px; margin-top: 2rem; }
        .form-header { margin-bottom: 3rem; text-align: center; }
        .form-header h1 { font-size: 2.2rem; font-weight: 900; letter-spacing: -0.02em; }

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
                    <p style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.2em; color: var(--primary);">CONFIGURAÇÃO</p>
                    <h1 class="gradient-text">Novo Evento</h1>
                </header>

                <?php if ($error): ?>
                    <?= alert('error', h($error)) ?>
                <?php endif; ?>

                <form method="POST" class="glass glass-form">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Identificação do Evento</label>
                            <input type="text" name="nome" placeholder="Nome da atividade ou celebração" required autofocus>
                        </div>

                        <div class="row-grid">
                            <div class="form-group">
                                <label>Data de Início</label>
                                <input type="date" name="data_inicio" value="<?= h($data_pref) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Horário</label>
                                <input type="time" name="hora_inicio">
                            </div>
                        </div>

                        <div class="row-grid">
                            <div class="form-group">
                                <label>Localização</label>
                                <select name="local_id">
                                    <option value="">Selecione um local</option>
                                    <?php while ($l = $locais->fetch_assoc()): ?>
                                        <option value="<?= $l['id'] ?>"><?= h($l['nome_local']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Categoria</label>
                                <select name="tipo_id">
                                    <option value="">Selecione o tipo</option>
                                    <?php while ($t = $tipos->fetch_assoc()): ?>
                                        <option value="<?= $t['id'] ?>"><?= h($t['nome_tipo']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Notas Adicionais</label>
                            <textarea name="descricao" rows="4" placeholder="Descreva os detalhes ou objetivos deste evento..."></textarea>
                        </div>

                        <div style="display: flex; gap: 1.2rem; margin-top: 1rem;">
                            <button type="submit" class="btn btn-primary shimmer" style="flex: 2; height: 55px;">Criar Evento</button>
                            <a href="atividades.php" class="btn btn-ghost" style="flex: 1; height: 55px; line-height: 55px; text-align: center;">Voltar</a>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
