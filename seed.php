<?php
require 'conexao.php';
require 'config.php';

// Seed Users
$pwd = password_hash('123456', PASSWORD_DEFAULT);
$month = date('m');

$users = [
    [
        'nome' => 'Ana Beatriz (Teste Gerente)',
        'email' => 'ana.gerente@teste.com',
        'senha' => $pwd,
        'sexo' => 'F',
        'data_nascimento' => date("Y-$month-15"),
        'nivel_acesso' => 2,
        'paroquia_id' => 1
    ],
    [
        'nome' => 'João Costa (Teste Supervisor)',
        'email' => 'joao.sup@teste.com',
        'senha' => $pwd,
        'sexo' => 'M',
        'data_nascimento' => date("Y-$month-22"),
        'nivel_acesso' => 1,
        'paroquia_id' => 1
    ],
    [
        'nome' => 'Carlos Silva (Excluir)',
        'email' => 'carlos.del@teste.com',
        'senha' => $pwd,
        'sexo' => 'M',
        'data_nascimento' => date("Y-$month-05"),
        'nivel_acesso' => 3,
        'paroquia_id' => 1
    ]
];

$insertedIds = [];

foreach ($users as $u) {
    if ($conn->query("SELECT id FROM usuarios WHERE email = '{$u['email']}'")->num_rows > 0) continue;
    
    $sql = "INSERT INTO usuarios (nome, email, senha, sexo, data_nascimento, nivel_acesso, paroquia_id, ativo) VALUES (?, ?, ?, ?, ?, ?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssii', $u['nome'], $u['email'], $u['senha'], $u['sexo'], $u['data_nascimento'], $u['nivel_acesso'], $u['paroquia_id']);
    
    if($stmt->execute()) {
        $id = $conn->insert_id;
        $insertedIds[] = $id;
        $_SESSION['usuario_id'] = 1; // mock session for logs
        logAction($conn, 'REGISTRAR_USUARIO', 'usuarios', $id, ['novo' => $u]);
        echo "Inserido: {$u['nome']}\n";
    }
}

// Test Toggle Ativo on Carlos Silva
if (count($insertedIds) > 0) {
    $uid = $insertedIds[2]; // Carlos
    $oldResult = $conn->query("SELECT * FROM usuarios WHERE id = $uid");
    $oldState = $oldResult->fetch_assoc();
    
    $conn->query("UPDATE usuarios SET ativo = 0 WHERE id = $uid");
    
    $newResult = $conn->query("SELECT * FROM usuarios WHERE id = $uid");
    $newState = $newResult->fetch_assoc();
    
    logAction($conn, 'ALTERAR_STATUS_USUARIO', 'usuarios', $uid, ['antigo' => $oldState, 'novo' => $newState]);
    echo "Carlos Silva desativado e logado com sucesso.\n";
}

?>
