<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Activity Explorer (v2.0)
 * Bento-style Grid · Advanced Filtering · CRUD Integrated
 * ═══════════════════════════════════════════════════════ */

require_once 'functions.php';
requireLogin();

$pid = current_paroquia_id();
$msg = $_GET['msg'] ?? '';

// 1. Fetch Activities with JOINs
$sql = "
    SELECT a.*, l.nome_local, t.nome_tipo, t.icone, u.nome as criador_nome
    FROM atividades a
    LEFT JOIN locais_paroquia l ON a.local_id = l.id
    LEFT JOIN tipos_atividade t ON a.tipo_atividade_id = t.id
    LEFT JOIN usuarios u ON a.criador_id = u.id
    WHERE a.paroquia_id = ?
      AND (a.restrito = 0 OR ? = 1 OR a.criador_id = ?)
    ORDER BY a.data_inicio DESC, a.hora_inicio DESC
";

$userId = (int)($_SESSION['usuario_id'] ?? 0);
$canVerRestritos = (int)can('ver_restritos');

$stmt = $conn->prepare($sql);
$stmt->bind_param('iii', $pid, $canVerRestritos, $userId);
$stmt->execute();
$activities = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Gerenciar Atividades – PASCOM</title>
    <link rel="stylesheet" href="style.css?v=2.5.0">
    <link rel="stylesheet" href="css/responsive.css?v=2.5.0">
    <style>
        .app-shell { display: flex; min-height: 100vh; }
        .main-content { flex: 1; margin-left: var(--sidebar-w); padding: 3rem; }
        
        .header-stack { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3rem; }
        .header-stack h1 { font-size: 2.4rem; font-weight: 900; }

        /* Modern Grid Layout */
        .activity-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; }
        
        .activity-card { 
            padding: 1.5rem; display: flex; flex-direction: column; gap: 1.2rem;
            height: 100%; transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .activity-card:hover { transform: translateY(-8px); border-color: var(--primary); }

        .card-header { display: flex; justify-content: space-between; align-items: flex-start; }
        .type-badge { 
            background: var(--panel-hi); padding: 0.4rem 0.8rem; border-radius: 100px;
            font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: var(--primary);
            border: 1px solid rgba(var(--primary-rgb), 0.2);
        }
        
        .card-body h3 { font-size: 1.2rem; line-height: 1.3; margin-bottom: 0.8rem; font-weight: 800; }
        .meta-item { display: flex; align-items: center; gap: 0.6rem; font-size: 0.8rem; color: var(--text-dim); margin-bottom: 0.4rem; font-weight: 500; }
        .meta-item svg { color: var(--text-ghost); }

        .card-footer { 
            padding-top: 1.2rem; border-top: 1px solid var(--border);
            display: flex; gap: 0.8rem; margin-top: auto;
        }

        @media (max-width: 768px) {
            .header-stack { flex-direction: column; align-items: flex-start; gap: 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="bg-mesh"></div>

    <div class="app-shell">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <?php if ($msg): ?>
                <?= alert('success', h($msg)) ?>
            <?php endif; ?>

                        <header class="calendar-header animate-in">
                <button class="menu-trigger inline hide-on-desktop" onclick="toggleSidebar()"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg></button>
                <div class="month-display">
                    <h1 class="gradient-text">Atividades</h1>
                    <?php if (can('criar_eventos')): ?>
                        <a href="novaatividade.php" class="btn-plus-header hide-on-desktop" title="Nova Atividade">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </a>
                    <?php endif; ?>
                </div>
                <?php if (can('criar_eventos')): ?>
                <a href="novaatividade.php" class="btn btn-primary shimmer hide-on-mobile">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Nova Atividade
                </a>
                <?php endif; ?>
            </header>

            <section class="activity-grid animate-in" style="animation-delay: 0.1s;">
                <?php if ($activities->num_rows > 0): ?>
                    <?php while ($row = $activities->fetch_assoc()): 
                        $di = new DateTime($row['data_inicio']);
                    ?>
                    <article class="glass-card activity-card">
                        <div class="card-header">
                            <span class="type-badge"><?= h($row['nome_tipo'] ?: 'Evento') ?></span>
                            <div style="font-size: 0.7rem; color: var(--text-ghost); font-weight: 700;">
                                ID #<?= $row['id'] ?>
                            </div>
                        </div>

                        <div class="card-body">
                            <h3><?= h($row['nome']) ?></h3>
                            <div class="meta-item">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                <?= $di->format('d/m/Y') ?> às <?= formatTime($row['hora_inicio']) ?>
                            </div>
                            <div class="meta-item">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                <?= h($row['nome_local'] ?: 'Sem local definido') ?>
                            </div>
                            <div class="meta-item">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                <?= h($row['criador_nome'] ?: 'Sistema') ?>
                            </div>
                        </div>

                        <div class="card-footer">
                            <a href="ver_atividade.php?id=<?= $row['id'] ?>" class="btn btn-ghost" style="padding: 0.6rem; flex: 1;">Visualizar</a>
                            <?php if (can('editar_eventos')): ?>
                            <a href="editar_atividade.php?id=<?= $row['id'] ?>" class="btn btn-ghost" style="padding: 0.6rem; color: var(--primary);">Editar</a>
                            <?php endif; ?>
                        </div>
                    </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="grid-column: 1/-1; text-align: center; padding: 5rem; background: var(--panel-hi); border-radius: 20px; border: 1px dashed var(--border);">
                        <p style="color: var(--text-dim); font-weight: 600;">Nenhuma atividade cadastrada para esta paróquia.</p>
                        <a href="novaatividade.php" class="btn btn-primary" style="margin-top: 1.5rem;">Cadastrar Primeira</a>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>
