<?php
function patchFile($file, $containerClass, $cardClass) {
    if (!file_exists($file)) return;
    $c = file_get_contents($file);
    
    // Add CSS before </head>
    if (strpos($c, '.view-controls') === false) {
        $css = "
        /* ── View Modes ────────────────────────────────────────── */
        .view-controls { display: flex; gap: 0.5rem; background: var(--panel); padding: 0.4rem; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 1.5rem; width: fit-content; }
        .view-btn { padding: 0.5rem; border-radius: 8px; border: none; background: transparent; color: var(--text-dim); cursor: pointer; display: flex; align-items: center; transition: all var(--anim); }
        .view-btn:hover { background: var(--panel-hi); color: var(--text); }
        .view-btn.active { background: var(--primary); color: #fff; box-shadow: var(--sh-primary); }

        /* LIST VIEW */
        .$containerClass.view-list { grid-template-columns: 1fr !important; gap: 0.8rem; }
        .view-list .$cardClass { flex-direction: row; align-items: center; padding: 1rem 1.5rem; justify-content: space-between; }
        .view-list .$cardClass > div { flex-direction: row; align-items: center; gap: 1rem; }
        .view-list .$cardClass p { margin: 0; }
        
        /* COMPACT VIEW */
        .$containerClass.view-compact { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)) !important; gap: 1rem; }
        .view-compact .$cardClass { padding: 1rem; }
        ";
        $c = str_replace("</head>", "<style>$css</style>\n</head>", $c);
    }
    
    // Add container ID if missing
    $c = preg_replace('/(<div class="[^"]*'.$containerClass.'[^"]*")/is', '$1 id="dataContainer"', $c);
    
    // Add Buttons after <header ...>...</header>
    if (strpos($c, 'id="btn-grid"') === false) {
        $btns = "
            <div class=\"view-controls animate-in\" style=\"animation-delay: 0.05s;\">
                <button onclick=\"setView('grid')\" id=\"btn-grid\" class=\"view-btn active\" title=\"Grelha\">
                    <svg width=\"18\" height=\"18\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2.5\"><rect x=\"3\" y=\"3\" width=\"7\" height=\"7\"/><rect x=\"14\" y=\"3\" width=\"7\" height=\"7\"/><rect x=\"14\" y=\"14\" width=\"7\" height=\"7\"/><rect x=\"3\" y=\"14\" width=\"7\" height=\"7\"/></svg>
                </button>
                <button onclick=\"setView('list')\" id=\"btn-list\" class=\"view-btn\" title=\"Lista\">
                    <svg width=\"18\" height=\"18\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2.5\"><line x1=\"8\" y1=\"6\" x2=\"21\" y2=\"6\"/><line x1=\"8\" y1=\"12\" x2=\"21\" y2=\"12\"/><line x1=\"8\" y1=\"18\" x2=\"21\" y2=\"18\"/><line x1=\"3\" y1=\"6\" x2=\"3.01\" y2=\"6\"/><line x1=\"3\" y1=\"12\" x2=\"3.01\" y2=\"12\"/><line x1=\"3\" y1=\"18\" x2=\"3.01\" y2=\"18\"/></svg>
                </button>
                <button onclick=\"setView('compact')\" id=\"btn-compact\" class=\"view-btn\" title=\"Compacto\">
                    <svg width=\"18\" height=\"18\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2.5\"><rect x=\"3\" y=\"3\" width=\"18\" height=\"18\" rx=\"2\" ry=\"2\"/><line x1=\"9\" y1=\"3\" x2=\"9\" y2=\"21\"/></svg>
                </button>
            </div>";
        $c = preg_replace('/(<\/header>)/is', "$1\n$btns", $c);
    }
    
    // Add JS at end
    if (strpos($c, 'function setView(') === false) {
        $js = "
    <script>
        function setView(mode) {
            const container = document.getElementById('dataContainer');
            if(!container) return;
            const btns = document.querySelectorAll('.view-btn');
            container.classList.remove('view-list', 'view-compact');
            if (mode === 'list') container.classList.add('view-list');
            if (mode === 'compact') container.classList.add('view-compact');
            btns.forEach(b => b.classList.remove('active'));
            const btn = document.getElementById('btn-' + mode);
            if(btn) btn.classList.add('active');
            localStorage.setItem('layout-mode', mode);
        }
        document.addEventListener('DOMContentLoaded', () => {
            const savedMode = localStorage.getItem('layout-mode') || 'grid';
            setView(savedMode);
        });
    </script>
</body>";
        $c = str_replace('</body>', $js, $c);
    }
    
    file_put_contents($file, $c);
}

patchFile('c:/xampp/htdocs/calender/gerenciar_catalogo.php', 'catalog-grid', 'catalog-card');
patchFile('c:/xampp/htdocs/calender/tipos_atividade.php', 'types-grid', 'type-card');
patchFile('c:/xampp/htdocs/calender/locais_paroquia.php', 'locais-grid', 'local-card');
patchFile('c:/xampp/htdocs/calender/paroquias.php', 'grid', 'pq-card');

// For Logs
patchFile('c:/xampp/htdocs/calender/logs.php', 'timeline', 'log-item');

echo "Views patched!\n";
