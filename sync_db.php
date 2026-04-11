<?php
require_once 'functions.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

echo "Starting Database Sync...\n";

// 1. Core Schema Updates
$updates = [
    "atividades" => [
        "cor VARCHAR(7) DEFAULT '#3b82f6' AFTER categoria_id",
        "restrito TINYINT(1) DEFAULT 0 AFTER cor",
        "publico TINYINT(1) DEFAULT 1 AFTER restrito",
        "permite_inscricao TINYINT(1) DEFAULT 0 AFTER publico"
    ],
    "perfis" => [
        "perm_cadastrar_usuario TINYINT(1) DEFAULT 0 AFTER perm_ver_restritos"
    ],
    "paroquias" => [
        "cidade VARCHAR(100) DEFAULT NULL",
        "estado CHAR(2) DEFAULT NULL",
        "diocese VARCHAR(255) DEFAULT NULL",
        "ativo TINYINT(1) DEFAULT 1"
    ],
    "tipos_atividade" => [
        "paroquia_id INT(10) UNSIGNED DEFAULT NULL"
    ]
];

foreach ($updates as $table => $columns) {
    foreach ($columns as $colDef) {
        $colName = explode(" ", trim($colDef))[0];
        $check = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$colName'");
        if ($check && $check->num_rows > 0) {
            echo "Column $table.$colName already exists. Skipping...\n";
            continue;
        }
        
        $sql = "ALTER TABLE `$table` ADD COLUMN $colDef";
        echo "Running: $sql ... ";
        if ($conn->query($sql)) {
            echo "OK\n";
        } else {
            echo "FAIL: " . $conn->error . "\n";
        }
    }
}

// 2. Call project's internal sync functions
echo "Running internal 'ensure' functions...\n";

ensurePermissionColumns($conn);
ensureUserPermissionColumns($conn);
ensureUserProfileNameColumn($conn);
ensureUserPermissionsMaterialized($conn);
ensureUserPhotoColumn($conn);
ensureUserLastLoginColumn($conn);
ensurePerfisHierarchyRemoved($conn);
ensureAuthThrottleTable($conn);
ensureWorkingGroupsTables($conn);
ensureInscricoesTable($conn);
ensureEventActivitiesStructure($conn);

// 3. Seed default data if parish exists
$res = $conn->query("SELECT id FROM paroquias LIMIT 1");
if ($res && $row = $res->fetch_assoc()) {
    $pid = (int)$row['id'];
    echo "Seeding default catalog for parish $pid...\n";
    seedDefaultEventActivities($conn, $pid);
    ensureDefaultVisitorGroup($conn, $pid);
}

echo "\nDatabase Sync Completed.\n";
?>
