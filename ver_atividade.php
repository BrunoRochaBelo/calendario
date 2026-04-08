<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Activity Detail View (v2.0)
 * Info Layout · Participant Management · Premium UI
 * ═══════════════════════════════════════════════════════ */

require_once 'functions.php';
requireLogin();

$pid = current_paroquia_id();
$id = (int)($_GET['id'] ?? 0);

// 1. Fetch Activity Details
$sql = "
    SELECT a.*, l.nome_local, l.endereco, l.responsavel, t.nome_tipo, t.icone, u.nome as criador_nome
    FROM atividades a
    LEFT JOIN locais_paroquia l ON a.local_id = l.id
    LEFT JOIN tipos_atividade t ON a.tipo_atividade_id = t.id
    LEFT JOIN usuarios u ON a.criador_id = u.id
    WHERE a.id = ? AND a.paroquia_id = ?
    LIMIT 1
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $id, $pid);
$stmt->execute();
$activity = $stmt->get_result()->fetch_assoc();

if (!$activity) {
    header('Location: atividades.php?error=not_found');
    exit();
}

// 2. Fetch Participants (Inscrições)
$parts_sql = "
    SELECT u.nome, u.email, i.data_inscricao 
    FROM inscricoes i 
    JOIN usuarios u ON i.usuario_id = u.id 
    WHERE i.atividade_id = ?
    ORDER BY i.data_inscricao ASC
";
$stmt_p = $conn->prepare($parts_sql);
$stmt_p->bind_param('i', $id);
$stmt_p->execute();
$participants = $stmt_p->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title><?= h($activity['nome']) ?> – Detalhes</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .app-shell { display: flex; min-height: 100vh; }
        .main-content { flex: 1; margin-left: var(--sidebar-w); padding: 3rem; }
        
        .detail-header { margin-bottom: 3.5rem; display: flex; justify-content: space-between; align-items: flex-start; }
        .detail-header h1 { font-size: 2.8rem; font-weight: 900; margin-top: 0.5rem; line-height: 1.1; }
        
        .info-grid { display: grid; grid-template-columns: 1.8fr 1fr; gap: 2rem; margin-bottom: 3rem; }
        .info-card { padding: 2.5rem; height: 100%; }
        .info-card h3 { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--text-ghost); letter-spacing: 0.1em; margin-bottom: 2rem; }

        .meta-list { display: grid; gap: 1.5rem; }
        .meta-box { display: flex; align-items: flex-start; gap: 1rem; }
        .meta-icon { width: 40px; height: 40px; border-radius: 12px; background: var(--panel-hi); display: flex; align-items: center; justify-content: center; color: var(--primary); }
        .meta-content div { font-size: 0.75rem; font-weight: 700; color: var(--text-dim); margin-bottom: 0.2rem; }
        .meta-content span { font-size: 1rem; font-weight: 700; color: var(--text); }

        .participants-list { display: grid; gap: 1rem; }
        .participant-item { display: flex; align-items: center; gap: 1rem; padding: 1rem; background: rgba(255,255,255,0.02); border-radius: 12px; border: 1px solid var(--border); }
        .p-avatar { width: 32px; height: 32px; border-radius: 8px; background: var(--panel-hi); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.8rem; color: var(--accent); }

        @media (max-width: 1100px) {
            .info-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="bg-mesh"></div>

    <div class="app-shell">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <header class="detail-header animate-in">
                <div>
                    <span class="type-badge" style="background:var(--panel-hi);"><?= h($activity['nome_tipo'] ?: 'Evento') ?></span>
                    <h1 class="gradient-text"><?= h($activity['nome']) ?></h1>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <?php if (can('editar_eventos')): ?>
                    <a href="editar_atividade.php?id=<?= $id ?>" class="btn btn-ghost">Editar Registro</a>
                    <?php endif; ?>
                    <a href="atividades.php" class="btn btn-primary shimmer">Voltar à Lista</a>
                </div>
            </header>

            <div class="info-grid animate-in" style="animation-delay: 0.1s;">
                <section class="glass info-card">
                    <h3>Visão Geral do Evento</h3>
                    <div class="meta-list">
                        <div class="meta-box">
                            <div class="meta-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                            </div>
                            <div class="meta-content">
                                <div>CRONOGRAMA</div>
                                <span><?= formatDate($activity['data_inicio']) ?> às <?= formatTime($activity['hora_inicio']) ?></span>
                            </div>
                        </div>

                        <div class="meta-box">
                            <div class="meta-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            </div>
                            <div class="meta-content">
                                <div>LOCALIZAÇÃO</div>
                                <span><?= h($activity['nome_local'] ?: 'Não definido') ?></span>
                                <p style="font-size: 0.8rem; color: var(--text-dim); margin-top: 0.3rem;"><?= h($activity['endereco'] ?: 'Endereço não disponível') ?></p>
                            </div>
                        </div>

                        <div class="meta-box">
                            <div class="meta-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <div class="meta-content">
                                <div>CRIADO POR</div>
                                <span><?= h($activity['criador_nome'] ?: 'Gerenciador do Sistema') ?></span>
                            </div>
                        </div>

                        <?php if ($activity['descricao']): ?>
                        <div class="meta-box" style="margin-top: 1rem; padding: 1.5rem; background: var(--panel-hi); border-radius: 16px;">
                            <div class="meta-content" style="width: 100%;">
                                <div style="margin-bottom: 0.8rem;">DETALHES DA ATIVIDADE</div>
                                <p style="color: var(--text); line-height: 1.6; font-size: 0.95rem;"><?= nl2br(h($activity['descricao'])) ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="glass info-card">
                    <h3>Participantes Confimados</h3>
                    <div class="participants-list">
                        <?php if ($participants->num_rows > 0): ?>
                            <?php while ($p = $participants->fetch_assoc()): ?>
                            <div class="participant-item">
                                <div class="p-avatar"><?= mb_substr($p['nome'], 0, 1) ?></div>
                                <div style="flex: 1;">
                                    <div style="font-size: 0.85rem; font-weight: 700;"><?= h($p['nome']) ?></div>
                                    <div style="font-size: 0.7rem; color: var(--text-ghost);"><?= h($p['email']) ?></div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div style="text-align: center; padding: 3rem; color: var(--text-ghost);">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 1rem; opacity: 0.4;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                <p>Nenhuma inscrição realizada.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (can('admin_sistema')): ?>
                    <button class="btn btn-ghost" style="width:100%; margin-top:1.5rem; font-size:0.75rem;">Gerenciar Inscrições</button>
                    <?php endif; ?>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
