<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Database Utility: Master Console (v3.0)
 * ═══════════════════════════════════════════════════════
 */

require_once __DIR__ . '/conexao C.php';

function argValue(string $name, ?string $default = null): ?string {
    if (PHP_SAPI === 'cli') {
        global $argv;
        foreach ($argv as $arg) {
            if (str_starts_with($arg, "--{$name}=")) return substr($arg, strlen($name) + 3);
        }
        return $default;
    }
    return isset($_GET[$name]) ? (string)$_GET[$name] : $default;
}

function qIdent(string $name): string {
    return "`" . str_replace("`", "``", $name) . "`";
}

function fetchTables(mysqli $conn): array {
    $res = $conn->query('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
    if (!$res) return [];
    $tables = [];
    while ($row = $res->fetch_row()) $tables[] = (string)$row[0];
    return $tables;
}

function fetchCreateTable(mysqli $conn, string $table): string {
    $res = $conn->query('SHOW CREATE TABLE ' . qIdent($table));
    if (!$res) return '';
    $row = $res->fetch_assoc();
    return (string)($row['Create Table'] ?? '');
}

function fetchTableData(mysqli $conn, string $table): array {
    $res = $conn->query('SELECT * FROM ' . qIdent($table));
    if (!$res) return [];
    $rows = [];
    while ($row = $res->fetch_assoc()) $rows[] = $row;
    return $rows;
}

function buildInsertSql(mysqli $conn, string $table, array $rows): string {
    if (!$rows) return '';
    $columns = array_keys($rows[0]);
    $colSql = implode(', ', array_map(fn($c) => qIdent((string)$c), $columns));
    $sql = '';
    foreach ($rows as $row) {
        $vals = [];
        foreach ($columns as $c) {
            $v = $row[$c];
            $vals[] = ($v === null) ? 'NULL' : "'" . $conn->real_escape_string((string)$v) . "'";
        }
        $sql .= 'INSERT INTO ' . qIdent($table) . " ({$colSql}) VALUES (" . implode(', ', $vals) . ");\n";
    }
    return $sql;
}

$mode = strtolower((string)argValue('mode', 'view'));
$confirm = (string)argValue('confirm', '');
$timestamp = date('Ymd_His');
$backupDir = __DIR__ . '/sql/backups';

if (!is_dir($backupDir)) @mkdir($backupDir, 0777, true);

$tables = fetchTables($conn);
$tableDataSnapshot = [];
$executionLog = [];

try {
    foreach ($tables as $t) {
        $data = fetchTableData($conn, $t);
        $tableDataSnapshot[$t] = [
            'count' => count($data),
            'create' => fetchCreateTable($conn, $t),
            'rows' => $data
        ];
    }

    if ($mode === 'rebuild' && $confirm === 'REBUILD') {
        $sqlRebuild = "SET FOREIGN_KEY_CHECKS=0;\n\n";
        foreach ($tableDataSnapshot as $name => $info) {
            $sqlRebuild .= "DROP TABLE IF EXISTS " . qIdent($name) . ";\n";
        }
        $sqlRebuild .= "\n";
        foreach ($tableDataSnapshot as $name => $info) {
            $sqlRebuild .= $info['create'] . ";\n\n";
        }
        foreach ($tableDataSnapshot as $name => $info) {
            $sqlRebuild .= buildInsertSql($conn, $name, $info['rows']);
            if (!empty($info['rows'])) $sqlRebuild .= "\n";
        }
        $sqlRebuild .= "SET FOREIGN_KEY_CHECKS=1;\n";

        $sqlFile = "{$backupDir}/auto_rebuild_{$timestamp}.sql";
        file_put_contents($sqlFile, $sqlRebuild);

        if ($conn->multi_query($sqlRebuild)) {
            do {
                if ($res = $conn->store_result()) $res->free();
            } while ($conn->more_results() && $conn->next_result());
            $executionLog[] = "Rebuild completo executado com sucesso.";
            $executionLog[] = "Snapshot salvo em: " . basename($sqlFile);
        } else {
            throw new Exception("Falha na execução do SQL: " . $conn->error);
        }
    }
} catch (Throwable $e) {
    $executionLog[] = "ERRO: " . $e->getMessage();
}

// Redirect if it was a rebuild to clear parameters
if ($mode === 'rebuild' && empty($executionLog)) {
     // something went wrong or no tables
}

if (PHP_SAPI === 'cli') {
    echo "=== PASCOM DATABASE CONSOLE ===\n";
    foreach ($tableDataSnapshot as $t => $info) echo "- {$t}: {$info['count']} registros\n";
    foreach ($executionLog as $log) echo "[LOG] {$log}\n";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Master | PASCOM</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --accent: #3b82f6;
            --accent-glow: rgba(59, 130, 246, 0.5);
            --danger: #ef4444;
            --success: #10b981;
            --text-main: #f8fafc;
            --text-dim: #94a3b8;
            --glass-border: rgba(255, 255, 255, 0.05);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg);
            background-image: 
                radial-gradient(circle at 0% 0%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(139, 92, 246, 0.1) 0%, transparent 50%);
            color: var(--text-main);
            min-height: 100vh;
            padding: 2rem;
            line-height: 1.6;
        }

        .container { max-width: 1200px; margin: 0 auto; }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--glass-border);
        }

        h1 { font-size: 2rem; font-weight: 700; letter-spacing: -0.025em; }
        h1 span { color: var(--accent); }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 1.25rem;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            border-color: var(--accent);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3), 0 0 15px var(--accent-glow);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .table-name { font-weight: 600; font-size: 1.1rem; color: var(--accent); }
        .row-count { font-size: 0.875rem; color: var(--text-dim); }
        
        .badge {
            background: rgba(59, 130, 246, 0.1);
            color: var(--accent);
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            border: none;
            font-family: inherit;
        }

        .btn-primary { background: var(--accent); color: white; }
        .btn-primary:hover { filter: brightness(1.1); box-shadow: 0 0 20px var(--accent-glow); }

        .btn-danger { background: rgba(239, 68, 68, 0.1); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.2); }
        .btn-danger:hover { background: var(--danger); color: white; }

        .log-section {
            margin-top: 2rem;
            background: rgba(0,0,0,0.3);
            border-radius: 1rem;
            padding: 1.5rem;
            font-family: 'monospace';
            font-size: 0.875rem;
            border: 1px solid var(--glass-border);
        }

        .log-entry { margin-bottom: 0.5rem; border-left: 2px solid var(--accent); padding-left: 1rem; }
        .log-entry.error { border-color: var(--danger); color: var(--danger); }

        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
        }

        .modal {
            background: var(--bg);
            border: 1px solid var(--danger);
            border-radius: 1.5rem;
            padding: 2.5rem;
            max-width: 500px;
            width: 90%;
            text-align: center;
        }

        .modal h2 { color: var(--danger); margin-bottom: 1rem; }
        .modal p { color: var(--text-dim); margin-bottom: 2rem; }
        
        .modal-btns { display: flex; gap: 1rem; justify-content: center; }

        @keyframes pulse {
            0% { opacity: 0.5; }
            50% { opacity: 1; }
            100% { opacity: 0.5; }
        }

        .rebuilding { animation: pulse 1.5s infinite; pointer-events: none; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div>
                <h1>Database<span>Master</span></h1>
                <p style="color: var(--text-dim); font-size: 0.875rem;">PASCOM — Central de Manutenção e Dados</p>
            </div>
            <div class="actions">
                <button class="btn btn-primary" onclick="window.location.reload()">Atualizar Dashboard</button>
                <button class="btn btn-danger" onclick="showRebuildModal()">Reset & Rebuild Total</button>
            </div>
        </header>

        <?php if (!empty($executionLog)): ?>
        <div class="log-section">
            <h3 style="margin-bottom: 1rem; font-family: 'Outfit';">Log de Execução</h3>
            <?php foreach ($executionLog as $log): ?>
                <div class="log-entry <?php echo str_contains($log, 'ERRO') ? 'error' : ''; ?>">
                    [<?php echo date('H:i:s'); ?>] <?php echo htmlspecialchars($log); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <br>
        <?php endif; ?>

        <div class="stats-grid">
            <?php foreach ($tableDataSnapshot as $name => $info): ?>
            <div class="card">
                <div class="card-header">
                    <span class="table-name"><?php echo htmlspecialchars($name); ?></span>
                    <span class="badge">TABLE</span>
                </div>
                <div class="row-count">
                    <strong><?php echo $info['count']; ?></strong> registros armazenados
                </div>
                <div style="margin-top: 1rem; font-size: 0.75rem; color: var(--text-dim);">
                    <?php if ($name === 'perfis') echo '<span style="color: var(--success);">✓ Proteção Especial Ativa</span>'; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="modal-overlay" id="rebuildModal">
        <div class="modal">
            <h2>AVISO CRÍTICO</h2>
            <p>Esta operação irá apagar todas as tabelas do banco de dados e recriá-las do zero com os dados atuais. Esta ação é irreversível.</p>
            <div class="modal-btns">
                <button class="btn" style="background: rgba(255,255,255,0.1);" onclick="hideRebuildModal()">Cancelar</button>
                <a href="?mode=rebuild&confirm=REBUILD" class="btn btn-danger">Confirmar Rebuild</a>
            </div>
        </div>
    </div>

    <script>
        function showRebuildModal() {
            document.getElementById('rebuildModal').style.display = 'flex';
        }
        function hideRebuildModal() {
            document.getElementById('rebuildModal').style.display = 'none';
        }
        
        // Se houver parâmetros de rebuild na URL, mostrar estado de loading
        if (window.location.search.includes('mode=rebuild')) {
            document.body.classList.add('rebuilding');
            setTimeout(() => {
                window.location.href = 'dump.php';
            }, 3000);
        }
    </script>
</body>
</html>

