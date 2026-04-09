<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Parish Management (v2.0)
 * Context Control · Logo PNG Upload · CRUD Paróquias
 * ═══════════════════════════════════════════════════════ */

require_once 'functions.php';
requireLogin();

if ($_SESSION['usuario_id'] != 1) {
    header('Location: index.php?error=unauthorized');
    exit();
}

$msg = '';
$error = '';

/** Handle Delete */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Check if there are users using this parish 
    // Usually handled by FK or we set null, but let's delete
    $oldResult = $conn->query("SELECT * FROM paroquias WHERE id = $id");
    if ($oldResult && $oldResult->num_rows > 0) {
        $oldState = $oldResult->fetch_assoc();
        $conn->query("DELETE FROM paroquias WHERE id = $id");
        
        $img_path = __DIR__ . '/img/paroquia_' . $id . '.png';
        if (file_exists($img_path)) unlink($img_path);
        
        logAction($conn, 'EXCLUIR_PAROQUIA', 'paroquias', $id, ['antigo' => $oldState]);
        header('Location: paroquias.php?msg=Contexto removido');
        exit();
    }
}

/** Handle Form Submission */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    
    if ($nome) {
        if ($id > 0) {
            // Update
            $oldRes = $conn->query("SELECT * FROM paroquias WHERE id = $id");
            $oldData = $oldRes->fetch_assoc();
            
            $stmt = $conn->prepare('UPDATE paroquias SET nome = ? WHERE id = ?');
            $stmt->bind_param('si', $nome, $id);
            if ($stmt->execute()) {
                logAction($conn, 'EDITAR_PAROQUIA', 'paroquias', $id, ['antigo' => $oldData, 'novo' => ['nome' => $nome]]);
                $msg = 'Contexto salvo.';
            } else {
                $error = 'Erro ao salvar contexto.';
            }
            $target_id = $id;
        } else {
            // Insert
            $stmt = $conn->prepare('INSERT INTO paroquias (nome) VALUES (?)');
            $stmt->bind_param('s', $nome);
            if ($stmt->execute()) {
                $target_id = $conn->insert_id;
                logAction($conn, 'CRIAR_PAROQUIA', 'paroquias', $target_id, ['novo' => $nome]);
                
                // Set to self context if master uses it
                if (empty($_SESSION['paroquia_id'])) $_SESSION['paroquia_id'] = $target_id;
                
                $msg = 'Contexto registrado.';
            } else {
                $error = 'Erro ao cadastrar.';
            }
        }
        
        // Handle Icon Upload
        if (!empty($target_id) && isset($_FILES['icone']) && $_FILES['icone']['error'] === UPLOAD_ERR_OK) {
            $tmpPath = $_FILES['icone']['tmp_name'];
            $mime = mime_content_type($tmpPath);
            
            if ($mime === 'image/png') {
                $destPath = __DIR__ . "/img/paroquia_{$target_id}.png";
                if (!is_dir(__DIR__ . '/img')) mkdir(__DIR__ . '/img', 0755, true);
                
                move_uploaded_file($tmpPath, $destPath);
                $msg .= ' Logo atualizada.';
            } else {
                $error = 'Formato inválido. Envie apenas arquivos .png.';
            }
        }
        
    } else {
        $error = 'O nome da paróquia/sede é obrigatório.';
    }
}

$res = $conn->query('SELECT p.*, (SELECT COUNT(id) FROM usuarios u WHERE u.paroquia_id = p.id) as totais FROM paroquias p ORDER BY p.nome');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Sedes e Contextos — PASCOM</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .app-shell { display: flex; min-height: 100vh; }
        .main-content { flex: 1; margin-left: var(--sidebar-w); padding: 3rem; transition: margin 0.3s; }
        .header-flex { display: flex; justify-content: space-between; align-items: flex-end; gap: 1.5rem; margin-bottom: 3rem; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; }

        .pq-card {
            padding: 1.5rem; border-radius: 24px; position: relative;
            overflow: hidden; display: flex; align-items: center; gap: 1.5rem;
        }
        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 1.5rem; padding-top: 5rem; }
            .header-flex { flex-direction: column; align-items: flex-start; }
            .grid { grid-template-columns: 1fr; }
            .pq-card { flex-direction: column; align-items: flex-start; text-align: left; }
            .pq-card > div:last-child { width: 100%; flex-direction: row; justify-content: space-between; }
        }
        /* The previous diff added these media queries, so they should be removed */
        /*
        @media (max-width: 991px) {
            .header-flex { flex-direction: column; align-items: flex-start; }
        }
        @media (max-width: 576px) {
            .main-content { padding: 1rem; }
            .header-flex { flex-direction: column; align-items: flex-start; }
            .grid { grid-template-columns: 1fr; }
            .pq-card { flex-direction: column; align-items: flex-start; text-align: left; }
            .pq-card > div:last-child { width: 100%; flex-direction: row; justify-content: space-between; }
        }
    </style>
</head>
<body>
    <div class="bg-mesh"></div>
    
    <div class="app-shell">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <header class="header-flex animate-in">
                <div>
                    <p style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.15em; color: var(--text-ghost);">MASTER CONTROL</p>
                    <h1 class="gradient-text">Sedes Paroquiais</h1>
                </div>
                <button onclick="openModal()" class="btn btn-primary shimmer">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                    Novo Contexto
                </button>
            </header>

            <?php if ($msg): ?> <?= alert('success', h($msg)) ?> <?php endif; ?>
            <?php if ($error): ?> <?= alert('danger', h($error)) ?> <?php endif; ?>
            <?php if (isset($_GET['msg'])): ?> <?= alert('success', h($_GET['msg'])) ?> <?php endif; ?>

            <div class="grid animate-in" style="animation-delay: 0.1s;">
                <?php while ($row = $res->fetch_assoc()): ?>
                <div class="glass pq-card">
                    <?php 
                    $icon = "img/paroquia_{$row['id']}.png";
                    if (file_exists(__DIR__ . '/' . $icon)): ?>
                        <div style="width: 70px; height: 70px; border-radius: 18px; border: 1px solid var(--border); overflow: hidden; flex-shrink: 0; background: #000;">
                            <img src="<?= $icon ?>?v=<?= time() ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    <?php else: ?>
                        <div style="width: 70px; height: 70px; border-radius: 18px; border: 1px dashed var(--border); display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.02); color: var(--text-ghost); flex-shrink: 0;">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </div>
                    <?php endif; ?>

                    <div style="flex: 1;">
                        <span style="font-size: 0.7rem; font-weight: 800; letter-spacing: 0.1em; color: var(--primary);">ID #<?= $row['id'] ?></span>
                        <h2 style="font-size: 1.3rem; margin: 0.2rem 0;"><?= h($row['nome']) ?></h2>
                        <div style="font-size: 0.8rem; color: var(--text-dim); display: flex; gap: 0.5rem; align-items: center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <?= $row['totais'] ?> Membros (Admin/Users)
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem; flex-shrink: 0;">
                        <button onclick='editPq(<?= json_encode($row) ?>)' class="btn btn-ghost" style="padding: 0.6rem;">Editar</button>
                        <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('ATENÇÃO! Excluir este contexto paroquial afeta todos os usuários e lógicas associadas a ele. Deseja prosseguir?')" class="btn" style="padding: 0.6rem; color: #ef4444; font-size: 0.75rem; border: 1px solid rgba(239, 68, 68, 0.3);">Excluir</a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            
        </main>
    </div>

    <!-- Modal Form -->
    <div class="modal" id="formModal">
        <div class="modal-dialog">
            <header class="modal-header">
                <h2>Gestão da Sede/Paróquia</h2>
                <button type="button" class="btn-close" onclick="closeModal()">×</button>
            </header>
            
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="id" id="paroquiaId">
                <div class="modal-body">
                    <div class="form-group">
                        <label>NOME OFICIAL DA PARÓQUIA / SEDE</label>
                        <input type="text" name="nome" id="paroquiaNome" required>
                    </div>

                    <div class="form-group" style="background: rgba(255,255,255,0.02); padding: 1rem; border-radius: 16px; border: 1px dashed var(--border);">
                        <label>ÍCONE DO CONTEXTO (.PNG)</label>
                        <input type="file" name="icone" accept=".png" style="width: 100%; border: none; padding: 0.5rem; background: transparent; font-size: 0.8rem; color: var(--text-dim);">
                        <p style="font-size: 0.7rem; color: var(--text-ghost); margin-top: 0.5rem;">Tamanho recomendado: Imagem quadrada (ex: 200x200). Aparecerá no menu lateral substituindo o brasão nativo.</p>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary shimmer">Salvar Configurações</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openModal() {
        document.getElementById('paroquiaId').value = '';
        document.getElementById('paroquiaNome').value = '';
        document.getElementById('formModal').classList.add('show');
    }
    
    function closeModal() {
        document.getElementById('formModal').classList.remove('show');
    }
    
    function editPq(data) {
        document.getElementById('paroquiaId').value = data.id;
        document.getElementById('paroquiaNome').value = data.nome;
        document.getElementById('formModal').classList.add('show');
    }
    </script>
</body>
</html>
