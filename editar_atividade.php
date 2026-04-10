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
ensureEventActivitiesStructure($conn);
seedDefaultEventActivities($conn, $pid);

// 1. Fetch Current Data
$stmt = $conn->prepare("SELECT * FROM atividades WHERE id = ? AND paroquia_id = ? LIMIT 1");
$stmt->bind_param('ii', $id, $pid);
$stmt->execute();
$activity = $stmt->get_result()->fetch_assoc();

if (!$activity) {
    header('Location: atividades.php?error=not_found');
    exit();
}

if ($activity['restrito']) {
    $userId = (int)($_SESSION['usuario_id'] ?? 0);
    if (!can('ver_restritos') && $activity['criador_id'] != $userId) {
        header('Location: atividades.php?error=unauthorized_restricted');
        exit();
    }
}

$existingEventActivities = array_map(
    static fn(array $item): int => (int)$item['atividade_catalogo_id'],
    getEventActivityItems($conn, $id, (int)($_SESSION['usuario_id'] ?? 0))
);
$selectedActivities = normalizeEventActivityCatalogIds($_POST['atividades_evento'] ?? $existingEventActivities);

// 2. Handle Update Submission

// 2. Handle Update Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = sanitize_post($_POST);
    
    if (empty($data['nome']) || empty($data['data_inicio'])) {
        $error = 'Nome e data de início são obrigatórios.';
    } else {
        $sql = "UPDATE atividades SET 
                nome = ?, local_id = ?, tipo_atividade_id = ?, 
                descricao = ?, data_inicio = ?, hora_inicio = ?, 
                restrito = ?, cor = ?
                WHERE id = ? AND paroquia_id = ?";
        
        $stmt = $conn->prepare($sql);
        $local = !empty($data['local_id']) ? (int)$data['local_id'] : null;
        $tipo = !empty($data['tipo_id']) ? (int)$data['tipo_id'] : null;
        $cor = trim($data['cor'] ?? '#3b82f6');
        $restrito = isset($data['restrito']) ? 1 : 0;
        
        $stmt->bind_param('siisssisii', 
            $data['nome'], $local, $tipo, 
            $data['descricao'], $data['data_inicio'], $data['hora_inicio'], 
            $restrito, $cor, $id, $pid
        );
        
        if ($stmt->execute()) {
            saveEventActivityItems($conn, $id, $pid, $data['atividades_evento'] ?? []);
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
$catalogoAtividades = getEventActivityCatalog($conn, $pid);
$activityOptionMap = [];
foreach ($catalogoAtividades as $catalogoItem) {
    $activityOptionMap[] = [
        'id' => (int)$catalogoItem['id'],
        'nome' => $catalogoItem['nome'],
    ];
}
if (!$selectedActivities) {
    $selectedActivities = [0];
}
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
        .event-activities-box { display: grid; gap: 0.9rem; }
        .event-activity-row { display: flex; gap: 0.75rem; align-items: center; }
        .event-activity-row select { flex: 1; color: #111827 !important; background: #ffffff !important; }
        .event-activity-remove { min-width: 48px; height: 48px; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .event-activities-help { font-size: 0.82rem; color: var(--text-dim); line-height: 1.5; }
        select[name="local_id"],
        select[name="tipo_id"] {
            color: #111827 !important;
            background: #ffffff !important;
        }
        select[name="local_id"] option,
        select[name="tipo_id"] option {
            color: #111827 !important;
            background: #ffffff !important;
        }

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
                        <div class="row-grid" style="grid-template-columns: 1fr auto;">
                            <div class="form-group">
                                <label>Identificação do Evento</label>
                                <input type="text" name="nome" value="<?= h($activity['nome']) ?>" required autofocus>
                            </div>
                            <div class="form-group">
                                <label>Cor do Evento</label>
                                <input type="color" name="cor" value="<?= h($activity['cor'] ?? '#3b82f6') ?>" style="height: 55px; width: 60px; padding: 0.2rem; cursor: pointer; border-radius: 12px; border: 1px solid var(--border); background: var(--panel-hi);">
                            </div>
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
                                        <option value="<?= $t['id'] ?>" <?= $t['id'] == $activity['tipo_atividade_id'] ? 'selected' : '' ?>>
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

                        <?php if (can('ver_restritos')): ?>
                        <div class="form-group" style="flex-direction: row; align-items: center; gap: 0.8rem; background: rgba(239, 68, 68, 0.05); padding: 1rem; border-radius: 12px; border: 1px solid rgba(239, 68, 68, 0.1);">
                            <input type="checkbox" name="restrito" id="restrito" style="width: 20px; height: 20px; cursor: pointer;" <?= $activity['restrito'] ? 'checked' : '' ?>>
                            <label for="restrito" style="margin: 0; cursor: pointer; color: #ef4444;">Evento Restrito (Privado)</label>
                        </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label>Atividades do Evento</label>
                            <div id="eventActivitiesBox" class="event-activities-box"></div>
                            <button type="button" id="addEventActivity" class="btn btn-ghost" style="width: fit-content;">+ Adicionar atividade</button>
                            <div class="event-activities-help">
                                Clique em “+” para adicionar outra atividade ao mesmo evento. Na edição você também pode trocar ou remover as já vinculadas.
                            </div>
                        </div>

                        <div style="display: flex; gap: 1.2rem; margin-top: 1rem;">
                            <button type="submit" class="btn btn-primary shimmer" style="flex: 2; height: 55px;">Salvar Alterações</button>
                            <a href="excluir_atividade.php?id=<?= $id ?>" class="btn btn-ghost" style="flex: 1; height: 55px; line-height: 55px; text-align: center; color: #ef4444;" onclick="return confirmLink(this, 'Tem certeza que deseja excluir permanentemente este evento?')">Excluir</a>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <script>
        (() => {
            const box = document.getElementById('eventActivitiesBox');
            const addButton = document.getElementById('addEventActivity');
            const options = <?= json_encode($activityOptionMap, JSON_UNESCAPED_UNICODE) ?>;
            const initialValues = <?= json_encode($selectedActivities) ?>;

            function buildOptions(selectedValue) {
                const base = ['<option value="">Selecione uma atividade</option>'];
                options.forEach((item) => {
                    const selected = Number(selectedValue) === Number(item.id) ? ' selected' : '';
                    base.push(`<option value="${item.id}"${selected}>${item.nome}</option>`);
                });
                return base.join('');
            }

            function addRow(selectedValue = '') {
                const row = document.createElement('div');
                row.className = 'event-activity-row';
                row.innerHTML = `
                    <select name="atividades_evento[]">${buildOptions(selectedValue)}</select>
                    <button type="button" class="btn btn-ghost event-activity-remove" title="Remover atividade">×</button>
                `;
                row.querySelector('.event-activity-remove').addEventListener('click', () => {
                    row.remove();
                    if (!box.children.length) {
                        addRow('');
                    }
                });
                box.appendChild(row);
            }

            addButton.addEventListener('click', () => addRow(''));

            if (initialValues.length) {
                initialValues.forEach((value) => addRow(value));
            } else {
                addRow('');
            }
        })();
    </script>
</body>
</html>
