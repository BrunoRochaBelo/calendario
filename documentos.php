<?php
require_once 'functions.php';
requireLogin();

$documents = [
    [
        'id' => 'resumida',
        'title' => 'Analise Resumida',
        'description' => 'Versao curta com falhas, melhorias e proximas abas sugeridas.',
        'filename' => 'analise_sistema_resumida.doc',
    ],
    [
        'id' => 'detalhada',
        'title' => 'Analise Detalhada',
        'description' => 'Versao completa com testes em browser, riscos, layout e recomendacoes.',
        'filename' => 'analise_sistema_detalhada.doc',
    ],
];

function document_exists(string $filename): bool {
    return is_file(__DIR__ . DIRECTORY_SEPARATOR . $filename);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Documentos de Analise — PASCOM</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .app-shell { display: flex; min-height: 100vh; }
        .main-content { flex: 1; margin-left: var(--sidebar-w); padding: 3rem; transition: margin 0.3s; }
        .header-stack { display: flex; justify-content: space-between; align-items: flex-end; gap: 1.5rem; margin-bottom: 2rem; }
        .doc-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem; }
        .doc-card { padding: 1.75rem; display: flex; flex-direction: column; gap: 1rem; }
        .doc-title { font-size: 1.15rem; font-weight: 900; }
        .doc-desc { color: var(--text-dim); font-size: 0.9rem; line-height: 1.5; }
        .doc-meta { display: flex; align-items: center; justify-content: space-between; gap: 1rem; font-size: 0.78rem; color: var(--text-ghost); padding-top: 0.8rem; border-top: 1px solid var(--border); }
        .doc-actions { display: flex; gap: 0.75rem; flex-wrap: wrap; }
        .badge { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.65rem; border-radius: 999px; background: rgba(var(--primary-rgb), 0.12); color: var(--primary); font-size: 0.7rem; font-weight: 800; letter-spacing: 0.04em; text-transform: uppercase; }
        .empty-state { padding: 3rem; text-align: center; border: 1px dashed var(--border); border-radius: 20px; background: rgba(255,255,255,0.02); color: var(--text-dim); }

        @media (max-width: 1024px) {
            .main-content { margin-left: 0; padding: 1.5rem; padding-top: 5rem; }
            .header-stack { flex-direction: column; align-items: flex-start; }
            .doc-grid { grid-template-columns: 1fr; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="bg-mesh"></div>
    <div class="app-shell">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <header class="header-stack animate-in">
                <div>
                    <p style="font-size:0.75rem; font-weight:800; letter-spacing:0.15em; color:var(--text-ghost);">ARQUIVOS</p>
                    <h1 class="gradient-text">Documentos de Analise</h1>
                    <p style="color:var(--text-dim); font-size:0.95rem; margin-top:0.4rem;">Baixe as duas versoes da revisao do sistema para leitura e aprovacao.</p>
                </div>
            </header>

            <section class="doc-grid animate-in" style="animation-delay: 0.08s;">
                <?php foreach ($documents as $doc): ?>
                    <article class="glass doc-card">
                        <div style="display:flex; justify-content:space-between; gap:1rem; align-items:flex-start;">
                            <div>
                                <div class="badge">DOC</div>
                                <div class="doc-title" style="margin-top:0.8rem;"><?= h($doc['title']) ?></div>
                            </div>
                            <div style="font-size:0.72rem; color:var(--text-ghost); text-align:right;">
                                <?= document_exists($doc['filename']) ? 'Disponivel' : 'Ausente' ?>
                            </div>
                        </div>
                        <div class="doc-desc"><?= h($doc['description']) ?></div>
                        <div class="doc-actions">
                            <?php if (document_exists($doc['filename'])): ?>
                                <a class="btn btn-primary shimmer" href="/calender/baixar_documento.php?id=<?= h($doc['id']) ?>">Baixar arquivo</a>
                                <a class="btn btn-ghost" href="/calender/<?= h($doc['filename']) ?>" target="_blank" rel="noopener">Abrir arquivo</a>
                            <?php else: ?>
                                <span class="empty-state" style="width:100%;">Arquivo nao encontrado na raiz do projeto.</span>
                            <?php endif; ?>
                        </div>
                        <div class="doc-meta">
                            <span><?= h($doc['filename']) ?></span>
                            <span><?= document_exists($doc['filename']) ? filesize(__DIR__ . DIRECTORY_SEPARATOR . $doc['filename']) . ' bytes' : '-' ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        </main>
    </div>
</body>
</html>
