<?php
require_once 'functions.php';

require 'conexao.php';

$sql = "UPDATE usuarios u JOIN perfis p ON u.perfil_id = p.id SET u.perfil_nome = p.nome";
if ($conn->query($sql)) {
    echo "Nomes de perfil atualizados com sucesso ({$conn->affected_rows} linhas modificadas).\n";
} else {
    echo "Erro: " . $conn->error . "\n";
}

// Também atualizar usuários que não têm perfil_id mas deveriam ser Visitante
$sql2 = "UPDATE usuarios SET perfil_id = 10, perfil_nome = 'VISITANTE' WHERE perfil_id IS NULL OR perfil_id = 0";
$conn->query($sql2);

echo "\nUsuários atuais (verificação):\n";
$res2 = $conn->query("SELECT id, nome, perfil_id, perfil_nome, nivel_acesso FROM usuarios LIMIT 15");
while ($row2 = $res2->fetch_assoc()) {
    echo json_encode($row2) . "\n";
}
