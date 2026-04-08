<?php
require 'conexao.php';
require 'config.php';

$pwd = password_hash('senha123', PASSWORD_DEFAULT);
$month = date('m');

$users = [
    [
        'nome' => 'Super Rodrigo (Master)',
        'email' => 'master@teste.com',
        'senha' => $pwd,
        'sexo' => 'M',
        'data_nascimento' => date("Y-$month-10"),
        'perfil_id' => 1, // MASTER
        'nivel_acesso' => 0,
        'paroquia_id' => 1
    ],
    [
        'nome' => 'Padre Jonas (Acesso Adm)',
        'email' => 'padre@teste.com',
        'senha' => $pwd,
        'sexo' => 'M',
        'data_nascimento' => date("Y-$month-12"),
        'perfil_id' => 2, // ADMIN
        'nivel_acesso' => 1,
        'paroquia_id' => 1
    ],
    [
        'nome' => 'Isabela (Pascom - Sem Logs e Admin)',
        'email' => 'isabela@teste.com',
        'senha' => $pwd,
        'sexo' => 'F',
        'data_nascimento' => date("Y-$month-20"),
        'perfil_id' => 5, // PASCOM
        'nivel_acesso' => 2,
        'paroquia_id' => 1
    ],
    [
        'nome' => 'Visitante Lucas (Ver apenas)',
        'email' => 'visitante@teste.com',
        'senha' => $pwd,
        'sexo' => 'M',
        'data_nascimento' => date("Y-$month-28"),
        'perfil_id' => 9, // VISITANTE
        'nivel_acesso' => 3,
        'paroquia_id' => 1
    ]
];

foreach ($users as $u) {
    if ($conn->query("SELECT id FROM usuarios WHERE email = '{$u['email']}'")->num_rows > 0) {
        $conn->query("UPDATE usuarios SET perfil_id = {$u['perfil_id']}, nivel_acesso = {$u['nivel_acesso']} WHERE email = '{$u['email']}'");
        echo "Atualizado: {$u['nome']}\n";
        continue;
    }
    
    $sql = "INSERT INTO usuarios (nome, email, senha, sexo, data_nascimento, perfil_id, nivel_acesso, paroquia_id, ativo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssiii', $u['nome'], $u['email'], $u['senha'], $u['sexo'], $u['data_nascimento'], $u['perfil_id'], $u['nivel_acesso'], $u['paroquia_id']);
    
    if($stmt->execute()) {
        echo "Inserido: {$u['nome']}\n";
    } else {
        echo "Erro ao inserir {$u['nome']}: " . $stmt->error . "\n";
    }
}
?>
