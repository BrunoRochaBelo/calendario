<?php
require_once __DIR__ . '/conexao.php';

/**
 * Script utilitario:
 * - cria a tabela `tabelaperfil` com a mesma estrutura de `perfis`
 * - copia os dados atuais de `perfis` para `tabelaperfil`
 *
 * Uso:
 *   php criar_tabelaperfil.php
 * ou abrir no navegador:
 *   http://localhost/calender/criar_tabelaperfil.php
 */

header('Content-Type: text/plain; charset=utf-8');

try {
    $conn->begin_transaction();

    $exists = $conn->query("SHOW TABLES LIKE 'perfis'");
    if (!$exists || $exists->num_rows === 0) {
        throw new RuntimeException("Tabela origem 'perfis' nao encontrada.");
    }

    if (!$conn->query("CREATE TABLE IF NOT EXISTS tabelaperfil LIKE perfis")) {
        throw new RuntimeException("Falha ao criar tabela 'tabelaperfil': " . $conn->error);
    }

    if (!$conn->query("TRUNCATE TABLE tabelaperfil")) {
        throw new RuntimeException("Falha ao limpar tabela 'tabelaperfil': " . $conn->error);
    }

    if (!$conn->query("INSERT INTO tabelaperfil SELECT * FROM perfis")) {
        throw new RuntimeException("Falha ao copiar dados para 'tabelaperfil': " . $conn->error);
    }

    $countRes = $conn->query("SELECT COUNT(*) AS total FROM tabelaperfil");
    $total = 0;
    if ($countRes) {
        $row = $countRes->fetch_assoc();
        $total = (int)($row['total'] ?? 0);
    }

    $conn->commit();
    echo "OK: tabela 'tabelaperfil' criada/atualizada com {$total} registro(s)." . PHP_EOL;
} catch (Throwable $e) {
    if ($conn->errno === 0 || $conn->ping()) {
        @ $conn->rollback();
    }
    http_response_code(500);
    echo "ERRO: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

