<?php
/**
 * â”€ DATABASE REPOSITORY (SOLID WRAPPER) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
 * Camada unificada para extinguir interpolaÃ§Ã£o SQL manual
 */
function db_execute(mysqli $db, string $sql, array $params = []): mysqli_stmt|false {
    $stmt = $db->prepare($sql);
    if (!$stmt) return false;
    
    if (!empty($params)) {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) $types .= 'i';
            elseif (is_float($param)) $types .= 'd';
            else $types .= 's';
        }
        $stmt->bind_param($types, ...$params);
    }
    
    if (!$stmt->execute()) return false;
    return $stmt;
}

function db_query(mysqli $db, string $sql, array $params = []) {
    $stmt = db_execute($db, $sql, $params);
    if (!$stmt) return false;
    $res = $stmt->get_result();
    $stmt->close();
    return $res;
}

function db_fetch_all(mysqli $db, string $sql, array $params = []): array {
    $stmt = db_execute($db, $sql, $params);
    if (!$stmt) return [];
    $res = $stmt->get_result();
    $data = [];
    if ($res) {
        while ($row = $res->fetch_assoc()) $data[] = $row;
    }
    $stmt->close();
    return $data;
}

function db_fetch_one(mysqli $db, string $sql, array $params = []): ?array {
    $stmt = db_execute($db, $sql, $params);
    if (!$stmt) return null;
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();
    return $row ?: null;
}

