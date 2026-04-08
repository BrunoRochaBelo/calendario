<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Category Management (v2.0)
 * Activity Types · Icon Sets · Premium UI
 * ═══════════════════════════════════════════════════════ */

require_once 'functions.php';
requireLogin();

$pid = current_paroquia_id();

requirePerm('admin_sistema');

$msg = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';

// 1. Handle CRUD Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $data = sanitize_post($_POST);
    
    if ($action === 'create' || $action === 'update') {
        if (empty($data['nome_tipo'])) {
            $error = 'O nome da categoria é obrigatório.';
        } else {
            if ($action === 'create') {
                $sql = "INSERT INTO tipos_atividade (paroquia_id, nome_tipo, icone) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('iss', $pid, $data['nome_tipo'], $data['icone']);
                
                if ($stmt->execute()) {
                    logAction($conn, 'CRIAR_TIPO', 'tipos_atividade', $conn->insert_id, $data['nome_tipo']);
                    header("Location: tipos_atividade.php?msg=Categoria criada!");
                    exit();
                }
            } else {
                $id = (int)$data['id'];
                $sql = "UPDATE tipos_atividade SET nome_tipo = ?, icone = ? WHERE id = ? AND paroquia_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ssii', $data['nome_tipo'], $data['icone'], $id, $pid);
                
                if ($stmt->execute()) {
                    logAction($conn, 'EDITAR_TIPO', 'tipos_atividade', $id, $data['nome_tipo']);
                    header("Location: tipos_atividade.php?msg=Categoria atualizada!");
                    exit();
                }
            }
        }
    } elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("DELETE FROM tipos_atividade WHERE id = ? AND paroquia_id = ?");
        $stmt->bind_param('ii', $id, $pid);
        if ($stmt->execute()) {
            logAction($conn, 'EXCLUIR_TIPO', 'tipos_atividade', $id);
            header("Location: tipos_atividade.php?msg=Categoria removida.");
            exit();
        }
    }
}

// 2. Fetch Categories
$tipos = $conn->query("SELECT * FROM tipos_atividade WHERE paroquia_id = $pid ORDER BY nome_tipo");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Categorias – PASCOM</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .app-shell { display: flex; min-height: 100vh; }
        .main-content { flex: 1; margin-left: var(--sidebar-w); padding: 3rem; transition: margin 0.3s; }
        
        @media (max-width: 1024px) {
            .main-content { margin-left: 0; padding: 1.5rem; padding-top: 5rem; }
            .header-stack { flex-direction: column; align-items: flex-start; gap: 1.5rem; }
            .types-grid { grid-template-columns: 1fr; }
            .btn-primary { width: 100%; }
        }
        
        .header-stack { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3rem; }
        
        .types-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; }
        .type-card { padding: 1.8rem; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 1.2rem; transition: transform 0.3s var(--ease); }
        .type-card:hover { transform: translateY(-5px); border-color: var(--primary); }

        .type-icon-box { width: 64px; height: 64px; border-radius: 20px; background: var(--panel-hi); display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1.5rem; }
        
        .type-info h3 { font-size: 1.1rem; font-weight: 800; margin-bottom: 0.3rem; }
        
        .type-actions { display: flex; gap: 0.8rem; width: 100%; border-top: 1px solid var(--border); padding-top: 1.2rem; margin-top: 0.5rem; }

        /* Icon Selection Grid */
        .icon-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0.8rem; margin-top: 0.5rem; }
        .icon-opt { cursor: pointer; padding: 0.8rem; border-radius: 10px; background: var(--panel-hi); border: 1px solid transparent; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; transition: all 0.2s; }
        .icon-opt:hover, .icon-opt.selected { background: var(--primary); color: #fff; border-color: var(--primary); }

        .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(10px); z-index: 1000; align-items: center; justify-content: center; padding: 2rem; }
        .modal.active { display: flex; }
        .modal-card { width: 100%; max-width: 500px; padding: 3rem; }
    </style>
</head>
<body>
    <div class="bg-mesh"></div>

    <div class="app-shell">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <?php if ($msg): ?> <?= alert('success', h($msg)) ?> <?php endif; ?>
            <?php if ($error): ?> <?= alert('error', h($error)) ?> <?php endif; ?>

            <header class="header-stack animate-in">
                <div>
                    <p style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.15em; color: var(--text-ghost);">ADMINISTRAÇÃO</p>
                    <h1 class="gradient-text">Categorias de Atividade</h1>
                </div>
                <button onclick="openModal()" class="btn btn-primary shimmer">Nova Categoria</button>
            </header>

            <div class="types-grid animate-in" style="animation-delay: 0.1s;">
                <?php while ($t = $tipos->fetch_assoc()): ?>
                <article class="glass type-card">
                    <div class="type-icon-box">
                        <i class="icon-display"><?= $t['icone'] ?: '📅' ?></i>
                    </div>
                    <div class="type-info">
                        <h3><?= h($t['nome_tipo']) ?></h3>
                    </div>
                    <div class="type-actions">
                        <button onclick='editType(<?= json_encode($t) ?>)' class="btn btn-ghost" style="flex: 1; font-size: 0.8rem;">Editar</button>
                        <form method="POST" style="flex: 1;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $t['id'] ?>">
                            <button type="submit" class="btn btn-ghost" style="width: 100%; color: #ef4444; font-size: 0.8rem;" onclick="return confirm('Remover esta categoria?')">Remover</button>
                        </form>
                    </div>
                </article>
                <?php endwhile; ?>
            </div>
        </main>
    </div>

    <!-- Modal Category -->
    <div id="typeModal" class="modal">
        <form method="POST" class="glass modal-card">
            <input type="hidden" name="action" id="modalAction" value="create">
            <input type="hidden" name="id" id="typeId">
            <input type="hidden" name="icone" id="modalIconValue" value="📅">
            
            <h2 id="modalTitle" style="margin-bottom: 2rem; font-weight: 900;">Nova Categoria</h2>
            
            <div style="display: grid; gap: 1.5rem;">
                <div class="form-group">
                    <label>Nome da Categoria</label>
                    <input type="text" name="nome_tipo" id="modalNome" placeholder="Ex: Celebração, Reunião..." required>
                </div>
                
                <div class="form-group">
                    <label>Escolha um Ícone</label>
                    <div class="icon-grid">
                        <div class="icon-opt" onclick="setIcon('📅')">📅</div>
                        <div class="icon-opt" onclick="setIcon('🙏')">🙏</div>
                        <div class="icon-opt" onclick="setIcon('⛪')">⛪</div>
                        <div class="icon-opt" onclick="setIcon('🍷')">🍷</div>
                        <div class="icon-opt" onclick="setIcon('📖')">📖</div>
                        <div class="icon-opt" onclick="setIcon('🤝')">🤝</div>
                        <div class="icon-opt" onclick="setIcon('📢')">📢</div>
                        <div class="icon-opt" onclick="setIcon('🎵')">🎵</div>
                        <div class="icon-opt" onclick="setIcon('🎨')">🎨</div>
                        <div class="icon-opt" onclick="setIcon('🔥')">🔥</div>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 2;">Confirmar</button>
                    <button type="button" onclick="closeModal()" class="btn btn-ghost" style="flex: 1;">Cancelar</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function openModal() {
            document.getElementById('modalAction').value = 'create';
            document.getElementById('modalTitle').textContent = 'Nova Categoria';
            document.getElementById('typeId').value = '';
            document.getElementById('modalNome').value = '';
            setIcon('📅');
            document.getElementById('typeModal').classList.add('active');
        }

        function editType(t) {
            document.getElementById('modalAction').value = 'update';
            document.getElementById('modalTitle').textContent = 'Editar Categoria';
            document.getElementById('typeId').value = t.id;
            document.getElementById('modalNome').value = t.nome_tipo;
            setIcon(t.icone || '📅');
            document.getElementById('typeModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('typeModal').classList.remove('active');
        }

        function setIcon(icon) {
            document.getElementById('modalIconValue').value = icon;
            document.querySelectorAll('.icon-opt').forEach(opt => {
                opt.classList.toggle('selected', opt.textContent === icon);
            });
        }
    </script>
</body>
</html>
