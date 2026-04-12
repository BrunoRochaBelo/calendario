<?php
/**
 * Script para recriar a tabela `perfis` e restaurar dados
 * conforme solicitação do usuário.
 */

require_once __DIR__ . '/conexao C.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $conn->begin_transaction();

    echo "--- INICIANDO RESTAURAÇÃO DA TABELA PERFIS ---\n";

    // 1. Verificar qual tabela de origem existe (fallback para tabelaperfil)
    $sourceTable = 'tabelaperfil';
    $checkSource = $conn->query("SHOW TABLES LIKE '$sourceTable'");
    
    if (!$checkSource || $checkSource->num_rows === 0) {
        throw new Exception("Erro: A tabela de origem '$sourceTable' não foi encontrada para copiar os dados.");
    }

    echo "Achei a tabela de origem: $sourceTable\n";

    // 2. Apagar a tabela perfis se ela já existir para recriar do zero
    $conn->query("SET FOREIGN_KEY_CHECKS = 0;");
    $conn->query("DROP TABLE IF EXISTS perfis;");
    echo "Tabela 'perfis' antiga removida (se existia).\n";

    // 3. Criar a tabela perfis baseada na estrutura da tabela de origem
    if (!$conn->query("CREATE TABLE perfis LIKE $sourceTable")) {
        throw new Exception("Falha ao criar estrutura da tabela 'perfis': " . $conn->error);
    }
    echo "Estrutura da tabela 'perfis' criada com sucesso.\n";

    // 4. Copiar os dados
    if (!$conn->query("INSERT INTO perfis SELECT * FROM $sourceTable")) {
        throw new Exception("Falha ao copiar dados para 'perfis': " . $conn->error);
    }
    
    $countRes = $conn->query("SELECT COUNT(*) as total FROM perfis");
    $total = $countRes->fetch_assoc()['total'];
    
    echo "Sucesso: $total registros copiados para 'perfis'.\n";

    $conn->query("SET FOREIGN_KEY_CHECKS = 1;");
    $conn->commit();

    echo "\n[OK] Operação concluída com sucesso.\n";

} catch (Throwable $e) {
    if (isset($conn) && $conn->ping()) {
        $conn->rollback();
        $conn->query("SET FOREIGN_KEY_CHECKS = 1;");
    }
    echo "\n[ERRO] " . $e->getMessage() . "\n";
    exit(1);
}
