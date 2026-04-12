-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 12/04/2026 às 06:30
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
CREATE TABLE `atividades` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `cor` varchar(7) DEFAULT '#3b82f6',
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
  `restrito` tinyint(1) DEFAULT 0,
  `serie_key` varchar(100) DEFAULT NULL,
  `serie_frequencia` varchar(50) DEFAULT NULL,
  `serie_dias_semana` varchar(100) DEFAULT NULL,
  `serie_data_fim` date DEFAULT NULL,
  `data_criacao` timestamp NULL DEFAULT current_timestamp(),
  `ultima_atualizacao` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_multi_color` tinyint(1) DEFAULT 0,
  `is_flashing` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `atividades`
--

INSERT INTO `atividades` (`id`, `nome`, `descricao`, `cor`, `data_inicio`, `hora_inicio`, `data_fim`, `hora_fim`, `local_id`, `tipo_atividade_id`, `categoria_id`, `criador_id`, `paroquia_id`, `status`, `restrito`, `serie_key`, `serie_frequencia`, `serie_dias_semana`, `serie_data_fim`, `data_criacao`, `ultima_atualizacao`, `is_multi_color`, `is_flashing`) VALUES
(1, 'Festa da Divina Misericórdia', '', '#dc2626', '2026-04-12', '14:00:00', NULL, NULL, NULL, NULL, NULL, 2, 2, 'ativo', 0, NULL, NULL, NULL, NULL, '2026-04-11 19:14:07', '2026-04-11 19:14:07', 1, 0),
(2, 'Missa Capela São Severino', '', '#16a34a', '2026-04-12', '17:00:00', NULL, NULL, NULL, NULL, NULL, 2, 2, 'ativo', 0, NULL, NULL, NULL, NULL, '2026-04-11 19:15:03', '2026-04-11 19:15:03', 1, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `atividades_catalogo`
--

CREATE TABLE `atividades_catalogo` (
  `id` int(10) UNSIGNED NOT NULL,
  `paroquia_id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(150) NOT NULL,
  `descricao` text DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `atividades_catalogo`
--

INSERT INTO `atividades_catalogo` (`id`, `paroquia_id`, `nome`, `descricao`, `ativo`) VALUES
(2, 1, 'Feed', 'Leitores e proclamadores', 1),
(3, 1, 'Transmissão Instagram', 'Equipe de música e canto', 1),
(4, 1, 'Transmissão Youtube', 'Recepção e apoio aos participantes', 1),
(5, 1, 'Telão Projetor', 'Cobertura, avisos e apoio da PASCOM', 1),
(6, 1, 'Criação de Artes', 'Organização litúrgica do evento', 1),
(7, 1, 'Edição de Artes', 'Leitores e proclamadores', 1),
(8, 1, 'Publicar Artes Story', 'Equipe de música e canto', 1),
(12, 2, 'Feed', 'Leitores e proclamadores', 1),
(13, 2, 'Transmissão Instagram', 'Equipe de música e canto', 1),
(15, 2, 'Telão Projetor', 'Cobertura, avisos e apoio da PASCOM', 1),
(16, 2, 'Criação de Artes', 'Organização litúrgica', 1),
(18, 2, 'Publicar Artes Story', 'Equipe de música e canto', 1),
(19, 2, 'Publicar Arte Informes', 'Recepção e apoio aos participantes', 1),
(21, 2, 'Santa Missa dominical', 'Santa Missa', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `atividade_evento_inscricoes`
--

CREATE TABLE `atividade_evento_inscricoes` (
  `id` int(10) UNSIGNED NOT NULL,
  `evento_item_id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `data_inscricao` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `atividade_evento_inscricoes`
--

INSERT INTO `atividade_evento_inscricoes` (`id`, `evento_item_id`, `usuario_id`, `data_inscricao`) VALUES
(1, 1, 4, '2026-04-11 23:12:58');

-- --------------------------------------------------------

--
-- Estrutura para tabela `atividade_evento_itens`
--

CREATE TABLE `atividade_evento_itens` (
  `id` int(10) UNSIGNED NOT NULL,
  `evento_id` int(10) UNSIGNED NOT NULL,
  `atividade_catalogo_id` int(10) UNSIGNED NOT NULL,
  `ordem` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `data_criacao` timestamp NULL DEFAULT current_timestamp(),
  `ultima_atualizacao` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `atividade_evento_itens`
--

INSERT INTO `atividade_evento_itens` (`id`, `evento_id`, `atividade_catalogo_id`, `ordem`, `data_criacao`, `ultima_atualizacao`) VALUES
(1, 1, 18, 1, '2026-04-11 22:55:18', '2026-04-11 22:55:18');

-- --------------------------------------------------------

--
-- Estrutura para tabela `atividade_grupos`
--

CREATE TABLE `atividade_grupos` (
  `atividade_id` int(10) UNSIGNED NOT NULL,
  `grupo_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `auth_throttle`
--

CREATE TABLE `auth_throttle` (
  `id` int(10) UNSIGNED NOT NULL,
  `scope` varchar(50) NOT NULL,
  `identifier` varchar(191) NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `last_attempt_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `auth_throttle`
--

INSERT INTO `auth_throttle` (`id`, `scope`, `identifier`, `attempts`, `locked_until`, `last_attempt_at`, `created_at`, `updated_at`) VALUES
(1, 'login', 'login|rangelajc1@gmail.com|45.164.11.220', 3, '2026-04-11 20:02:10', '2026-04-11 22:57:10', '2026-04-11 22:56:14', '2026-04-11 22:57:10'),
(2, 'recovery', 'recovery|rangelsjc1@gmwil.com|45.164.11.220', 1, NULL, '2026-04-11 23:07:53', '2026-04-11 23:07:53', '2026-04-11 23:07:53'),
(5, 'recovery', 'recovery|sistema@pascom.com|45.164.11.220', 1, NULL, '2026-04-11 23:15:26', '2026-04-11 23:15:26', '2026-04-11 23:15:26'),
(6, 'login', 'login|pesergio@gmail.com|45.164.11.220', 1, NULL, '2026-04-11 23:54:13', '2026-04-11 23:54:13', '2026-04-11 23:54:13'),
(7, 'login', 'login|admin@pascom.local|45.164.11.220', 1, NULL, '2026-04-12 00:08:15', '2026-04-12 00:08:15', '2026-04-12 00:08:15'),
(8, 'login', 'login|admin@gmail.com|45.164.11.220', 1, NULL, '2026-04-12 00:08:56', '2026-04-12 00:08:56', '2026-04-12 00:08:56'),
(9, 'login', 'login|ramgelsjc1@gmail.com|45.164.11.220', 1, NULL, '2026-04-12 04:04:06', '2026-04-12 04:04:06', '2026-04-12 04:04:06');

-- --------------------------------------------------------

--
-- Estrutura para tabela `grupos_trabalho`
--

CREATE TABLE `grupos_trabalho` (
  `id` int(10) UNSIGNED NOT NULL,
  `paroquia_id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `cor` varchar(7) DEFAULT '#3b82f6',
  `visivel` tinyint(1) DEFAULT 1,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `grupos_trabalho`
--

INSERT INTO `grupos_trabalho` (`id`, `paroquia_id`, `nome`, `descricao`, `cor`, `visivel`, `ativo`) VALUES
(1, 2, 'Quermesse', 'Grupo padrão para novos cadastros (Sala de Espera)', '#165bbb', 1, 1),
(2, 2, 'Pascom Artes', '0', '#3bf78c', 1, 1),
(3, 2, 'Pascom Transmissão', 'Pascom Transmissão', '#043920', 1, 1),
(4, 2, 'Pároco / Secretaria', '0', '#1a24ad', 1, 1),
(5, 1, 'Pascom Youtube', 'Grupo padrão para novos cadastros (Sala de Espera)', '#94a3b8', 1, 0),
(6, 2, 'Acolhida', 'Grupo padrão para novos cadastros (Sala de Espera)', '#2b8619', 1, 1),
(7, 2, 'Todos', 'Grupo padrão — todos os membros da paróquia', '#94a3b8', 1, 1),
(8, 2, 'Jornal da Imaculada', '0', '#61c3db', 1, 1),
(9, 1, 'Todos', 'Grupo padrão — todos os membros da paróquia', '#3b82f6', 1, 1),
(10, 1, 'Acolhida', 'acolhida', '#053685', 1, 1),
(11, 2, 'Storys', 'O agente irá fazer os registros o postr no Strorys da Paróquia ou na Capela', '#f7b23b', 1, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `inscricoes`
--

CREATE TABLE `inscricoes` (
  `id` int(10) UNSIGNED NOT NULL,
  `atividade_id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `data_inscricao` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `locais_paroquia`
--

CREATE TABLE `locais_paroquia` (
  `id` int(10) UNSIGNED NOT NULL,
  `paroquia_id` int(10) UNSIGNED NOT NULL,
  `nome_local` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `endereco` varchar(255) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `responsavel` varchar(100) DEFAULT NULL,
  `capacidade` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `locais_paroquia`
--

INSERT INTO `locais_paroquia` (`id`, `paroquia_id`, `nome_local`, `descricao`, `ativo`, `endereco`, `telefone`, `responsavel`, `capacidade`) VALUES
(1, 1, 'Basilica de São Pedro', NULL, 1, '0', '0', 'Padre Sérgio Muniz', 0),
(3, 2, 'Capela São Antônio', NULL, 1, '0', '0', 'Padre Sérgio Muniz', 0),
(4, 2, 'Matriz Nossa Senhora da Conceição', NULL, 1, 'Rua Virgílio Mârques, 84', '(81) 3788-0812', 'Padre Sérgio Muniz', 1000),
(5, 2, 'Capela São Severino Mártir', NULL, 1, 'Estrada do Caiara, 415 - Iputinga', '0', 'Padre Sérgio Muniz', 500),
(6, 2, 'Capela São João Batista', NULL, 1, 'R. Rezende, 40 - Iputinga,', '0', 'Padre Sérgio Muniz', 500),
(7, 2, 'Capela Santa Marta', NULL, 1, '2A Travessa Sucupira - Iputinga', '0', 'Padre Sérgio Muniz', 400);

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
(1, 2, 'CRIAR_ATIVIDADE', 'atividades', 1, 'Festa da Divina Misericórdia', 2, '201.18.97.139', '2026-04-11 19:14:07'),
(2, 2, 'CRIAR_ATIVIDADE', 'atividades', 2, 'Missa Capela São Severino', 2, '201.18.97.139', '2026-04-11 19:15:03'),
(3, 2, 'INSCREVER_ATIVIDADE', 'inscricoes', 2, '{\n    \"atividade_id\": 2,\n    \"evento_item_id\": null,\n    \"usuario_id\": 2\n}', 2, '201.18.97.139', '2026-04-11 19:15:08'),
(4, 3, 'LOGIN', 'usuarios', 3, 'Autenticacao bem-sucedida', 2, '45.164.11.220', '2026-04-11 22:54:17'),
(5, 3, 'LOGOUT', 'usuarios', 3, 'Sessão encerrada pelo usuário', 2, '45.164.11.220', '2026-04-11 22:55:43'),
(6, NULL, 'RESET_SENHA', 'usuarios', 4, 'Senha redefinida via fluxo de recuperacao (palavra-chave removida)', NULL, '45.164.11.220', '2026-04-11 23:11:18'),
(7, 4, 'LOGIN', 'usuarios', 4, 'Autenticacao bem-sucedida', 2, '45.164.11.220', '2026-04-11 23:12:26'),
(8, 4, 'INSCREVER_ATIVIDADE_EVENTO', 'atividade_evento_inscricoes', 1, '{\n    \"atividade_id\": 1,\n    \"evento_item_id\": 1,\n    \"usuario_id\": 4\n}', 2, '45.164.11.220', '2026-04-11 23:12:58'),
(9, 4, 'LOGOUT', 'usuarios', 4, 'Sessão encerrada pelo usuário', 2, '45.164.11.220', '2026-04-11 23:13:14'),
(10, 2, 'LOGIN', 'usuarios', 2, 'Autenticacao bem-sucedida', 2, '45.164.11.220', '2026-04-11 23:13:41'),
(11, 2, 'CANCELAR_INSCRICAO_ATIVIDADE', 'inscricoes', 2, '{\n    \"atividade_id\": 2,\n    \"evento_item_id\": null,\n    \"usuario_id\": 2\n}', 2, '45.164.11.220', '2026-04-11 23:13:51'),
(12, NULL, 'RESET_SENHA', 'usuarios', 1, 'Senha redefinida via fluxo de recuperacao (palavra-chave removida)', NULL, '45.164.11.220', '2026-04-11 23:16:26'),
(13, NULL, 'RESET_SENHA', 'usuarios', 1, 'Senha redefinida via fluxo de recuperacao (palavra-chave removida)', NULL, '45.164.11.220', '2026-04-11 23:55:04'),
(14, 2, 'LOGOUT', 'usuarios', 2, 'Sessão encerrada pelo usuário', 2, '45.164.11.220', '2026-04-11 23:55:55'),
(15, 1, 'LOGIN', 'usuarios', 1, 'Autenticacao bem-sucedida', 2, '45.164.11.220', '2026-04-11 23:56:16'),
(16, 1, 'LOGIN', 'usuarios', 1, 'Autenticacao bem-sucedida', 2, '45.164.11.220', '2026-04-11 23:58:38'),
(17, NULL, 'RESET_SENHA', 'usuarios', 4, 'Senha redefinida via fluxo de recuperacao (palavra-chave removida)', NULL, '45.164.11.220', '2026-04-12 00:10:14'),
(18, 4, 'LOGIN', 'usuarios', 4, 'Autenticacao bem-sucedida', 2, '45.164.11.220', '2026-04-12 00:11:17'),
(19, 1, 'EDITAR_USUARIO', 'usuarios', 4, '{\n    \"antigo\": {\n        \"id\": \"4\",\n        \"nome\": \"Rangel Silva\",\n        \"email\": \"rangelsjc1@gmail.com\",\n        \"senha\": \"$2y$12$S7DZQdfwQnphNLHcAQucsOztsP0vrNJvqqVeh73JW7M6nuvRGLVmG\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98146-1663\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"1\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"1\",\n        \"data_criacao\": \"2026-04-08 16:48:03\",\n        \"ultimo_login\": \"2026-04-12 00:11:17\",\n        \"data_nascimento\": \"1983-07-18\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_10_b08392877579.jpeg\",\n        \"nivel_acesso\": \"4\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    },\n    \"novo\": {\n        \"id\": \"4\",\n        \"nome\": \"Rangel Silva\",\n        \"email\": \"rangelsjc1@gmail.com\",\n        \"senha\": \"$2y$12$S7DZQdfwQnphNLHcAQucsOztsP0vrNJvqqVeh73JW7M6nuvRGLVmG\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98146-1663\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"1\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"1\",\n        \"data_criacao\": \"2026-04-08 16:48:03\",\n        \"ultimo_login\": \"2026-04-12 00:11:17\",\n        \"data_nascimento\": \"1983-07-18\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_10_b08392877579.jpeg\",\n        \"nivel_acesso\": \"4\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:15:16'),
(20, 1, 'EDITAR_USUARIO', 'usuarios', 7, '{\n    \"antigo\": {\n        \"id\": \"7\",\n        \"nome\": \"Amanda Leal\",\n        \"email\": \"amandapeixoto87@gmail.com\",\n        \"senha\": \"$2y$10$i3YrXwlRKxww3QvuBU1QO.KoBQCBnIbC4WxHpfZQuzecZ1HAyoXdC\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99858-6006\",\n        \"palavra_chave\": \"PIO X\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 05:11:32\",\n        \"ultimo_login\": \"2026-04-11 01:59:07\",\n        \"data_nascimento\": \"1987-03-11\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_7_9988b8ae3614.jpeg\",\n        \"nivel_acesso\": \"4\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    },\n    \"novo\": {\n        \"id\": \"7\",\n        \"nome\": \"Amanda Leal\",\n        \"email\": \"amandapeixoto87@gmail.com\",\n        \"senha\": \"$2y$10$i3YrXwlRKxww3QvuBU1QO.KoBQCBnIbC4WxHpfZQuzecZ1HAyoXdC\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99858-6006\",\n        \"palavra_chave\": \"PIO X\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 05:11:32\",\n        \"ultimo_login\": \"2026-04-11 01:59:07\",\n        \"data_nascimento\": \"1987-03-11\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_7_9988b8ae3614.jpeg\",\n        \"nivel_acesso\": \"4\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:15:54'),
(21, 1, 'EDITAR_USUARIO', 'usuarios', 6, '{\n    \"antigo\": {\n        \"id\": \"6\",\n        \"nome\": \"Maria Eduarda\",\n        \"email\": \"mariaeduarda@gmail.com\",\n        \"senha\": \"$2y$10$v2RYQM5fmXBWU5Gjyh0GXuDintU6BDDf4f6JCdQWQjN16BUs2Wdti\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81 ) 98362-5306\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 05:11:32\",\n        \"ultimo_login\": \"2026-04-11 00:38:09\",\n        \"data_nascimento\": null,\n        \"foto_perfil\": \"img\\/usuarios\\/user_6_b7012fef1728.jpeg\",\n        \"nivel_acesso\": \"5\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"6\",\n        \"nome\": \"Maria Eduarda\",\n        \"email\": \"dudafloriano2514@gmail.com\",\n        \"senha\": \"$2y$10$v2RYQM5fmXBWU5Gjyh0GXuDintU6BDDf4f6JCdQWQjN16BUs2Wdti\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81 ) 98362-5306\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 05:11:32\",\n        \"ultimo_login\": \"2026-04-11 00:38:09\",\n        \"data_nascimento\": \"2003-09-22\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_6_b7012fef1728.jpeg\",\n        \"nivel_acesso\": \"5\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:18:17'),
(22, 1, 'EDITAR_USUARIO', 'usuarios', 12, '{\n    \"antigo\": {\n        \"id\": \"12\",\n        \"nome\": \"Alif Victória Alves de Lima\",\n        \"email\": \"vitoriaalif@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 98371-1185\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 02:31:59\",\n        \"ultimo_login\": \"2026-04-09 03:36:13\",\n        \"data_nascimento\": \"2009-01-05\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"12\",\n        \"nome\": \"Alif Victória Alves de Lima\",\n        \"email\": \"vitoriaalif@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 98371-1185\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 02:31:59\",\n        \"ultimo_login\": \"2026-04-09 03:36:13\",\n        \"data_nascimento\": \"2009-01-05\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:19:33'),
(23, 1, 'EDITAR_USUARIO', 'usuarios', 3, '{\n    \"antigo\": {\n        \"id\": \"3\",\n        \"nome\": \"Ana Carla de Melo\",\n        \"email\": \"secretariaparoquialiputinga@gmail.com\",\n        \"senha\": \"$2y$12$WNvykg6E.xrpeehYTlhp.OvQYa8zNmIgiQ9fhjABBzvv5ip8OGKiG\",\n        \"perfil_id\": \"3\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"\",\n        \"palavra_chave\": \"PIO X\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"1\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-03-12 01:37:29\",\n        \"ultimo_login\": \"2026-04-11 22:54:17\",\n        \"data_nascimento\": \"1980-11-28\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_3_439e68657d6a.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"SECRETARIA\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"3\",\n        \"nome\": \"Ana Carla de Melo\",\n        \"email\": \"secretariaparoquialiputinga@gmail.com\",\n        \"senha\": \"$2y$12$WNvykg6E.xrpeehYTlhp.OvQYa8zNmIgiQ9fhjABBzvv5ip8OGKiG\",\n        \"perfil_id\": \"3\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 99593-9042\",\n        \"palavra_chave\": \"PIO X\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"1\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-03-12 01:37:29\",\n        \"ultimo_login\": \"2026-04-11 22:54:17\",\n        \"data_nascimento\": \"1980-11-28\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_3_03b84e37e9a3.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"SECRETARIA\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:20:28'),
(24, 1, 'EDITAR_USUARIO', 'usuarios', 2, '{\n    \"antigo\": {\n        \"id\": \"2\",\n        \"nome\": \"Pe. Sérgio Muniz\",\n        \"email\": \"pesergio@gmail.com\",\n        \"senha\": \"$2y$10$wRo8Qwh6jwHxTbTYfF3bJu068nLyv\\/5BIcRpC33pBlx.i\\/wnNUamq\",\n        \"perfil_id\": \"2\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99615-8138\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"1\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-03-12 01:37:29\",\n        \"ultimo_login\": \"2026-04-11 23:13:41\",\n        \"data_nascimento\": \"1988-12-14\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_2_84276bc024c4.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"ADMINISTRADOR PAROQUIAL\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"2\",\n        \"nome\": \"Pe. Sérgio Muniz\",\n        \"email\": \"pesergio@gmail.com\",\n        \"senha\": \"$2y$10$wRo8Qwh6jwHxTbTYfF3bJu068nLyv\\/5BIcRpC33pBlx.i\\/wnNUamq\",\n        \"perfil_id\": \"2\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99615-8138\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"1\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-03-12 01:37:29\",\n        \"ultimo_login\": \"2026-04-11 23:13:41\",\n        \"data_nascimento\": \"1988-12-14\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_2_20b9f93ed98a.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"ADMINISTRADOR PAROQUIAL\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:21:06'),
(25, 1, 'EXCLUIR_USUARIO', 'usuarios', 16, '{\n    \"id\": \"16\",\n    \"nome\": \"Cristiane Silva Serejo\",\n    \"email\": \"cristianeserejo797@gmail.com\",\n    \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n    \"perfil_id\": \"7\",\n    \"paroquia_id\": \"2\",\n    \"sexo\": \"F\",\n    \"telefone\": \"(81) 99929-6896\",\n    \"palavra_chave\": null,\n    \"ativo\": \"1\",\n    \"perm_criar_eventos\": \"0\",\n    \"perm_editar_eventos\": \"0\",\n    \"perm_excluir_eventos\": \"0\",\n    \"perm_ver_restritos\": \"0\",\n    \"perm_cadastrar_usuario\": \"0\",\n    \"perm_admin_usuarios\": \"0\",\n    \"perm_ver_logs\": \"0\",\n    \"data_criacao\": \"2026-04-09 02:31:59\",\n    \"ultimo_login\": null,\n    \"data_nascimento\": \"1972-05-03\",\n    \"foto_perfil\": null,\n    \"nivel_acesso\": \"3\",\n    \"perfil_nome\": \"PASCOM AGENTE 2\",\n    \"perm_ver_calendario\": \"1\",\n    \"perm_admin_sistema\": \"0\",\n    \"perm_gerenciar_catalogo\": \"0\",\n    \"perm_gerenciar_grupos\": \"0\"\n}', 2, '45.164.11.220', '2026-04-12 00:22:35'),
(26, 1, 'EDITAR_USUARIO', 'usuarios', 18, '{\n    \"antigo\": {\n        \"id\": \"18\",\n        \"nome\": \"Danilo da Silva Medeiros\",\n        \"email\": \"danilosilvamedeiros19@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99381-2347\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 02:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2008-06-19\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"18\",\n        \"nome\": \"Danilo da Silva Medeiros\",\n        \"email\": \"danilosilvamedeiros19@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99381-2347\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 02:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2008-06-19\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:23:19'),
(27, 1, 'EDITAR_USUARIO', 'usuarios', 21, '{\n    \"antigo\": {\n        \"id\": \"21\",\n        \"nome\": \"Danilo José de Bonfim de Brito\",\n        \"email\": \"danilojosebomfim14@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98311-4355\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 02:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2004-09-04\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"21\",\n        \"nome\": \"Danilo José de Bonfim de Brito\",\n        \"email\": \"danilojosebomfim14@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98311-4355\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 02:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2004-09-04\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:23:49'),
(28, 1, 'EDITAR_PAROQUIA', 'paroquias', 2, '{\n    \"antigo\": {\n        \"id\": \"2\",\n        \"nome\": \"Paróquia Nossa Senhora da Conceição\",\n        \"cidade\": \"Pernambuco\",\n        \"estado\": \"PE\",\n        \"diocese\": \"AOR\",\n        \"ativo\": \"1\",\n        \"data_criacao\": \"2026-04-08 05:33:29\"\n    },\n    \"novo\": {\n        \"nome\": \"Nossa Senhora da Conceição\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:24:02'),
(29, 1, 'EDITAR_USUARIO', 'usuarios', 11, '{\n    \"antigo\": {\n        \"id\": \"11\",\n        \"nome\": \"Danusa Maria Silva do Nascimento\",\n        \"email\": \"danusa@gmail.com\",\n        \"senha\": \"$2y$10$lqu1oJFDZxEeGcDwpSZUi.CPOjCBS9Ywxf6INPq9bGY6gFZsoSXxm\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 97317-3773\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 01:52:03\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2011-05-11\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"6\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"11\",\n        \"nome\": \"Danusa Maria Silva do Nascimento\",\n        \"email\": \"docinhocida@hotmail.com\",\n        \"senha\": \"$2y$10$lqu1oJFDZxEeGcDwpSZUi.CPOjCBS9Ywxf6INPq9bGY6gFZsoSXxm\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 97317-3773\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 01:52:03\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2011-05-11\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"6\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:25:14'),
(30, 1, 'EDITAR_USUARIO', 'usuarios', 11, '{\n    \"antigo\": {\n        \"id\": \"11\",\n        \"nome\": \"Danusa Maria Silva do Nascimento\",\n        \"email\": \"docinhocida@hotmail.com\",\n        \"senha\": \"$2y$10$lqu1oJFDZxEeGcDwpSZUi.CPOjCBS9Ywxf6INPq9bGY6gFZsoSXxm\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 97317-3773\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 01:52:03\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2011-05-11\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"6\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"11\",\n        \"nome\": \"Danusa Maria Silva do Nascimento\",\n        \"email\": \"docinhocida@hotmail.com\",\n        \"senha\": \"$2y$10$lqu1oJFDZxEeGcDwpSZUi.CPOjCBS9Ywxf6INPq9bGY6gFZsoSXxm\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 97317-3773\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 01:52:03\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2011-05-11\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_11_aaf0109cdc64.png\",\n        \"nivel_acesso\": \"6\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:26:11'),
(31, 1, 'EDITAR_USUARIO', 'usuarios', 5, '{\n    \"antigo\": {\n        \"id\": \"5\",\n        \"nome\": \"Diácono Teixeira\",\n        \"email\": \"diacono@gmail.com\",\n        \"senha\": \"$2y$10$UfMfJlKBEB7r6qlA1BU6hu4cF1XRw61Wjw4Q\\/IpQuoatzU6mZkGUS\",\n        \"perfil_id\": \"11\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98628-0580\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 05:11:32\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": null,\n        \"foto_perfil\": \"img\\/usuarios\\/user_5_02c9ef55625c.jpeg\",\n        \"nivel_acesso\": \"5\",\n        \"perfil_nome\": \"DIACONO\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"5\",\n        \"nome\": \"Diácono Teixeira\",\n        \"email\": \"diacono@gmail.com\",\n        \"senha\": \"$2y$10$UfMfJlKBEB7r6qlA1BU6hu4cF1XRw61Wjw4Q\\/IpQuoatzU6mZkGUS\",\n        \"perfil_id\": \"11\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98628-0580\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 05:11:32\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": null,\n        \"foto_perfil\": \"img\\/usuarios\\/user_5_02c9ef55625c.jpeg\",\n        \"nivel_acesso\": \"5\",\n        \"perfil_nome\": \"DIACONO\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:26:29'),
(32, 1, 'EDITAR_USUARIO', 'usuarios', 13, '{\n    \"antigo\": {\n        \"id\": \"13\",\n        \"nome\": \"Eduardo Henrique Almeida Martins\",\n        \"email\": \"edusertania.dm@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 97121-4576\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 02:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"1999-11-13\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"13\",\n        \"nome\": \"Eduardo Henrique Almeida Martins\",\n        \"email\": \"edusertania.dm@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 97121-4576\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 02:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"1999-11-13\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:26:48'),
(33, 1, 'EDITAR_USUARIO', 'usuarios', 8, '{\n    \"antigo\": {\n        \"id\": \"8\",\n        \"nome\": \"Gabriel Bonfin\",\n        \"email\": \"gabriel@gmail.com\",\n        \"senha\": \"$2y$10$vsVhp60bGkJyCfIeMKT.be3IrkW5wwwTjYqcU2FrAhAbIzJ4YgEGm\",\n        \"perfil_id\": \"6\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99693-4222\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 06:34:04\",\n        \"ultimo_login\": \"2026-04-09 00:30:00\",\n        \"data_nascimento\": \"1997-05-12\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_8_24dc5ad775cd.jpeg\",\n        \"nivel_acesso\": \"5\",\n        \"perfil_nome\": \"PASCOM AGENTE\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"8\",\n        \"nome\": \"Gabriel Bonfin\",\n        \"email\": \"bomfimgabrieldefrancabomfim@gmail.com\",\n        \"senha\": \"$2y$10$vsVhp60bGkJyCfIeMKT.be3IrkW5wwwTjYqcU2FrAhAbIzJ4YgEGm\",\n        \"perfil_id\": \"6\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99693-4222\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 06:34:04\",\n        \"ultimo_login\": \"2026-04-09 00:30:00\",\n        \"data_nascimento\": \"1997-05-12\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_8_24dc5ad775cd.jpeg\",\n        \"nivel_acesso\": \"5\",\n        \"perfil_nome\": \"PASCOM AGENTE\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:28:04'),
(34, 1, 'EDITAR_USUARIO', 'usuarios', 22, '{\n    \"antigo\": {\n        \"id\": \"22\",\n        \"nome\": \"Gustavo da Silva Correia de Santana\",\n        \"email\": \"gustavocorreia243@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99309-8880\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 02:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2006-10-15\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"22\",\n        \"nome\": \"Gustavo da Silva Correia de Santana\",\n        \"email\": \"gustavocorreia243@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99309-8880\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 02:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2006-10-15\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:28:54'),
(35, 1, 'EDITAR_USUARIO', 'usuarios', 14, '{\n    \"antigo\": {\n        \"id\": \"14\",\n        \"nome\": \"Kátia Keli Pessoa Silva\",\n        \"email\": \"pessoakatiakeli@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 98365-2530\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 02:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"1991-07-10\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"14\",\n        \"nome\": \"Kátia Keli Pessoa Silva\",\n        \"email\": \"katiakeli005@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 98365-2530\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 02:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"1991-07-10\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:29:51'),
(36, 1, 'EDITAR_USUARIO', 'usuarios', 23, '{\n    \"antigo\": {\n        \"id\": \"23\",\n        \"nome\": \"Kauãne Macena\",\n        \"email\": \"kauanetaina05@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 99455-1241\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 02:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2005-06-23\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"23\",\n        \"nome\": \"Kauãne Macena\",\n        \"email\": \"kauanetaina05@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 99455-1241\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 02:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2005-06-23\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:30:24'),
(37, 1, 'EDITAR_USUARIO', 'usuarios', 17, '{\n    \"antigo\": {\n        \"id\": \"17\",\n        \"nome\": \"Lauanny Vitória Guedes Barbosa da Silva\",\n        \"email\": \"anny.v0p@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 98340-7393\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 02:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2007-09-30\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"17\",\n        \"nome\": \"Lauanny Vitória Guedes Barbosa da Silva\",\n        \"email\": \"anny.v0p@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 983407393\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 02:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2007-09-30\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:31:10'),
(38, 1, 'EDITAR_USUARIO', 'usuarios', 15, '{\n    \"antigo\": {\n        \"id\": \"15\",\n        \"nome\": \"Lucas Ferreira da Silva\",\n        \"email\": \"lucasferreiradasilvaf42@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98827-2211\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 02:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2009-08-03\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"15\",\n        \"nome\": \"Lucas Ferreira da Silva\",\n        \"email\": \"lucasferreiradasilvaf42@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98827-2211\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 02:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2009-08-03\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:31:56'),
(39, 1, 'EDITAR_USUARIO', 'usuarios', 19, '{\n    \"antigo\": {\n        \"id\": \"19\",\n        \"nome\": \"Marcos Anthonio Lins Moura Mariano\",\n        \"email\": \"marcosanthonio111@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99652-0202\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 02:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2007-04-30\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"19\",\n        \"nome\": \"Marcos Anthonio Lins Moura Mariano\",\n        \"email\": \"marcosanthonio111@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99652-0202\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 02:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2007-04-30\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:32:27');
INSERT INTO `log_alteracoes` (`id`, `usuario_id`, `acao`, `tabela_afetada`, `registro_id`, `detalhes_alteracao`, `paroquia_id`, `ip_origem`, `data_hora`) VALUES
(40, 1, 'EDITAR_USUARIO', 'usuarios', 6, '{\n    \"antigo\": {\n        \"id\": \"6\",\n        \"nome\": \"Maria Eduarda\",\n        \"email\": \"dudafloriano2514@gmail.com\",\n        \"senha\": \"$2y$10$v2RYQM5fmXBWU5Gjyh0GXuDintU6BDDf4f6JCdQWQjN16BUs2Wdti\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81 ) 98362-5306\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 05:11:32\",\n        \"ultimo_login\": \"2026-04-11 00:38:09\",\n        \"data_nascimento\": \"2003-09-22\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_6_b7012fef1728.jpeg\",\n        \"nivel_acesso\": \"5\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"6\",\n        \"nome\": \"Maria Eduarda\",\n        \"email\": \"dudafloriano2514@gmail.com\",\n        \"senha\": \"$2y$10$v2RYQM5fmXBWU5Gjyh0GXuDintU6BDDf4f6JCdQWQjN16BUs2Wdti\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81 ) 98362-5306\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 05:11:32\",\n        \"ultimo_login\": \"2026-04-11 00:38:09\",\n        \"data_nascimento\": \"2003-09-22\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_6_b7012fef1728.jpeg\",\n        \"nivel_acesso\": \"5\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:33:06'),
(41, 1, 'EDITAR_USUARIO', 'usuarios', 20, '{\n    \"antigo\": {\n        \"id\": \"20\",\n        \"nome\": \"Maria Eduarda Vitor Correia\",\n        \"email\": \"mariaecorreiaa@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 99916-8860\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 02:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"1997-04-26\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"20\",\n        \"nome\": \"Maria Eduarda Vitor Correia\",\n        \"email\": \"mariaecorreiaa@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 99916-8860\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-09 02:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"1997-04-26\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:33:34'),
(42, 1, 'EDITAR_USUARIO', 'usuarios', 4, '{\n    \"antigo\": {\n        \"id\": \"4\",\n        \"nome\": \"Rangel Silva\",\n        \"email\": \"rangelsjc1@gmail.com\",\n        \"senha\": \"$2y$12$S7DZQdfwQnphNLHcAQucsOztsP0vrNJvqqVeh73JW7M6nuvRGLVmG\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98146-1663\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"1\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"1\",\n        \"data_criacao\": \"2026-04-08 16:48:03\",\n        \"ultimo_login\": \"2026-04-12 00:11:17\",\n        \"data_nascimento\": \"1983-07-18\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_10_b08392877579.jpeg\",\n        \"nivel_acesso\": \"4\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    },\n    \"novo\": {\n        \"id\": \"4\",\n        \"nome\": \"Rangel Silva\",\n        \"email\": \"rangelsjc1@gmail.com\",\n        \"senha\": \"$2y$12$S7DZQdfwQnphNLHcAQucsOztsP0vrNJvqqVeh73JW7M6nuvRGLVmG\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98146-1663\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"1\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"1\",\n        \"data_criacao\": \"2026-04-08 16:48:03\",\n        \"ultimo_login\": \"2026-04-12 00:11:17\",\n        \"data_nascimento\": \"1983-07-18\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_10_b08392877579.jpeg\",\n        \"nivel_acesso\": \"4\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:33:56'),
(43, 1, 'REGISTRAR_USUARIO', 'usuarios', 32, '{\n    \"novo\": {\n        \"nome\": \"Adalbério Mota\",\n        \"email\": \"adalberio.vilela@gmail.com\",\n        \"telefone\": \"(81) 98102-8829\",\n        \"data_nascimento\": \"1985-12-09\",\n        \"sexo\": \"M\",\n        \"perfil_id\": \"9\",\n        \"paroquia_id\": \"2\",\n        \"senha\": \"adalberio12\",\n        \"confirmar_senha\": \"adalberio12\",\n        \"palavra_chave\": \"PIO X\"\n    },\n    \"foto_perfil\": \"\"\n}', 2, '45.164.11.220', '2026-04-12 00:42:32'),
(44, 1, 'EDITAR_USUARIO', 'usuarios', 32, '{\n    \"antigo\": {\n        \"id\": \"32\",\n        \"nome\": \"Adalbério Mota\",\n        \"email\": \"adalberio.vilela@gmail.com\",\n        \"senha\": \"$2y$12$2eSkNTDk6ltqO11s7N.hou2UKytf62tGSrR.7deqj\\/P5BkiqJB5r6\",\n        \"perfil_id\": \"9\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98102-8829\",\n        \"palavra_chave\": \"PIO X\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-12 00:42:32\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"1985-12-09\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"SEMINARISTA\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"32\",\n        \"nome\": \"Adalbério Mota\",\n        \"email\": \"adalberio.vilela@gmail.com\",\n        \"senha\": \"$2y$12$2eSkNTDk6ltqO11s7N.hou2UKytf62tGSrR.7deqj\\/P5BkiqJB5r6\",\n        \"perfil_id\": \"9\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98102-8829\",\n        \"palavra_chave\": \"PIO X\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-12 00:42:32\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"1985-12-09\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"SEMINARISTA\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 00:43:01'),
(45, 4, 'EDITAR_USUARIO', 'usuarios', 24, '{\n    \"antigo\": {\n        \"id\": \"24\",\n        \"nome\": \"Adalbério Mota\",\n        \"email\": \"adalberio.vilela@gmail.com\",\n        \"senha\": \"$2y$12$2eSkNTDk6ltqO11s7N.hou2UKytf62tGSrR.7deqj\\/P5BkiqJB5r6\",\n        \"perfil_id\": \"9\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98102-8829\",\n        \"palavra_chave\": \"PIO X\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-12 00:42:32\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"1985-12-09\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"5\",\n        \"perfil_nome\": \"SEMINARISTA\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"24\",\n        \"nome\": \"Adalbério Mota\",\n        \"email\": \"adalberio.vilela@gmail.com\",\n        \"senha\": \"$2y$12$2eSkNTDk6ltqO11s7N.hou2UKytf62tGSrR.7deqj\\/P5BkiqJB5r6\",\n        \"perfil_id\": \"9\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98102-8829\",\n        \"palavra_chave\": \"PIO X\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-12 00:42:32\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"1985-12-09\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"5\",\n        \"perfil_nome\": \"SEMINARISTA\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 01:54:01'),
(46, 1, 'EDITAR_USUARIO', 'usuarios', 7, '{\n    \"antigo\": {\n        \"id\": \"7\",\n        \"nome\": \"Amanda Leal\",\n        \"email\": \"amandapeixoto87@gmail.com\",\n        \"senha\": \"$2y$10$i3YrXwlRKxww3QvuBU1QO.KoBQCBnIbC4WxHpfZQuzecZ1HAyoXdC\",\n        \"perfil_id\": \"4\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99858-6006\",\n        \"palavra_chave\": \"PIO X\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 05:11:32\",\n        \"ultimo_login\": \"2026-04-11 01:59:07\",\n        \"data_nascimento\": \"1987-03-11\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_7_9988b8ae3614.jpeg\",\n        \"nivel_acesso\": \"4\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    },\n    \"novo\": {\n        \"id\": \"7\",\n        \"nome\": \"Amanda Leal\",\n        \"email\": \"amandapeixoto87@gmail.com\",\n        \"senha\": \"$2y$10$i3YrXwlRKxww3QvuBU1QO.KoBQCBnIbC4WxHpfZQuzecZ1HAyoXdC\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99858-6006\",\n        \"palavra_chave\": \"PIO X\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 05:11:32\",\n        \"ultimo_login\": \"2026-04-11 01:59:07\",\n        \"data_nascimento\": \"1987-03-11\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_7_9988b8ae3614.jpeg\",\n        \"nivel_acesso\": \"4\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 02:22:40'),
(47, 1, 'EDITAR_USUARIO', 'usuarios', 4, '{\n    \"antigo\": {\n        \"id\": \"4\",\n        \"nome\": \"Rangel Silva\",\n        \"email\": \"rangelsjc1@gmail.com\",\n        \"senha\": \"$2y$12$S7DZQdfwQnphNLHcAQucsOztsP0vrNJvqqVeh73JW7M6nuvRGLVmG\",\n        \"perfil_id\": \"2\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98146-1663\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"1\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"1\",\n        \"data_criacao\": \"2026-04-08 16:48:03\",\n        \"ultimo_login\": \"2026-04-12 00:11:17\",\n        \"data_nascimento\": \"1983-07-18\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_10_b08392877579.jpeg\",\n        \"nivel_acesso\": \"4\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    },\n    \"novo\": {\n        \"id\": \"4\",\n        \"nome\": \"Rangel Silva\",\n        \"email\": \"rangelsjc1@gmail.com\",\n        \"senha\": \"$2y$12$S7DZQdfwQnphNLHcAQucsOztsP0vrNJvqqVeh73JW7M6nuvRGLVmG\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98146-1663\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"1\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"1\",\n        \"data_criacao\": \"2026-04-08 16:48:03\",\n        \"ultimo_login\": \"2026-04-12 00:11:17\",\n        \"data_nascimento\": \"1983-07-18\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_10_b08392877579.jpeg\",\n        \"nivel_acesso\": \"1\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 02:23:39'),
(48, 4, 'LOGIN', 'usuarios', 4, 'Autenticacao bem-sucedida', 2, '45.164.11.220', '2026-04-12 02:25:53'),
(49, 1, 'EDITAR_USUARIO', 'usuarios', 4, '{\n    \"antigo\": {\n        \"id\": \"4\",\n        \"nome\": \"Rangel Silva\",\n        \"email\": \"rangelsjc1@gmail.com\",\n        \"senha\": \"$2y$12$S7DZQdfwQnphNLHcAQucsOztsP0vrNJvqqVeh73JW7M6nuvRGLVmG\",\n        \"perfil_id\": \"3\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98146-1663\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"1\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"1\",\n        \"data_criacao\": \"2026-04-08 16:48:03\",\n        \"ultimo_login\": \"2026-04-12 02:25:53\",\n        \"data_nascimento\": \"1983-07-18\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_10_b08392877579.jpeg\",\n        \"nivel_acesso\": \"1\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    },\n    \"novo\": {\n        \"id\": \"4\",\n        \"nome\": \"Rangel Silva\",\n        \"email\": \"rangelsjc1@gmail.com\",\n        \"senha\": \"$2y$12$S7DZQdfwQnphNLHcAQucsOztsP0vrNJvqqVeh73JW7M6nuvRGLVmG\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98146-1663\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"1\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"1\",\n        \"data_criacao\": \"2026-04-08 16:48:03\",\n        \"ultimo_login\": \"2026-04-12 02:25:53\",\n        \"data_nascimento\": \"1983-07-18\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_10_b08392877579.jpeg\",\n        \"nivel_acesso\": \"1\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 02:27:05'),
(50, 1, 'EDITAR_USUARIO', 'usuarios', 4, '{\n    \"antigo\": {\n        \"id\": \"4\",\n        \"nome\": \"Rangel Silva\",\n        \"email\": \"rangelsjc1@gmail.com\",\n        \"senha\": \"$2y$12$S7DZQdfwQnphNLHcAQucsOztsP0vrNJvqqVeh73JW7M6nuvRGLVmG\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98146-1663\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"1\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"1\",\n        \"data_criacao\": \"2026-04-08 16:48:03\",\n        \"ultimo_login\": \"2026-04-12 02:25:53\",\n        \"data_nascimento\": \"1983-07-18\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_10_b08392877579.jpeg\",\n        \"nivel_acesso\": \"1\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    },\n    \"novo\": {\n        \"id\": \"4\",\n        \"nome\": \"Rangel Silva\",\n        \"email\": \"rangelsjc1@gmail.com\",\n        \"senha\": \"$2y$12$S7DZQdfwQnphNLHcAQucsOztsP0vrNJvqqVeh73JW7M6nuvRGLVmG\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98146-1663\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"1\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"1\",\n        \"data_criacao\": \"2026-04-08 16:48:03\",\n        \"ultimo_login\": \"2026-04-12 02:25:53\",\n        \"data_nascimento\": \"1983-07-18\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_10_b08392877579.jpeg\",\n        \"nivel_acesso\": \"2\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 02:29:41'),
(51, 4, 'LOGOUT', 'usuarios', 4, 'Sessão encerrada pelo usuário', 2, '45.164.11.220', '2026-04-12 02:55:04'),
(52, 4, 'LOGIN', 'usuarios', 4, 'Autenticacao bem-sucedida', 2, '45.164.11.220', '2026-04-12 02:55:16'),
(53, 1, 'LOGOUT', 'usuarios', 1, 'Sessão encerrada pelo usuário', 2, '45.164.11.220', '2026-04-12 03:13:54'),
(54, 1, 'LOGIN', 'usuarios', 1, 'Autenticacao bem-sucedida', 2, '45.164.11.220', '2026-04-12 03:14:12'),
(55, 1, 'LOGOUT', 'usuarios', 1, 'Sessão encerrada pelo usuário', 2, '45.164.11.220', '2026-04-12 03:20:39'),
(56, 1, 'LOGIN', 'usuarios', 1, 'Autenticacao bem-sucedida', 2, '45.164.11.220', '2026-04-12 03:20:51'),
(57, 1, 'INSCREVER_ATIVIDADE', 'inscricoes', 2, '{\n    \"atividade_id\": 2,\n    \"evento_item_id\": null,\n    \"usuario_id\": 1\n}', 2, '45.164.11.220', '2026-04-12 03:23:17'),
(58, 1, 'INSCREVER_ATIVIDADE_EVENTO', 'atividade_evento_inscricoes', 1, '{\n    \"atividade_id\": 1,\n    \"evento_item_id\": 1,\n    \"usuario_id\": 1\n}', 2, '45.164.11.220', '2026-04-12 03:23:26'),
(59, 1, 'CANCELAR_INSCRICAO_ATIVIDADE_EVENTO', 'atividade_evento_inscricoes', 1, '{\n    \"atividade_id\": 1,\n    \"evento_item_id\": 1,\n    \"usuario_id\": 1\n}', 2, '45.164.11.220', '2026-04-12 03:23:34'),
(60, 1, 'CANCELAR_INSCRICAO_ATIVIDADE', 'inscricoes', 2, '{\n    \"atividade_id\": 2,\n    \"evento_item_id\": null,\n    \"usuario_id\": 1\n}', 2, '45.164.11.220', '2026-04-12 03:23:40'),
(61, 4, 'LOGOUT', 'usuarios', 4, 'Sessão encerrada pelo usuário', 2, '45.164.11.220', '2026-04-12 03:39:50'),
(62, 1, 'LOGOUT', 'usuarios', 1, 'Sessão encerrada pelo usuário', 2, '45.164.11.220', '2026-04-12 03:40:04'),
(63, 4, 'LOGIN', 'usuarios', 4, 'Autenticacao bem-sucedida', 2, '45.164.11.220', '2026-04-12 03:59:34'),
(64, 4, 'EDITAR_USUARIO', 'usuarios', 24, '{\n    \"antigo\": {\n        \"id\": \"24\",\n        \"nome\": \"Adalbério Mota\",\n        \"email\": \"adalberio.vilela@gmail.com\",\n        \"senha\": \"$2y$12$2eSkNTDk6ltqO11s7N.hou2UKytf62tGSrR.7deqj\\/P5BkiqJB5r6\",\n        \"perfil_id\": \"9\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98102-8829\",\n        \"palavra_chave\": \"PIO X\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-12 00:42:32\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"1985-12-09\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"5\",\n        \"perfil_nome\": \"SEMINARISTA\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"24\",\n        \"nome\": \"Adalbério Mota\",\n        \"email\": \"adalberio.vilela@gmail.com\",\n        \"senha\": \"$2y$12$2eSkNTDk6ltqO11s7N.hou2UKytf62tGSrR.7deqj\\/P5BkiqJB5r6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98102-8829\",\n        \"palavra_chave\": \"PIO X\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-12 00:42:32\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"1985-12-09\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"5\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 04:01:13'),
(65, 4, 'EDITAR_USUARIO', 'usuarios', 3, '{\n    \"antigo\": {\n        \"id\": \"3\",\n        \"nome\": \"Ana Carla de Melo\",\n        \"email\": \"secretariaparoquialiputinga@gmail.com\",\n        \"senha\": \"$2y$12$WNvykg6E.xrpeehYTlhp.OvQYa8zNmIgiQ9fhjABBzvv5ip8OGKiG\",\n        \"perfil_id\": \"3\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 99593-9042\",\n        \"palavra_chave\": \"PIO X\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"1\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-03-12 01:37:29\",\n        \"ultimo_login\": \"2026-04-11 22:54:17\",\n        \"data_nascimento\": \"1980-11-28\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_3_03b84e37e9a3.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"SECRETARIA\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"3\",\n        \"nome\": \"Ana Carla de Melo\",\n        \"email\": \"secretariaparoquialiputinga@gmail.com\",\n        \"senha\": \"$2y$12$WNvykg6E.xrpeehYTlhp.OvQYa8zNmIgiQ9fhjABBzvv5ip8OGKiG\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 99593-9042\",\n        \"palavra_chave\": \"PIO X\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"1\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-03-12 01:37:29\",\n        \"ultimo_login\": \"2026-04-11 22:54:17\",\n        \"data_nascimento\": \"1980-11-28\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_3_03b84e37e9a3.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 04:01:49'),
(66, 4, 'EDITAR_USUARIO', 'usuarios', 3, '{\n    \"antigo\": {\n        \"id\": \"3\",\n        \"nome\": \"Ana Carla de Melo\",\n        \"email\": \"secretariaparoquialiputinga@gmail.com\",\n        \"senha\": \"$2y$12$WNvykg6E.xrpeehYTlhp.OvQYa8zNmIgiQ9fhjABBzvv5ip8OGKiG\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 99593-9042\",\n        \"palavra_chave\": \"PIO X\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"1\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-03-12 01:37:29\",\n        \"ultimo_login\": \"2026-04-11 22:54:17\",\n        \"data_nascimento\": \"1980-11-28\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_3_03b84e37e9a3.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"3\",\n        \"nome\": \"Ana Carla de Melo\",\n        \"email\": \"secretariaparoquialiputinga@gmail.com\",\n        \"senha\": \"$2y$12$WNvykg6E.xrpeehYTlhp.OvQYa8zNmIgiQ9fhjABBzvv5ip8OGKiG\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 99593-9042\",\n        \"palavra_chave\": \"PIO X\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"1\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-03-12 01:37:29\",\n        \"ultimo_login\": \"2026-04-11 22:54:17\",\n        \"data_nascimento\": \"1980-11-28\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_3_03b84e37e9a3.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 04:02:17'),
(67, 4, 'LOGIN', 'usuarios', 4, 'Autenticacao bem-sucedida', 2, '45.164.11.220', '2026-04-12 04:04:16'),
(68, 4, 'EDITAR_USUARIO', 'usuarios', 3, '{\n    \"antigo\": {\n        \"id\": \"3\",\n        \"nome\": \"Ana Carla de Melo\",\n        \"email\": \"secretariaparoquialiputinga@gmail.com\",\n        \"senha\": \"$2y$12$WNvykg6E.xrpeehYTlhp.OvQYa8zNmIgiQ9fhjABBzvv5ip8OGKiG\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 99593-9042\",\n        \"palavra_chave\": \"PIO X\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"1\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-03-12 01:37:29\",\n        \"ultimo_login\": \"2026-04-11 22:54:17\",\n        \"data_nascimento\": \"1980-11-28\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_3_03b84e37e9a3.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"3\",\n        \"nome\": \"Ana Carla de Melo\",\n        \"email\": \"secretariaparoquialiputinga@gmail.com\",\n        \"senha\": \"$2y$12$WNvykg6E.xrpeehYTlhp.OvQYa8zNmIgiQ9fhjABBzvv5ip8OGKiG\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 99593-9042\",\n        \"palavra_chave\": \"PIO X\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"1\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-03-12 01:37:29\",\n        \"ultimo_login\": \"2026-04-11 22:54:17\",\n        \"data_nascimento\": \"1980-11-28\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_3_5ab3dedc5928.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 04:04:39'),
(69, 4, 'LOGOUT', 'usuarios', 4, 'Sessão encerrada pelo usuário', 2, '45.164.11.220', '2026-04-12 04:05:54'),
(70, 1, 'LOGIN', 'usuarios', 1, 'Autenticacao bem-sucedida', 2, '45.164.11.220', '2026-04-12 04:06:04'),
(71, 1, 'EDITAR_USUARIO', 'usuarios', 3, '{\n    \"antigo\": {\n        \"id\": \"3\",\n        \"nome\": \"Ana Carla de Melo\",\n        \"email\": \"secretariaparoquialiputinga@gmail.com\",\n        \"senha\": \"$2y$12$WNvykg6E.xrpeehYTlhp.OvQYa8zNmIgiQ9fhjABBzvv5ip8OGKiG\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 99593-9042\",\n        \"palavra_chave\": \"PIO X\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"1\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-03-12 01:37:29\",\n        \"ultimo_login\": \"2026-04-11 22:54:17\",\n        \"data_nascimento\": \"1980-11-28\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_3_5ab3dedc5928.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"3\",\n        \"nome\": \"Ana Carla de Melo\",\n        \"email\": \"secretariaparoquialiputinga@gmail.com\",\n        \"senha\": \"$2y$12$WNvykg6E.xrpeehYTlhp.OvQYa8zNmIgiQ9fhjABBzvv5ip8OGKiG\",\n        \"perfil_id\": \"3\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 99593-9042\",\n        \"palavra_chave\": \"PIO X\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"1\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-03-12 01:37:29\",\n        \"ultimo_login\": \"2026-04-11 22:54:17\",\n        \"data_nascimento\": \"1980-11-28\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_3_5ab3dedc5928.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"SECRETARIA\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 04:06:33'),
(72, 4, 'EDITAR_USUARIO', 'usuarios', 3, '{\n    \"antigo\": {\n        \"id\": \"3\",\n        \"nome\": \"Ana Carla de Melo\",\n        \"email\": \"secretariaparoquialiputinga@gmail.com\",\n        \"senha\": \"$2y$12$WNvykg6E.xrpeehYTlhp.OvQYa8zNmIgiQ9fhjABBzvv5ip8OGKiG\",\n        \"perfil_id\": \"3\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 99593-9042\",\n        \"palavra_chave\": \"PIO X\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"1\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-03-12 01:37:29\",\n        \"ultimo_login\": \"2026-04-11 22:54:17\",\n        \"data_nascimento\": \"1980-11-28\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_3_5ab3dedc5928.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"SECRETARIA\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"3\",\n        \"nome\": \"Ana Carla de Melo\",\n        \"email\": \"secretariaparoquialiputinga@gmail.com\",\n        \"senha\": \"$2y$12$WNvykg6E.xrpeehYTlhp.OvQYa8zNmIgiQ9fhjABBzvv5ip8OGKiG\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 99593-9042\",\n        \"palavra_chave\": \"PIO X\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"1\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-03-12 01:37:29\",\n        \"ultimo_login\": \"2026-04-11 22:54:17\",\n        \"data_nascimento\": \"1980-11-28\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_3_5ab3dedc5928.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}', 2, '45.164.11.220', '2026-04-12 04:07:24');

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
(1, 'Igreja Católica Apostólica Romana', 'Vaticano', 'IT', 'Santa Sé', 1, '2026-03-12 01:37:29'),
(2, 'Nossa Senhora da Conceição', 'Pernambuco', 'PE', 'AOR', 1, '2026-04-08 05:33:29');

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
(1, 2, 'Celebração da Palavra', '#9333ea', '🙏', 'Test description'),
(2, 2, 'Evento Social', '#db2777', '🎨', 'Test description'),
(3, 2, 'Formação', '#2563eb', '⛪', 'Test description'),
(4, 2, 'Reunião', '#059669', '📖', 'Test description'),
(5, 2, 'Quermesse', '#059669', '🍷', 'Test description'),
(6, 2, 'Festa', '#059669', '🔥', 'Test description'),
(7, 2, 'Programar Meta business', '#059669', '🖥️', 'Test description'),
(9, 1, 'Other Category P1', '#00ff00', '🙏', 'Another description'),
(10, 2, 'Celebração da Palavra com Adoração', NULL, '🙏', NULL),
(11, 2, 'Santa Missa', NULL, '⛪', NULL),
(12, 2, 'Santa Missa com Adoração', NULL, '⛪', NULL);

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
  `perm_criar_eventos` tinyint(1) DEFAULT 0,
  `perm_editar_eventos` tinyint(1) DEFAULT 0,
  `perm_excluir_eventos` tinyint(1) DEFAULT 0,
  `perm_ver_restritos` tinyint(1) DEFAULT 0,
  `perm_cadastrar_usuario` tinyint(1) DEFAULT 0,
  `perm_admin_usuarios` tinyint(1) DEFAULT 0,
  `perm_ver_logs` tinyint(1) DEFAULT 0,
  `data_criacao` timestamp NULL DEFAULT current_timestamp(),
  `ultimo_login` timestamp NULL DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `nivel_acesso` int(10) DEFAULT 0,
  `perfil_nome` varchar(50) DEFAULT NULL,
  `perm_ver_calendario` tinyint(1) DEFAULT 1,
  `perm_admin_sistema` tinyint(1) DEFAULT 0,
  `perm_gerenciar_catalogo` tinyint(1) DEFAULT 0,
  `perm_gerenciar_grupos` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `perfil_id`, `paroquia_id`, `sexo`, `telefone`, `palavra_chave`, `ativo`, `perm_criar_eventos`, `perm_editar_eventos`, `perm_excluir_eventos`, `perm_ver_restritos`, `perm_cadastrar_usuario`, `perm_admin_usuarios`, `perm_ver_logs`, `data_criacao`, `ultimo_login`, `data_nascimento`, `foto_perfil`, `nivel_acesso`, `perfil_nome`, `perm_ver_calendario`, `perm_admin_sistema`, `perm_gerenciar_catalogo`, `perm_gerenciar_grupos`) VALUES
(1, 'Admin Sistema', 'admin@sistema.com', '$2y$12$UwoLTgu4egu0E8Bi5aEcO./euyUqoEIsC4R/j1c/dbdSQzEc5KuX6', NULL, 2, 'M', '(81) 99999999', NULL, 1, 0, 0, 0, 0, 0, 0, 0, '2026-03-12 01:37:29', '2026-04-12 04:06:04', '1988-12-14', 'img/usuarios/user_1_0af8cc25e98c.png', 0, 'ADMINISTRADOR PAROQUIAL', 1, 0, 0, 0),
(2, 'Pe. Sérgio Muniz', 'pesergio@gmail.com', '$2y$10$wRo8Qwh6jwHxTbTYfF3bJu068nLyv/5BIcRpC33pBlx.i/wnNUamq', NULL, 2, 'M', '(81) 99615-8138', NULL, 1, 1, 1, 1, 1, 0, 0, 0, '2026-03-12 01:37:29', '2026-04-11 23:13:41', '1988-12-14', 'img/usuarios/user_2_20b9f93ed98a.jpeg', 3, 'ADMINISTRADOR PAROQUIAL', 1, 0, 0, 0),
(3, 'Ana Carla de Melo', 'secretariaparoquialiputinga@gmail.com', '$2y$12$WNvykg6E.xrpeehYTlhp.OvQYa8zNmIgiQ9fhjABBzvv5ip8OGKiG', NULL, 2, 'F', '(81) 99593-9042', 'PIO X', 1, 1, 1, 1, 1, 0, 0, 0, '2026-03-12 01:37:29', '2026-04-11 22:54:17', '1980-11-28', 'img/usuarios/user_3_5ab3dedc5928.jpeg', 3, 'PASCOM ADM', 1, 0, 0, 0),
(4, 'Rangel Silva', 'rangelsjc1@gmail.com', '$2y$12$S7DZQdfwQnphNLHcAQucsOztsP0vrNJvqqVeh73JW7M6nuvRGLVmG', NULL, 2, 'M', '(81) 98146-1663', NULL, 1, 1, 1, 1, 0, 1, 1, 1, '2026-04-08 16:48:03', '2026-04-12 04:04:16', '1983-07-18', 'img/usuarios/user_10_b08392877579.jpeg', 2, 'PASCOM ADM', 1, 0, 1, 1),
(5, 'Diácono Teixeira', 'diacono@gmail.com', '$2y$10$UfMfJlKBEB7r6qlA1BU6hu4cF1XRw61Wjw4Q/IpQuoatzU6mZkGUS', NULL, 2, 'M', '(81) 98628-0580', NULL, 1, 0, 0, 0, 0, 0, 0, 0, '2026-04-08 05:11:32', NULL, NULL, 'img/usuarios/user_5_02c9ef55625c.jpeg', 5, 'DIACONO', 1, 0, 0, 0),
(6, 'Maria Eduarda', 'dudafloriano2514@gmail.com', '$2y$10$v2RYQM5fmXBWU5Gjyh0GXuDintU6BDDf4f6JCdQWQjN16BUs2Wdti', NULL, 2, 'F', '(81 ) 98362-5306', NULL, 1, 1, 1, 1, 0, 0, 0, 0, '2026-04-08 05:11:32', '2026-04-11 00:38:09', '2003-09-22', 'img/usuarios/user_6_b7012fef1728.jpeg', 5, 'PASCOM ADM', 1, 0, 0, 0),
(7, 'Amanda Leal', 'amandapeixoto87@gmail.com', '$2y$10$i3YrXwlRKxww3QvuBU1QO.KoBQCBnIbC4WxHpfZQuzecZ1HAyoXdC', NULL, 2, 'M', '(81) 99858-6006', 'PIO X', 1, 1, 1, 1, 0, 0, 1, 0, '2026-04-08 05:11:32', '2026-04-11 01:59:07', '1987-03-11', 'img/usuarios/user_7_9988b8ae3614.jpeg', 4, 'PASCOM ADM', 1, 0, 1, 1),
(8, 'Gabriel Bonfin', 'bomfimgabrieldefrancabomfim@gmail.com', '$2y$10$vsVhp60bGkJyCfIeMKT.be3IrkW5wwwTjYqcU2FrAhAbIzJ4YgEGm', NULL, 2, 'M', '(81) 99693-4222', NULL, 1, 1, 1, 1, 0, 0, 0, 0, '2026-04-08 06:34:04', '2026-04-09 00:30:00', '1997-05-12', 'img/usuarios/user_8_24dc5ad775cd.jpeg', 5, 'PASCOM AGENTE', 1, 0, 0, 0),
(11, 'Danusa Maria Silva do Nascimento', 'docinhocida@hotmail.com', '$2y$10$lqu1oJFDZxEeGcDwpSZUi.CPOjCBS9Ywxf6INPq9bGY6gFZsoSXxm', NULL, 2, 'F', '(81) 97317-3773', NULL, 1, 0, 0, 0, 0, 0, 0, 0, '2026-04-09 01:52:03', NULL, '2011-05-11', 'img/usuarios/user_11_aaf0109cdc64.png', 6, 'PASCOM AGENTE 2', 1, 0, 0, 0),
(12, 'Alif Victória Alves de Lima', 'vitoriaalif@gmail.com', '$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6', NULL, 2, 'F', '(81) 98371-1185', NULL, 1, 0, 0, 0, 0, 0, 0, 0, '2026-04-09 02:31:59', '2026-04-09 03:36:13', '2009-01-05', NULL, 3, 'PASCOM AGENTE 2', 1, 0, 0, 0),
(13, 'Eduardo Henrique Almeida Martins', 'edusertania.dm@gmail.com', '$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6', NULL, 2, 'M', '(81) 97121-4576', NULL, 1, 0, 0, 0, 0, 0, 0, 0, '2026-04-09 02:31:59', NULL, '1999-11-13', NULL, 3, 'PASCOM AGENTE 2', 1, 0, 0, 0),
(14, 'Kátia Keli Pessoa Silva', 'katiakeli005@gmail.com', '$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6', NULL, 2, 'F', '(81) 98365-2530', NULL, 1, 0, 0, 0, 0, 0, 0, 0, '2026-04-09 02:31:59', NULL, '1991-07-10', NULL, 3, 'PASCOM AGENTE 2', 1, 0, 0, 0),
(15, 'Lucas Ferreira da Silva', 'lucasferreiradasilvaf42@gmail.com', '$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6', NULL, 2, 'M', '(81) 98827-2211', NULL, 1, 0, 0, 0, 0, 0, 0, 0, '2026-04-09 02:31:59', NULL, '2009-08-03', NULL, 3, 'PASCOM AGENTE 2', 1, 0, 0, 0),
(17, 'Lauanny Vitória Guedes Barbosa da Silva', 'anny.v0p@gmail.com', '$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6', NULL, 2, 'F', '(81) 983407393', NULL, 1, 0, 0, 0, 0, 0, 0, 0, '2026-04-09 02:31:59', NULL, '2007-09-30', NULL, 3, 'PASCOM AGENTE 2', 1, 0, 0, 0),
(18, 'Danilo da Silva Medeiros', 'danilosilvamedeiros19@gmail.com', '$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6', NULL, 2, 'M', '(81) 99381-2347', NULL, 1, 0, 0, 0, 0, 0, 0, 0, '2026-04-09 02:31:59', NULL, '2008-06-19', NULL, 3, 'PASCOM AGENTE 2', 1, 0, 0, 0),
(19, 'Marcos Anthonio Lins Moura Mariano', 'marcosanthonio111@gmail.com', '$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6', NULL, 2, 'M', '(81) 99652-0202', NULL, 1, 0, 0, 0, 0, 0, 0, 0, '2026-04-09 02:31:59', NULL, '2007-04-30', NULL, 3, 'PASCOM AGENTE 2', 1, 0, 0, 0),
(20, 'Maria Eduarda Vitor Correia', 'mariaecorreiaa@gmail.com', '$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6', NULL, 2, 'F', '(81) 99916-8860', NULL, 1, 0, 0, 0, 0, 0, 0, 0, '2026-04-09 02:31:59', NULL, '1997-04-26', NULL, 3, 'PASCOM AGENTE 2', 1, 0, 0, 0),
(21, 'Danilo José de Bonfim de Brito', 'danilojosebomfim14@gmail.com', '$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6', NULL, 2, 'M', '(81) 98311-4355', NULL, 1, 0, 0, 0, 0, 0, 0, 0, '2026-04-09 02:31:59', NULL, '2004-09-04', NULL, 3, 'PASCOM AGENTE 2', 1, 0, 0, 0),
(22, 'Gustavo da Silva Correia de Santana', 'gustavocorreia243@gmail.com', '$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6', NULL, 2, 'M', '(81) 99309-8880', NULL, 1, 0, 0, 0, 0, 0, 0, 0, '2026-04-09 02:31:59', NULL, '2006-10-15', NULL, 3, 'PASCOM AGENTE 2', 1, 0, 0, 0),
(23, 'Kauãne Macena', 'kauanetaina05@gmail.com', '$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6', NULL, 2, 'F', '(81) 99455-1241', NULL, 1, 0, 0, 0, 0, 0, 0, 0, '2026-04-09 02:31:59', NULL, '2005-06-23', NULL, 3, 'PASCOM AGENTE 2', 1, 0, 0, 0),
(24, 'Adalbério Mota', 'adalberio.vilela@gmail.com', '$2y$12$2eSkNTDk6ltqO11s7N.hou2UKytf62tGSrR.7deqj/P5BkiqJB5r6', NULL, 2, 'M', '(81) 98102-8829', 'PIO X', 1, 0, 0, 0, 0, 0, 0, 0, '2026-04-12 00:42:32', NULL, '1985-12-09', NULL, 5, 'PASCOM AGENTE 2', 1, 0, 0, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario_grupos`
--

CREATE TABLE `usuario_grupos` (
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `grupo_id` int(10) UNSIGNED NOT NULL,
  `paroquia_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuario_grupos`
--

INSERT INTO `usuario_grupos` (`usuario_id`, `grupo_id`, `paroquia_id`) VALUES
(1, 7, 2),
(2, 4, 2),
(2, 7, 2),
(3, 4, 2),
(3, 7, 2),
(4, 1, 2),
(4, 2, 2),
(4, 3, 2),
(4, 4, 2),
(4, 7, 2),
(4, 8, 2),
(4, 11, 2),
(5, 4, 2),
(5, 7, 2),
(6, 1, 2),
(6, 2, 2),
(6, 3, 2),
(6, 7, 2),
(6, 11, 2),
(7, 1, 2),
(7, 2, 2),
(7, 3, 2),
(7, 4, 2),
(7, 7, 2),
(7, 8, 2),
(7, 11, 2),
(8, 1, 2),
(8, 7, 2),
(8, 11, 2),
(11, 1, 2),
(11, 7, 2),
(11, 11, 2),
(12, 3, 2),
(12, 7, 2),
(12, 11, 2),
(13, 1, 2),
(13, 3, 2),
(13, 7, 2),
(14, 1, 2),
(14, 7, 2),
(15, 1, 2),
(15, 7, 2),
(16, 7, 2),
(17, 1, 2),
(17, 7, 2),
(18, 1, 2),
(18, 7, 2),
(18, 8, 2),
(19, 1, 2),
(19, 7, 2),
(20, 1, 2),
(20, 7, 2),
(21, 1, 2),
(21, 7, 2),
(22, 1, 2),
(22, 7, 2),
(23, 1, 2),
(23, 7, 2),
(24, 1, 2),
(24, 7, 2),
(32, 1, 2),
(32, 7, 2);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `atividades`
--
ALTER TABLE `atividades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_atividades_paroquia` (`paroquia_id`),
  ADD KEY `fk_atividades_local` (`local_id`),
  ADD KEY `fk_atividades_tipo` (`tipo_atividade_id`);

--
-- Índices de tabela `atividades_catalogo`
--
ALTER TABLE `atividades_catalogo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `paroquia_id` (`paroquia_id`,`nome`);

--
-- Índices de tabela `atividade_evento_inscricoes`
--
ALTER TABLE `atividade_evento_inscricoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `evento_item_id` (`evento_item_id`,`usuario_id`);

--
-- Índices de tabela `atividade_evento_itens`
--
ALTER TABLE `atividade_evento_itens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `evento_id` (`evento_id`,`atividade_catalogo_id`);

--
-- Índices de tabela `atividade_grupos`
--
ALTER TABLE `atividade_grupos`
  ADD PRIMARY KEY (`atividade_id`,`grupo_id`),
  ADD KEY `fk_ag_atividade` (`atividade_id`),
  ADD KEY `fk_ag_grupo` (`grupo_id`);

--
-- Índices de tabela `auth_throttle`
--
ALTER TABLE `auth_throttle`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `scope` (`scope`,`identifier`);

--
-- Índices de tabela `grupos_trabalho`
--
ALTER TABLE `grupos_trabalho`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `inscricoes`
--
ALTER TABLE `inscricoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `atividade_id` (`atividade_id`,`usuario_id`);

--
-- Índices de tabela `locais_paroquia`
--
ALTER TABLE `locais_paroquia`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `log_alteracoes`
--
ALTER TABLE `log_alteracoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `paroquias`
--
ALTER TABLE `paroquias`
  ADD PRIMARY KEY (`id`);

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
-- Índices de tabela `usuario_grupos`
--
ALTER TABLE `usuario_grupos`
  ADD PRIMARY KEY (`usuario_id`,`grupo_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `atividades`
--
ALTER TABLE `atividades`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `atividades_catalogo`
--
ALTER TABLE `atividades_catalogo`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `atividade_evento_inscricoes`
--
ALTER TABLE `atividade_evento_inscricoes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `atividade_evento_itens`
--
ALTER TABLE `atividade_evento_itens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `auth_throttle`
--
ALTER TABLE `auth_throttle`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `grupos_trabalho`
--
ALTER TABLE `grupos_trabalho`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `inscricoes`
--
ALTER TABLE `inscricoes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `locais_paroquia`
--
ALTER TABLE `locais_paroquia`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `log_alteracoes`
--
ALTER TABLE `log_alteracoes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT de tabela `paroquias`
--
ALTER TABLE `paroquias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `tipos_atividade`
--
ALTER TABLE `tipos_atividade`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `atividades`
--
ALTER TABLE `atividades`
  ADD CONSTRAINT `fk_atividades_local` FOREIGN KEY (`local_id`) REFERENCES `locais_paroquia` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_atividades_paroquia` FOREIGN KEY (`paroquia_id`) REFERENCES `paroquias` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_atividades_tipo` FOREIGN KEY (`tipo_atividade_id`) REFERENCES `tipos_atividade` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `atividades_catalogo`
--
ALTER TABLE `atividades_catalogo`
  ADD CONSTRAINT `fk_catalogo_paroquia` FOREIGN KEY (`paroquia_id`) REFERENCES `paroquias` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `atividade_grupos`
--
ALTER TABLE `atividade_grupos`
  ADD CONSTRAINT `fk_ag_atividade` FOREIGN KEY (`atividade_id`) REFERENCES `atividades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ag_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupos_trabalho` (`id`) ON DELETE CASCADE;

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
