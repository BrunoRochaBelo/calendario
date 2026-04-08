-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 08/04/2026 às 01:49
-- Versão do servidor: 11.8.6-MariaDB-log
-- Versão do PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `u596929139_calen`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `atividades`
--

CREATE TABLE `atividades` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `data_inicio` date NOT NULL,
  `hora_inicio` time DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `hora_fim` time DEFAULT NULL,
  `local_id` int(10) UNSIGNED DEFAULT NULL,
  `tipo_atividade_id` int(10) UNSIGNED DEFAULT NULL,
  `categoria_id` int(10) UNSIGNED DEFAULT NULL,
  `criador_id` int(10) UNSIGNED DEFAULT NULL,
  `paroquia_id` int(10) UNSIGNED DEFAULT NULL,
  `status` varchar(50) DEFAULT 'ativo',
  `data_criacao` timestamp NULL DEFAULT current_timestamp(),
  `ultima_atualizacao` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `atividades`
--

INSERT INTO `atividades` (`id`, `nome`, `descricao`, `data_inicio`, `hora_inicio`, `data_fim`, `hora_fim`, `local_id`, `tipo_atividade_id`, `categoria_id`, `criador_id`, `paroquia_id`, `status`, `data_criacao`, `ultima_atualizacao`) VALUES
(1, 'Missa Dominical', 'Missa com a comunidade', '2026-03-15', '08:00:00', '2026-03-15', '09:30:00', 1, 1, NULL, 1, 1, 'ativo', '2026-04-07 19:28:21', '2026-04-07 19:28:21'),
(2, 'Reunião PASCOM', 'Planejamento de comunicação', '2026-03-16', '20:00:00', '2026-03-16', '21:30:00', 2, 4, NULL, 1, 1, 'ativo', '2026-04-07 19:28:21', '2026-04-07 19:28:21'),
(3, 'Catequese Infantil', 'Encontro semanal', '2026-03-17', '18:00:00', '2026-03-17', '19:30:00', 3, 3, NULL, 1, 1, 'ativo', '2026-04-07 19:28:21', '2026-04-07 19:28:21');

-- --------------------------------------------------------

--
-- Estrutura para tabela `locais_paroquia`
--

CREATE TABLE `locais_paroquia` (
  `id` int(10) UNSIGNED NOT NULL,
  `paroquia_id` int(10) UNSIGNED NOT NULL,
  `nome_local` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `locais_paroquia`
--

INSERT INTO `locais_paroquia` (`id`, `paroquia_id`, `nome_local`, `descricao`, `ativo`) VALUES
(1, 1, 'Igreja Matriz', NULL, 1),
(2, 1, 'Salão Paroquial', NULL, 1),
(3, 1, 'Capela São José', NULL, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `log_alteracoes`
--

CREATE TABLE `log_alteracoes` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `acao` varchar(255) NOT NULL,
  `tabela_afetada` varchar(100) DEFAULT NULL,
  `registro_id` int(10) UNSIGNED DEFAULT NULL,
  `detalhes_alteracao` text DEFAULT NULL,
  `paroquia_id` int(10) UNSIGNED DEFAULT NULL,
  `ip_origem` varchar(45) DEFAULT NULL,
  `data_hora` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `log_alteracoes`
--

INSERT INTO `log_alteracoes` (`id`, `usuario_id`, `acao`, `tabela_afetada`, `registro_id`, `detalhes_alteracao`, `paroquia_id`, `ip_origem`, `data_hora`) VALUES
(1, 1, 'LOGIN', 'usuarios', 1, NULL, NULL, '45.164.11.240', '2026-03-12 01:49:37'),
(2, 3, 'LOGIN', 'usuarios', 3, NULL, NULL, '45.164.11.240', '2026-03-12 02:35:55'),
(3, NULL, 'RESET_SENHA', 'usuarios', 1, 'Senha redefinida via fluxo de recuperação', NULL, '45.164.11.220', '2026-04-07 21:20:21'),
(4, 1, 'LOGIN', 'usuarios', 1, 'Autenticação bem-sucedida', 1, '45.164.11.220', '2026-04-07 21:20:33'),
(5, 1, 'LOGIN', 'usuarios', 1, 'Autenticação bem-sucedida', 1, '45.164.11.220', '2026-04-08 01:36:26');

-- --------------------------------------------------------

--
-- Estrutura para tabela `paroquias`
--

CREATE TABLE `paroquias` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` char(2) DEFAULT NULL,
  `diocese` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `data_criacao` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `paroquias`
--

INSERT INTO `paroquias` (`id`, `nome`, `cidade`, `estado`, `diocese`, `ativo`, `data_criacao`) VALUES
(1, 'Paróquia São José', 'São Paulo', 'SP', 'Arquidiocese de São Paulo', 1, '2026-03-12 01:37:29');

-- --------------------------------------------------------

--
-- Estrutura para tabela `perfis`
--

CREATE TABLE `perfis` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  `nivel_hierarquia` int(11) DEFAULT 0,
  `perm_ver_calendario` tinyint(1) DEFAULT 1,
  `perm_criar_eventos` tinyint(1) DEFAULT 0,
  `perm_editar_eventos` tinyint(1) DEFAULT 0,
  `perm_excluir_eventos` tinyint(1) DEFAULT 0,
  `perm_ver_restritos` tinyint(1) DEFAULT 0,
  `perm_admin_usuarios` tinyint(1) DEFAULT 0,
  `perm_admin_sistema` tinyint(1) DEFAULT 0,
  `perm_ver_logs` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `perfis`
--

INSERT INTO `perfis` (`id`, `nome`, `descricao`, `nivel_hierarquia`, `perm_ver_calendario`, `perm_criar_eventos`, `perm_editar_eventos`, `perm_excluir_eventos`, `perm_ver_restritos`, `perm_admin_usuarios`, `perm_admin_sistema`, `perm_ver_logs`) VALUES
(1, 'MASTER', NULL, 100, 1, 1, 1, 1, 1, 1, 1, 1),
(2, 'ADMINISTRADOR PAROQUIAL', NULL, 80, 1, 1, 1, 0, 1, 1, 1, 1),
(3, 'SECRETARIA', NULL, 60, 1, 1, 1, 0, 1, 0, 1, 0),
(4, 'PADRE', NULL, 70, 1, 1, 1, 1, 1, 0, 0, 1),
(5, 'PASCOM', NULL, 50, 1, 0, 0, 0, 0, 0, 1, 0),
(6, 'COORDENADOR PASTORAL', NULL, 40, 1, 1, 0, 0, 0, 0, 0, 0),
(7, 'VISITANTE', NULL, 0, 1, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos_atividade`
--

CREATE TABLE `tipos_atividade` (
  `id` int(10) UNSIGNED NOT NULL,
  `paroquia_id` int(10) UNSIGNED DEFAULT NULL,
  `nome_tipo` varchar(100) NOT NULL,
  `cor` varchar(7) DEFAULT NULL,
  `icone` varchar(50) DEFAULT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tipos_atividade`
--

INSERT INTO `tipos_atividade` (`id`, `paroquia_id`, `nome_tipo`, `cor`, `icone`, `descricao`) VALUES
(1, 1, 'Celebração', '#9333ea', 'cross', NULL),
(2, 1, 'Evento Social', '#db2777', 'users', NULL),
(3, 1, 'Formação', '#2563eb', 'book', NULL),
(4, 1, 'Reunião', '#059669', 'calendar', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil_id` int(10) UNSIGNED DEFAULT NULL,
  `paroquia_id` int(10) UNSIGNED DEFAULT NULL,
  `sexo` varchar(20) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `palavra_chave` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `perm_ver_calendario` tinyint(1) DEFAULT 1,
  `perm_criar_eventos` tinyint(1) DEFAULT 0,
  `perm_editar_eventos` tinyint(1) DEFAULT 0,
  `perm_excluir_eventos` tinyint(1) DEFAULT 0,
  `perm_ver_restritos` tinyint(1) DEFAULT 0,
  `perm_admin_usuarios` tinyint(1) DEFAULT 0,
  `perm_admin_sistema` tinyint(1) DEFAULT 0,
  `perm_ver_logs` tinyint(1) DEFAULT 0,
  `data_criacao` timestamp NULL DEFAULT current_timestamp(),
  `ultimo_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `perfil_id`, `paroquia_id`, `sexo`, `telefone`, `palavra_chave`, `ativo`, `perm_ver_calendario`, `perm_criar_eventos`, `perm_editar_eventos`, `perm_excluir_eventos`, `perm_ver_restritos`, `perm_admin_usuarios`, `perm_admin_sistema`, `perm_ver_logs`, `data_criacao`, `ultimo_login`) VALUES
(1, 'Administrador Master', 'admin@sistema.com', '$2y$10$ykZSUqkyU0HF/FmIB2eVLeiXYi9cJO6QxGJZgar/jstsqDMW0EaVG', 1, 1, NULL, NULL, 'PASCOM2026', 1, 1, 0, 0, 0, 0, 0, 0, 0, '2026-03-12 01:37:29', NULL),
(2, 'Pe. João Silva', 'padre@paroquia.com', '$2y$10$UvGKmJA4AHnSZR/67ufOHOFEKEFxZs/bVsrjKE7JJDfuukrzC8pOq', 4, 1, NULL, NULL, 'FE2026', 1, 1, 0, 0, 0, 0, 0, 0, 0, '2026-03-12 01:37:29', NULL),
(3, 'Maria Secretaria', 'secretaria@paroquia.com', '$2y$10$UvGKmJA4AHnSZR/67ufOHOFEKEFxZs/bVsrjKE7JJDfuukrzC8pOq', 3, 1, NULL, NULL, 'SERVICE2026', 1, 1, 0, 0, 0, 0, 0, 0, 0, '2026-03-12 01:37:29', NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `atividades`
--
ALTER TABLE `atividades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ev_local` (`local_id`),
  ADD KEY `fk_ev_tipo` (`tipo_atividade_id`),
  ADD KEY `fk_ev_usuario` (`criador_id`),
  ADD KEY `fk_ev_paroquia` (`paroquia_id`);

--
-- Índices de tabela `locais_paroquia`
--
ALTER TABLE `locais_paroquia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_local_paroquia` (`paroquia_id`);

--
-- Índices de tabela `log_alteracoes`
--
ALTER TABLE `log_alteracoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_log_usuario` (`usuario_id`);

--
-- Índices de tabela `paroquias`
--
ALTER TABLE `paroquias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `perfis`
--
ALTER TABLE `perfis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `tipos_atividade`
--
ALTER TABLE `tipos_atividade`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_usuario_perfil` (`perfil_id`),
  ADD KEY `fk_usuario_paroquia` (`paroquia_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `atividades`
--
ALTER TABLE `atividades`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `locais_paroquia`
--
ALTER TABLE `locais_paroquia`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `log_alteracoes`
--
ALTER TABLE `log_alteracoes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `paroquias`
--
ALTER TABLE `paroquias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `perfis`
--
ALTER TABLE `perfis`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `tipos_atividade`
--
ALTER TABLE `tipos_atividade`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `atividades`
--
ALTER TABLE `atividades`
  ADD CONSTRAINT `fk_ev_local` FOREIGN KEY (`local_id`) REFERENCES `locais_paroquia` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ev_paroquia` FOREIGN KEY (`paroquia_id`) REFERENCES `paroquias` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ev_tipo` FOREIGN KEY (`tipo_atividade_id`) REFERENCES `tipos_atividade` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ev_usuario` FOREIGN KEY (`criador_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `locais_paroquia`
--
ALTER TABLE `locais_paroquia`
  ADD CONSTRAINT `fk_local_paroquia` FOREIGN KEY (`paroquia_id`) REFERENCES `paroquias` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `log_alteracoes`
--
ALTER TABLE `log_alteracoes`
  ADD CONSTRAINT `fk_log_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_paroquia` FOREIGN KEY (`paroquia_id`) REFERENCES `paroquias` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_usuario_perfil` FOREIGN KEY (`perfil_id`) REFERENCES `perfis` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
