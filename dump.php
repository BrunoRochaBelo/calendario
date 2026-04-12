<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Database Utility: Master Console (v4.0)
 * Logic: Snapshot -> Aggressive Purge -> Orderly Rebuild
 * ═══════════════════════════════════════════════════════
 */

require_once __DIR__ . '/conexao C.php';

// Ativar exibição de erros para diagnóstico
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($conn) || $conn->connect_error) {
    die("Erro: Conexão com o banco de dados não disponível. Verifique o arquivo 'conexao C.php'.");
}

// Dados embutidos da tabela perfis para garantir restauração mesmo se houver falha no backup dinâmico
const PERFIS_SEED_DATA = [
    ["id" => "2", "paroquia_id" => "2", "nome_perfil" => "ADMINISTRADOR PAROQUIAL", "descricao" => null, "perm_ver_calendario" => "1", "perm_criar_eventos" => "1", "perm_editar_eventos" => "1", "perm_excluir_eventos" => "1", "perm_ver_restritos" => "1", "perm_admin_usuarios" => "1", "perm_admin_sistema" => "1", "perm_ver_logs" => "1", "perm_cadastrar_usuario" => "1"],
    ["id" => "3", "paroquia_id" => "2", "nome_perfil" => "VIGÁRIO", "descricao" => "", "perm_ver_calendario" => "1", "perm_criar_eventos" => "0", "perm_editar_eventos" => "0", "perm_excluir_eventos" => "0", "perm_ver_restritos" => "0", "perm_admin_usuarios" => "0", "perm_admin_sistema" => "0", "perm_ver_logs" => "0", "perm_cadastrar_usuario" => "0"],
    ["id" => "4", "paroquia_id" => "2", "nome_perfil" => "DIACONO", "descricao" => "", "perm_ver_calendario" => "1", "perm_criar_eventos" => "0", "perm_editar_eventos" => "0", "perm_excluir_eventos" => "0", "perm_ver_restritos" => "0", "perm_admin_usuarios" => "0", "perm_admin_sistema" => "0", "perm_ver_logs" => "0", "perm_cadastrar_usuario" => "0"],
    ["id" => "5", "paroquia_id" => "2", "nome_perfil" => "SECRETARIA", "descricao" => "", "perm_ver_calendario" => "1", "perm_criar_eventos" => "1", "perm_editar_eventos" => "1", "perm_excluir_eventos" => "1", "perm_ver_restritos" => "1", "perm_admin_usuarios" => "0", "perm_admin_sistema" => "0", "perm_ver_logs" => "0", "perm_cadastrar_usuario" => "0"],
    ["id" => "6", "paroquia_id" => "2", "nome_perfil" => "PASCOM ADM", "descricao" => "", "perm_ver_calendario" => "1", "perm_criar_eventos" => "1", "perm_editar_eventos" => "1", "perm_excluir_eventos" => "1", "perm_ver_restritos" => "0", "perm_admin_usuarios" => "0", "perm_admin_sistema" => "0", "perm_ver_logs" => "0", "perm_cadastrar_usuario" => "1"],
    ["id" => "7", "paroquia_id" => "2", "nome_perfil" => "PASCOM AGENTE", "descricao" => "", "perm_ver_calendario" => "1", "perm_criar_eventos" => "1", "perm_editar_eventos" => "0", "perm_excluir_eventos" => "0", "perm_ver_restritos" => "0", "perm_admin_usuarios" => "0", "perm_admin_sistema" => "0", "perm_ver_logs" => "0", "perm_cadastrar_usuario" => "0"],
    ["id" => "8", "paroquia_id" => "2", "nome_perfil" => "PASCOM AGENTE 2", "descricao" => "", "perm_ver_calendario" => "1", "perm_criar_eventos" => "0", "perm_editar_eventos" => "0", "perm_excluir_eventos" => "0", "perm_ver_restritos" => "0", "perm_admin_usuarios" => "0", "perm_admin_sistema" => "0", "perm_ver_logs" => "0", "perm_cadastrar_usuario" => "0"],
    ["id" => "9", "paroquia_id" => "2", "nome_perfil" => "CORDENADOR PASTORAL", "descricao" => "", "perm_ver_calendario" => "1", "perm_criar_eventos" => "0", "perm_editar_eventos" => "0", "perm_excluir_eventos" => "0", "perm_ver_restritos" => "0", "perm_admin_usuarios" => "0", "perm_admin_sistema" => "0", "perm_ver_logs" => "0", "perm_cadastrar_usuario" => "0"],
    ["id" => "10", "paroquia_id" => "2", "nome_perfil" => "FIEL DA IGREJA", "descricao" => null, "perm_ver_calendario" => "1", "perm_criar_eventos" => "1", "perm_editar_eventos" => "1", "perm_excluir_eventos" => "1", "perm_ver_restritos" => "1", "perm_admin_usuarios" => "0", "perm_admin_sistema" => "0", "perm_ver_logs" => "0", "perm_cadastrar_usuario" => "1"],
    ["id" => "11", "paroquia_id" => "2", "nome_perfil" => "VISITANTE", "descricao" => "", "perm_ver_calendario" => "1", "perm_criar_eventos" => "0", "perm_editar_eventos" => "0", "perm_excluir_eventos" => "0", "perm_ver_restritos" => "0", "perm_admin_usuarios" => "0", "perm_admin_sistema" => "0", "perm_ver_logs" => "0", "perm_cadastrar_usuario" => "0"]
];

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
    // 1. COLETA DE INFORMAÇÕES
    foreach ($tables as $t) {
        $data = fetchTableData($conn, $t);
        // Garantir que perfis tenha dados, se possível
        if ($t === 'perfis' && empty($data)) {
            $data = PERFIS_SEED_DATA;
        }
        $tableDataSnapshot[$t] = [
            'count' => count($data),
            'create' => fetchCreateTable($conn, $t),
            'rows' => $data
        ];
    }

    if ($mode === 'rebuild' && $confirm === 'REBUILD') {
        $executionLog[] = "Iniciando processo agressivo de limpeza...";
        
        $conn->query("SET FOREIGN_KEY_CHECKS=0;");

        // 2. APAGAR TODAS AS TABELAS COM CONFERÊNCIA UM A UM
        $maxAttempts = 5;
        $attempt = 0;
        
        while ($attempt < $maxAttempts) {
            $currentTables = fetchTables($conn);
            if (empty($currentTables)) break;
            
            $attempt++;
            $executionLog[] = "Tentativa de limpeza #{$attempt}... (" . count($currentTables) . " restantes)";
            
            foreach ($currentTables as $t) {
                if ($conn->query("DROP TABLE IF EXISTS " . qIdent($t))) {
                    $executionLog[] = "[OK] Tabela '{$t}' removida.";
                } else {
                    $executionLog[] = "[FAIL] Tabela '{$t}' erro: " . $conn->error;
                }
            }
        }

        $finalCheck = fetchTables($conn);
        if (!empty($finalCheck)) {
            throw new Exception("Falha ao apagar tabelas apos {$maxAttempts} tentativas: " . implode(', ', $finalCheck));
        }

        $executionLog[] = "Banco de dados limpo com sucesso.";

        // 3. RECRIAÇÃO
        $executionLog[] = "Iniciando recriação das tabelas...";

        foreach ($tableDataSnapshot as $name => $info) {
            if (empty($info['create'])) continue;
            
            if ($conn->query($info['create'])) {
                $executionLog[] = "[OK] Estrutura '{$name}' recriada.";
                
                // 4. RESTAURAÇÃO DE DADOS
                $rowsToInsert = $info['rows'];
                // Forçar uso do SEED se for a tabela perfis e estiver vazia no snapshot (segurança extra)
                if ($name === 'perfis' && empty($rowsToInsert)) {
                    $rowsToInsert = PERFIS_SEED_DATA;
                }
                
                if (!empty($rowsToInsert)) {
                    $insertSql = buildInsertSql($conn, $name, $rowsToInsert);
                    // Usar query simples para cada insert ou multi_query
                    if ($conn->multi_query($insertSql)) {
                        do { if ($res = $conn->store_result()) $res->free(); } while ($conn->more_results() && $conn->next_result());
                        $executionLog[] = "[OK] Dados restaurados para '{$name}' (" . count($rowsToInsert) . " registros).";
                    } else {
                        $executionLog[] = "[WARN] Erro ao restaurar dados de '{$name}': " . $conn->error;
                    }
                }
            } else {
                $executionLog[] = "[ERROR] Falha ao recriar '{$name}': " . $conn->error;
            }
        }

        $conn->query("SET FOREIGN_KEY_CHECKS=1;");
        $executionLog[] = "Processo finalizado com sucesso.";
    }
} catch (Throwable $e) {
    $executionLog[] = "ERRO CRÍTICO: " . $e->getMessage();
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
    <title>Database Master PRO | PASCOM</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0b1120;
            --card-bg: rgba(15, 23, 42, 0.8);
            --accent: #3b82f6;
            --accent-glow: rgba(59, 130, 246, 0.4);
            --danger: #f43f5e;
            --success: #10b981;
            --text-main: #f8fafc;
            --text-dim: #64748b;
            --glass-border: rgba(255, 255, 255, 0.08);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg);
            background-image: 
                radial-gradient(circle at 10% 10%, rgba(59, 130, 246, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 90% 90%, rgba(244, 63, 94, 0.1) 0%, transparent 40%);
            color: var(--text-main);
            min-height: 100vh;
            padding: 2.5rem;
            line-height: 1.6;
            overflow-x: hidden;
        }

        .container { max-width: 1200px; margin: 0 auto; }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3.5rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid var(--glass-border);
        }

        h1 { font-size: 2.5rem; font-weight: 700; letter-spacing: -0.05em; }
        h1 span { color: var(--accent); background: linear-gradient(to right, #3b82f6, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3.5rem;
        }

        .card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 1.5rem;
            padding: 1.75rem;
            position: relative;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card:hover {
            transform: translateY(-8px);
            border-color: var(--accent);
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.5), 0 0 20px var(--accent-glow);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .table-name { font-weight: 600; font-size: 1.15rem; color: #fff; }
        .row-count { font-size: 0.95rem; color: var(--text-dim); }
        
        .badge {
            background: rgba(59, 130, 246, 0.15);
            color: var(--accent);
            padding: 0.35rem 0.85rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .actions { display: flex; gap: 1.25rem; flex-wrap: wrap; }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.85rem 1.75rem;
            border-radius: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            border: none;
            font-family: inherit;
            font-size: 0.95rem;
        }

        .btn-primary { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; box-shadow: 0 4px 14px 0 rgba(37, 99, 235, 0.39); }
        .btn-primary:hover { transform: scale(1.02); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45); }

        .btn-danger { background: rgba(244, 63, 94, 0.1); color: var(--danger); border: 1px solid rgba(244, 63, 94, 0.2); }
        .btn-danger:hover { background: var(--danger); color: white; box-shadow: 0 0 25px rgba(244, 63, 94, 0.3); }

        .log-section {
            margin-top: 3rem;
            background: rgba(2, 6, 23, 0.6);
            border-radius: 1.5rem;
            padding: 2rem;
            font-family: 'ui-monospace', 'Cascadia Code', 'Source Code Pro', monospace;
            font-size: 0.85rem;
            border: 1px solid var(--glass-border);
            max-height: 400px;
            overflow-y: auto;
        }

        .log-section h3 { margin-bottom: 1.5rem; font-family: 'Outfit'; font-size: 1.25rem; color: var(--accent); display: flex; align-items: center; gap: 0.75rem; }

        .log-entry { margin-bottom: 0.65rem; border-left: 3px solid #1e293b; padding-left: 1.25rem; color: #cbd5e1; }
        .log-entry.error { border-color: var(--danger); color: #fda4af; }
        .log-entry.success { border-color: var(--success); color: #6ee7b7; }

        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(2, 6, 23, 0.9);
            backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal {
            background: #0f172a;
            border: 2px solid var(--danger);
            border-radius: 2rem;
            padding: 3.5rem;
            max-width: 600px;
            width: 90%;
            text-align: center;
            box-shadow: 0 0 50px rgba(244, 63, 94, 0.2);
        }

        .modal h2 { color: var(--danger); margin-bottom: 1.25rem; font-size: 2rem; }
        .modal p { color: #94a3b8; margin-bottom: 2.5rem; font-size: 1.1rem; }
        
        .modal-btns { display: flex; gap: 1.5rem; justify-content: center; }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }

        @keyframes scanning { 0% { opacity: 0.3; } 50% { opacity: 1; } 100% { opacity: 0.3; } }
        .rebuilding { animation: scanning 2s infinite; pointer-events: none; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div>
                <h1>Database<span>Master PRO</span></h1>
                <p style="color: var(--text-dim); font-size: 1rem; margin-top: 0.5rem;">Sincronização Agressiva & Restauração de Integridade</p>
            </div>
            <div class="actions">
                <button class="btn btn-primary" onclick="window.location.reload()">Sincronizar Dashboard</button>
                <button class="btn btn-danger" onclick="showRebuildModal()">Reset Total & Rebuild</button>
            </div>
        </header>

        <?php if (!empty($executionLog)): ?>
        <div class="log-section">
            <h3><span>⚡</span> Relatório de Operação</h3>
            <?php foreach ($executionLog as $log): ?>
                <?php 
                    $type = '';
                    if (str_contains($log, 'ERRO')) $type = 'error';
                    elseif (str_contains($log, '[OK]') || str_contains($log, 'Sucesso')) $type = 'success';
                ?>
                <div class="log-entry <?php echo $type; ?>">
                    <span style="color: #475569; margin-right: 1rem;"><?php echo date('H:i:s'); ?></span> 
                    <?php echo htmlspecialchars($log); ?>
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
                    <span class="badge">Ativo</span>
                </div>
                <div class="row-count">
                    <strong><?php echo $info['count']; ?></strong> registros vinculados
                </div>
                <div style="margin-top: 1.25rem; font-size: 0.8rem;">
                    <?php if ($name === 'perfis'): ?>
                        <span style="color: var(--success); display:flex; align-items:center; gap:0.5rem;">
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.9L9.03 9.122a2 2 0 001.938 0L17.834 4.9A2 2 0 0016 1.5H4a2 2 0 00-1.834 3.4zM18 8a2 2 0 01-2 2h-1.06l-4.705 2.883a4 4 0 01-4.47 0L1.06 10H0V9l7.03 4.317a2 2 0 002.238 0L16.297 9h.001a1 1 0 01.707.293l1 1A1 1 0 0118 11v1h-3a1 1 0 00-1 1v2a1 1 0 001 1h3a1 1 0 001-1v-5a2 2 0 012-2z"/></svg>
                            Cache de Perfis Embutido
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="modal-overlay" id="rebuildModal">
        <div class="modal">
            <h2>OPERACÃO CRÍTICA</h2>
            <p>O sistema irá realizar uma <strong>limpeza agressiva</strong>, verificando a exclusão de todas as tabelas individualmente até que o banco esteja vazio, para então reconstruir tudo. Deseja prosseguir?</p>
            <div class="modal-btns">
                <button class="btn" style="background: rgba(255,255,255,0.05); color: #fff;" onclick="hideRebuildModal()">Abortar</button>
                <a href="?mode=rebuild&confirm=REBUILD" class="btn btn-danger" style="background: var(--danger); color:#fff">Confirmar Limpeza Total</a>
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
        
        if (window.location.search.includes('mode=rebuild')) {
            document.body.classList.add('rebuilding');
            // Redirecionar após 5 segundos para limpar a URL, mas permitindo ler o log
            setTimeout(() => {
                // window.location.href = 'dump.php'; 
            }, 10000);
        }
    </script>
</body>
</html>

