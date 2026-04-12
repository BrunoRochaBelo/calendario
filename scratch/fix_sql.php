<?php
$sourcePath = 'C:\Users\Meu Computador\Downloads\u596929139_calen (2).sql';
$destPath = 'c:\xampp\htdocs\calender\u596929139_calen_final_hosting.sql';

if (!file_exists($sourcePath)) {
    die("Arquivo fonte não encontrado: $sourcePath");
}

$content = file_get_contents($sourcePath);

// 1. Padronizar todas as colações para utf8mb4_unicode_ci
$content = str_replace('utf8mb4_general_ci', 'utf8mb4_unicode_ci', $content);
$content = str_replace('utf8mb4_unicode_ci', 'utf8mb4_unicode_ci', $content); // prevent double replace issues if needed

// 2. Preparar bloco da tabela perfis
$perfisSql = "
-- --------------------------------------------------------
-- Estrutura para tabela `perfis`
--
CREATE TABLE `perfis` (
  `id` int(10) UNSIGNED NOT NULL,
  `paroquia_id` int(10) UNSIGNED DEFAULT NULL,
  `nome_perfil` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `perm_ver_calendario` tinyint(1) DEFAULT 1,
  `perm_criar_eventos` tinyint(1) DEFAULT 0,
  `perm_editar_eventos` tinyint(1) DEFAULT 0,
  `perm_excluir_eventos` tinyint(1) DEFAULT 0,
  `perm_ver_restritos` tinyint(1) DEFAULT 0,
  `perm_admin_usuarios` tinyint(1) DEFAULT 0,
  `perm_admin_sistema` tinyint(1) DEFAULT 0,
  `perm_ver_logs` tinyint(1) DEFAULT 0,
  `perm_cadastrar_usuario` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `perfis`
--
INSERT INTO `perfis` (`id`, `paroquia_id`, `nome_perfil`, `descricao`, `perm_ver_calendario`, `perm_criar_eventos`, `perm_editar_eventos`, `perm_excluir_eventos`, `perm_ver_restritos`, `perm_admin_usuarios`, `perm_admin_sistema`, `perm_ver_logs`, `perm_cadastrar_usuario`) VALUES
(2, 2, 'ADMINISTRADOR PAROQUIAL', NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(3, 2, 'VIGÁRIO', '', 1, 0, 0, 0, 0, 0, 0, 0, 0),
(4, 2, 'DIACONO', '', 1, 0, 0, 0, 0, 0, 0, 0, 0),
(5, 2, 'SECRETARIA', '', 1, 1, 1, 1, 1, 0, 0, 0, 0),
(6, 2, 'PASCOM ADM', '', 1, 1, 1, 1, 0, 0, 0, 0, 1),
(7, 2, 'PASCOM AGENTE', '', 1, 1, 0, 0, 0, 0, 0, 0, 0),
(8, 2, 'PASCOM AGENTE 2', '', 1, 0, 0, 0, 0, 0, 0, 0, 0),
(9, 2, 'CORDENADOR PASTORAL', '', 1, 0, 0, 0, 0, 0, 0, 0, 0),
(10, 2, 'FIEL DA IGREJA', NULL, 1, 1, 1, 1, 1, 0, 0, 0, 1),
(11, 2, 'VISITANTE', '', 1, 0, 0, 0, 0, 0, 0, 0, 0);

ALTER TABLE `perfis` ADD PRIMARY KEY (`id`);
ALTER TABLE `perfis` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
";

// 3. Inserir perfis logo após o START TRANSACTION ou antes de criar tabelas
$pos = strpos($content, 'CREATE TABLE');
if ($pos !== false) {
    $content = substr($content, 0, $pos) . $perfisSql . substr($content, $pos);
} else {
    $content .= $perfisSql;
}

if (file_put_contents($destPath, $content)) {
    echo "Sucesso: Arquivo gerado em $destPath\n";
} else {
    echo "Erro ao salvar arquivo.\n";
}
