-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: u596929139_calen
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `atividade_evento_inscricoes`
--

DROP TABLE IF EXISTS `atividade_evento_inscricoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `atividade_evento_inscricoes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `evento_item_id` int(10) unsigned NOT NULL,
  `usuario_id` int(10) unsigned NOT NULL,
  `data_inscricao` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `evento_item_id` (`evento_item_id`,`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atividade_evento_inscricoes`
--

LOCK TABLES `atividade_evento_inscricoes` WRITE;
/*!40000 ALTER TABLE `atividade_evento_inscricoes` DISABLE KEYS */;
/*!40000 ALTER TABLE `atividade_evento_inscricoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `atividade_evento_itens`
--

DROP TABLE IF EXISTS `atividade_evento_itens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `atividade_evento_itens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `evento_id` int(10) unsigned NOT NULL,
  `atividade_catalogo_id` int(10) unsigned NOT NULL,
  `ordem` int(10) unsigned NOT NULL DEFAULT 0,
  `data_criacao` timestamp NULL DEFAULT current_timestamp(),
  `ultima_atualizacao` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `evento_id` (`evento_id`,`atividade_catalogo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atividade_evento_itens`
--

LOCK TABLES `atividade_evento_itens` WRITE;
/*!40000 ALTER TABLE `atividade_evento_itens` DISABLE KEYS */;
/*!40000 ALTER TABLE `atividade_evento_itens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `atividade_grupos`
--

DROP TABLE IF EXISTS `atividade_grupos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `atividade_grupos` (
  `atividade_id` int(10) unsigned NOT NULL,
  `grupo_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`atividade_id`,`grupo_id`),
  KEY `fk_ag_atividade` (`atividade_id`),
  KEY `fk_ag_grupo` (`grupo_id`),
  CONSTRAINT `fk_ag_atividade` FOREIGN KEY (`atividade_id`) REFERENCES `atividades` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ag_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupos_trabalho` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atividade_grupos`
--

LOCK TABLES `atividade_grupos` WRITE;
/*!40000 ALTER TABLE `atividade_grupos` DISABLE KEYS */;
/*!40000 ALTER TABLE `atividade_grupos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `atividades`
--

DROP TABLE IF EXISTS `atividades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `atividades` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `cor` varchar(7) DEFAULT '#3b82f6',
  `data_inicio` date NOT NULL,
  `hora_inicio` time DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `hora_fim` time DEFAULT NULL,
  `local_id` int(10) unsigned DEFAULT NULL,
  `tipo_atividade_id` int(10) unsigned DEFAULT NULL,
  `categoria_id` int(10) unsigned DEFAULT NULL,
  `criador_id` int(10) unsigned DEFAULT NULL,
  `paroquia_id` int(10) unsigned DEFAULT NULL,
  `status` varchar(50) DEFAULT 'ativo',
  `restrito` tinyint(1) DEFAULT 0,
  `serie_key` varchar(100) DEFAULT NULL,
  `serie_frequencia` varchar(50) DEFAULT NULL,
  `serie_dias_semana` varchar(100) DEFAULT NULL,
  `serie_data_fim` date DEFAULT NULL,
  `data_criacao` timestamp NULL DEFAULT current_timestamp(),
  `ultima_atualizacao` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_multi_color` tinyint(1) DEFAULT 0,
  `is_flashing` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fk_atividades_paroquia` (`paroquia_id`),
  KEY `fk_atividades_local` (`local_id`),
  KEY `fk_atividades_tipo` (`tipo_atividade_id`),
  CONSTRAINT `fk_atividades_local` FOREIGN KEY (`local_id`) REFERENCES `locais_paroquia` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_atividades_paroquia` FOREIGN KEY (`paroquia_id`) REFERENCES `paroquias` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_atividades_tipo` FOREIGN KEY (`tipo_atividade_id`) REFERENCES `tipos_atividade` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atividades`
--

LOCK TABLES `atividades` WRITE;
/*!40000 ALTER TABLE `atividades` DISABLE KEYS */;
/*!40000 ALTER TABLE `atividades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `atividades_catalogo`
--

DROP TABLE IF EXISTS `atividades_catalogo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `atividades_catalogo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `paroquia_id` int(10) unsigned NOT NULL,
  `nome` varchar(150) NOT NULL,
  `descricao` text DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paroquia_id` (`paroquia_id`,`nome`),
  CONSTRAINT `fk_catalogo_paroquia` FOREIGN KEY (`paroquia_id`) REFERENCES `paroquias` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atividades_catalogo`
--

LOCK TABLES `atividades_catalogo` WRITE;
/*!40000 ALTER TABLE `atividades_catalogo` DISABLE KEYS */;
INSERT INTO `atividades_catalogo` VALUES (1,2,'Publicar Arte Informes','Recepção e apoio aos participantes',1),(2,2,'Feed','Leitores e proclamadores',1),(3,2,'Transmissão Instagram','Equipe de música e canto',1),(4,2,'Telão Projetor','Cobertura, avisos e apoio da PASCOM',1),(5,2,'Criação de Artes','Organização litúrgica',1),(6,2,'Publicar Artes Story','Equipe de música e canto',1);
/*!40000 ALTER TABLE `atividades_catalogo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_throttle`
--

DROP TABLE IF EXISTS `auth_throttle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_throttle` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `scope` varchar(50) NOT NULL,
  `identifier` varchar(191) NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `last_attempt_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `scope` (`scope`,`identifier`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_throttle`
--

LOCK TABLES `auth_throttle` WRITE;
/*!40000 ALTER TABLE `auth_throttle` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_throttle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grupos_trabalho`
--

DROP TABLE IF EXISTS `grupos_trabalho`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grupos_trabalho` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `paroquia_id` int(10) unsigned NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `cor` varchar(7) DEFAULT '#3b82f6',
  `visivel` tinyint(1) DEFAULT 1,
  `ativo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grupos_trabalho`
--

LOCK TABLES `grupos_trabalho` WRITE;
/*!40000 ALTER TABLE `grupos_trabalho` DISABLE KEYS */;
INSERT INTO `grupos_trabalho` VALUES (1,2,'Quermesse','Grupo padrão para novos cadastros (Sala de Espera)','#165bbb',1,1),(2,2,'Pascom Artes','0','#3bf78c',1,1),(3,2,'Pascom Transmissão','Pascom Transmissão','#043920',1,1),(4,2,'Secretaria','0','#bb309d',1,1),(5,2,'Pascom Youtube','Grupo padrão para novos cadastros (Sala de Espera)','#94a3b8',1,0),(6,2,'Acolhida','Grupo padrão para novos cadastros (Sala de Espera)','#2b8619',1,1),(7,2,'Todos','Grupo padrão — todos os membros da paróquia','#94a3b8',1,1),(8,2,'Jornal da Imaculada','0','#61c3db',1,1);
/*!40000 ALTER TABLE `grupos_trabalho` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inscricoes`
--

DROP TABLE IF EXISTS `inscricoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inscricoes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `atividade_id` int(10) unsigned NOT NULL,
  `usuario_id` int(10) unsigned NOT NULL,
  `data_inscricao` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `atividade_id` (`atividade_id`,`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inscricoes`
--

LOCK TABLES `inscricoes` WRITE;
/*!40000 ALTER TABLE `inscricoes` DISABLE KEYS */;
INSERT INTO `inscricoes` VALUES (1,98,7,'2026-04-10 22:27:15'),(2,14,10,'2026-04-08 23:30:26'),(3,13,3,'2026-04-10 06:47:32'),(4,10,10,'2026-04-10 07:18:43'),(5,96,10,'2026-04-10 07:33:35'),(6,95,10,'2026-04-10 07:36:38'),(7,10,7,'2026-04-11 00:28:40'),(8,14,7,'2026-04-11 00:32:56');
/*!40000 ALTER TABLE `inscricoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `locais_paroquia`
--

DROP TABLE IF EXISTS `locais_paroquia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `locais_paroquia` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `paroquia_id` int(10) unsigned NOT NULL,
  `nome_local` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `endereco` varchar(255) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `responsavel` varchar(100) DEFAULT NULL,
  `capacidade` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locais_paroquia`
--

LOCK TABLES `locais_paroquia` WRITE;
/*!40000 ALTER TABLE `locais_paroquia` DISABLE KEYS */;
INSERT INTO `locais_paroquia` VALUES (1,1,'Basilica de São Pedro',NULL,1,'0','0','Padre Sérgio Muniz',0),(3,2,'Capela São Antônio',NULL,1,'0','0','Padre Sérgio Muniz',0),(4,2,'Matriz Nossa Senhora da Conceição',NULL,1,'Rua Virgílio Mârques, 84','(81) 3788-0812','Padre Sérgio Muniz',1000),(5,2,'Capela São Severino Mártir',NULL,1,'Estrada do Caiara, 415 - Iputinga','0','Padre Sérgio Muniz',500),(6,2,'Capela São João Batista',NULL,1,'R. Rezende, 40 - Iputinga,','0','Padre Sérgio Muniz',500),(7,2,'Capela Santa Marta',NULL,1,'2A Travessa Sucupira - Iputinga','0','Padre Sérgio Muniz',400);
/*!40000 ALTER TABLE `locais_paroquia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_alteracoes`
--

DROP TABLE IF EXISTS `log_alteracoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_alteracoes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int(10) unsigned DEFAULT NULL,
  `acao` varchar(255) NOT NULL,
  `tabela_afetada` varchar(100) DEFAULT NULL,
  `registro_id` int(10) unsigned DEFAULT NULL,
  `detalhes_alteracao` text DEFAULT NULL,
  `paroquia_id` int(10) unsigned DEFAULT NULL,
  `ip_origem` varchar(45) DEFAULT NULL,
  `data_hora` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_alteracoes`
--

LOCK TABLES `log_alteracoes` WRITE;
/*!40000 ALTER TABLE `log_alteracoes` DISABLE KEYS */;
INSERT INTO `log_alteracoes` VALUES (1,1,'EDITAR_PAROQUIA','paroquias',2,'{\n    \"antigo\": {\n        \"id\": \"2\",\n        \"nome\": \"Nossa Senhora da Conceição\",\n        \"cidade\": \"Pernambuco\",\n        \"estado\": \"PE\",\n        \"diocese\": \"AOR\",\n        \"ativo\": \"1\",\n        \"data_criacao\": \"2026-04-08 02:33:29\"\n    },\n    \"novo\": {\n        \"nome\": \"Nossa Senhora da Conceição\"\n    }\n}',2,'::1','2026-04-11 23:37:24'),(2,1,'LOGOUT','usuarios',1,'Sessão encerrada pelo usuário',2,'::1','2026-04-11 23:41:49'),(3,1,'LOGIN','usuarios',1,'Autenticacao bem-sucedida',2,'::1','2026-04-11 23:41:52'),(4,1,'LOGIN','usuarios',1,'Autenticacao bem-sucedida',2,'::1','2026-04-12 03:16:54'),(5,1,'LOGOUT','usuarios',1,'Sessão encerrada pelo usuário',2,'::1','2026-04-12 03:29:54'),(6,1,'LOGIN','usuarios',1,'Autenticacao bem-sucedida',2,'::1','2026-04-12 03:29:56'),(7,1,'MOVER_PERFIL','perfis',11,'{\n    \"dir\": \"up\",\n    \"a\": 11,\n    \"b\": 10\n}',2,'::1','2026-04-12 03:31:23'),(8,1,'MOVER_PERFIL','perfis',11,'{\n    \"dir\": \"up\",\n    \"a\": 11,\n    \"b\": 10\n}',2,'::1','2026-04-12 03:31:36'),(9,1,'MOVER_PERFIL','perfis',11,'{\n    \"dir\": \"up\",\n    \"a\": 11,\n    \"b\": 10\n}',2,'::1','2026-04-12 03:31:40'),(10,1,'MOVER_PERFIL','perfis',10,'{\n    \"dir\": \"up\",\n    \"a\": 10,\n    \"b\": 9\n}',2,'::1','2026-04-12 03:31:46'),(11,1,'MOVER_PERFIL','perfis',9,'{\n    \"dir\": \"up\",\n    \"a\": 9,\n    \"b\": 8\n}',2,'::1','2026-04-12 03:31:50'),(12,1,'MOVER_PERFIL','perfis',8,'{\n    \"dir\": \"up\",\n    \"a\": 8,\n    \"b\": 7\n}',2,'::1','2026-04-12 03:31:54'),(13,1,'MOVER_PERFIL','perfis',7,'{\n    \"dir\": \"up\",\n    \"a\": 7,\n    \"b\": 6\n}',2,'::1','2026-04-12 03:31:57'),(14,1,'MOVER_PERFIL','perfis',6,'{\n    \"dir\": \"up\",\n    \"a\": 6,\n    \"b\": 5\n}',2,'::1','2026-04-12 03:32:05'),(15,10,'LOGIN','usuarios',10,'Autenticacao bem-sucedida',2,'::1','2026-04-12 03:35:41'),(16,1,'EDITAR_USUARIO','usuarios',10,'{\n    \"antigo\": {\n        \"id\": \"10\",\n        \"nome\": \"Rangel Silva\",\n        \"email\": \"rangelsjc1@gmail.com\",\n        \"senha\": \"$2y$10$iuuJG1azD\\/biVBm6vkWFpuC0dlcHFbXiTzQfQ.eThOkUzvSzE79d2\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98146-1663\",\n        \"palavra_chave\": \"PASCOM2026\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"1\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"1\",\n        \"data_criacao\": \"2026-04-08 13:48:03\",\n        \"ultimo_login\": \"2026-04-12 00:35:41\",\n        \"data_nascimento\": \"1983-07-18\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_10_b08392877579.jpeg\",\n        \"nivel_acesso\": \"4\",\n        \"perfil_nome\": \"DIACONO\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"1\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    },\n    \"novo\": {\n        \"id\": \"10\",\n        \"nome\": \"Rangel Silva\",\n        \"email\": \"rangelsjc1@gmail.com\",\n        \"senha\": \"$2y$10$iuuJG1azD\\/biVBm6vkWFpuC0dlcHFbXiTzQfQ.eThOkUzvSzE79d2\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98146-1663\",\n        \"palavra_chave\": \"PASCOM2026\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"1\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"1\",\n        \"data_criacao\": \"2026-04-08 13:48:03\",\n        \"ultimo_login\": \"2026-04-12 00:35:41\",\n        \"data_nascimento\": \"1983-07-18\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_10_b08392877579.jpeg\",\n        \"nivel_acesso\": \"4\",\n        \"perfil_nome\": \"DIACONO\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    }\n}',2,'::1','2026-04-12 03:36:14'),(17,10,'LOGOUT','usuarios',10,'Sessão encerrada pelo usuário',2,'::1','2026-04-12 03:36:25'),(18,10,'LOGIN','usuarios',10,'Autenticacao bem-sucedida',2,'::1','2026-04-12 03:36:32'),(19,10,'LOGOUT','usuarios',10,'Sessão encerrada pelo usuário',2,'::1','2026-04-12 03:42:09'),(20,1,'LOGOUT','usuarios',1,'Sessão encerrada pelo usuário',2,'::1','2026-04-12 03:58:21'),(21,1,'LOGIN','usuarios',1,'Autenticacao bem-sucedida',2,'::1','2026-04-12 03:58:26'),(22,10,'LOGIN','usuarios',10,'Autenticacao bem-sucedida',2,'::1','2026-04-12 04:11:30'),(23,10,'EDITAR_USUARIO','usuarios',5,'{\n    \"antigo\": {\n        \"id\": \"5\",\n        \"nome\": \"Diácono Teixeira\",\n        \"email\": \"diacono@gmail.com\",\n        \"senha\": \"$2y$10$UfMfJlKBEB7r6qlA1BU6hu4cF1XRw61Wjw4Q\\/IpQuoatzU6mZkGUS\",\n        \"perfil_id\": \"11\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98628-0580\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": null,\n        \"foto_perfil\": \"img\\/usuarios\\/user_5_02c9ef55625c.jpeg\",\n        \"nivel_acesso\": \"5\",\n        \"perfil_nome\": \"VISITANTE\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"5\",\n        \"nome\": \"Diácono Teixeira\",\n        \"email\": \"diacono@gmail.com\",\n        \"senha\": \"$2y$10$UfMfJlKBEB7r6qlA1BU6hu4cF1XRw61Wjw4Q\\/IpQuoatzU6mZkGUS\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98628-0580\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": null,\n        \"foto_perfil\": \"img\\/usuarios\\/user_5_02c9ef55625c.jpeg\",\n        \"nivel_acesso\": \"5\",\n        \"perfil_nome\": \"DIACONO\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}',2,'::1','2026-04-12 04:13:39'),(24,1,'EDITAR_USUARIO','usuarios',5,'{\n    \"antigo\": {\n        \"id\": \"5\",\n        \"nome\": \"Diácono Teixeira\",\n        \"email\": \"diacono@gmail.com\",\n        \"senha\": \"$2y$10$UfMfJlKBEB7r6qlA1BU6hu4cF1XRw61Wjw4Q\\/IpQuoatzU6mZkGUS\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98628-0580\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": null,\n        \"foto_perfil\": \"img\\/usuarios\\/user_5_02c9ef55625c.jpeg\",\n        \"nivel_acesso\": \"5\",\n        \"perfil_nome\": \"DIACONO\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"5\",\n        \"nome\": \"Diácono Teixeira\",\n        \"email\": \"diacono@gmail.com\",\n        \"senha\": \"$2y$10$UfMfJlKBEB7r6qlA1BU6hu4cF1XRw61Wjw4Q\\/IpQuoatzU6mZkGUS\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98628-0580\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": null,\n        \"foto_perfil\": \"img\\/usuarios\\/user_5_02c9ef55625c.jpeg\",\n        \"nivel_acesso\": \"5\",\n        \"perfil_nome\": \"DIACONO\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}',2,'::1','2026-04-12 04:15:31'),(25,1,'EDITAR_USUARIO','usuarios',7,'{\n    \"antigo\": {\n        \"id\": \"7\",\n        \"nome\": \"Amanda Leal\",\n        \"email\": \"amandaleal@gmail.com\",\n        \"senha\": \"$2y$10$i3YrXwlRKxww3QvuBU1QO.KoBQCBnIbC4WxHpfZQuzecZ1HAyoXdC\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99858-6006\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": \"2026-04-10 21:26:06\",\n        \"data_nascimento\": \"1987-03-11\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_7_9988b8ae3614.jpeg\",\n        \"nivel_acesso\": \"4\",\n        \"perfil_nome\": \"DIACONO\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    },\n    \"novo\": {\n        \"id\": \"7\",\n        \"nome\": \"Amanda Leal\",\n        \"email\": \"amandaleal@gmail.com\",\n        \"senha\": \"$2y$10$i3YrXwlRKxww3QvuBU1QO.KoBQCBnIbC4WxHpfZQuzecZ1HAyoXdC\",\n        \"perfil_id\": \"6\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99858-6006\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": \"2026-04-10 21:26:06\",\n        \"data_nascimento\": \"1987-03-11\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_7_9988b8ae3614.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    }\n}',2,'::1','2026-04-12 04:15:50'),(26,10,'LOGOUT','usuarios',10,'Sessão encerrada pelo usuário',2,'::1','2026-04-12 04:16:05'),(27,7,'LOGIN','usuarios',7,'Autenticacao bem-sucedida',2,'::1','2026-04-12 04:16:10'),(28,7,'EDITAR_USUARIO','usuarios',5,'{\n    \"antigo\": {\n        \"id\": \"5\",\n        \"nome\": \"Diácono Teixeira\",\n        \"email\": \"diacono@gmail.com\",\n        \"senha\": \"$2y$10$UfMfJlKBEB7r6qlA1BU6hu4cF1XRw61Wjw4Q\\/IpQuoatzU6mZkGUS\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98628-0580\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": null,\n        \"foto_perfil\": \"img\\/usuarios\\/user_5_02c9ef55625c.jpeg\",\n        \"nivel_acesso\": \"5\",\n        \"perfil_nome\": \"DIACONO\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"5\",\n        \"nome\": \"Diácono Teixeira\",\n        \"email\": \"diacono@gmail.com\",\n        \"senha\": \"$2y$10$UfMfJlKBEB7r6qlA1BU6hu4cF1XRw61Wjw4Q\\/IpQuoatzU6mZkGUS\",\n        \"perfil_id\": \"6\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98628-0580\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": null,\n        \"foto_perfil\": \"img\\/usuarios\\/user_5_02c9ef55625c.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}',2,'::1','2026-04-12 04:16:53'),(29,1,'EDITAR_USUARIO','usuarios',7,'{\n    \"antigo\": {\n        \"id\": \"7\",\n        \"nome\": \"Amanda Leal\",\n        \"email\": \"amandaleal@gmail.com\",\n        \"senha\": \"$2y$10$i3YrXwlRKxww3QvuBU1QO.KoBQCBnIbC4WxHpfZQuzecZ1HAyoXdC\",\n        \"perfil_id\": \"6\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99858-6006\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": \"2026-04-12 01:16:10\",\n        \"data_nascimento\": \"1987-03-11\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_7_9988b8ae3614.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    },\n    \"novo\": {\n        \"id\": \"7\",\n        \"nome\": \"Amanda Leal\",\n        \"email\": \"amandaleal@gmail.com\",\n        \"senha\": \"$2y$10$i3YrXwlRKxww3QvuBU1QO.KoBQCBnIbC4WxHpfZQuzecZ1HAyoXdC\",\n        \"perfil_id\": \"6\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99858-6006\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": \"2026-04-12 01:16:10\",\n        \"data_nascimento\": \"1987-03-11\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_7_9988b8ae3614.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    }\n}',2,'::1','2026-04-12 04:17:37'),(30,1,'EDITAR_USUARIO','usuarios',10,'{\n    \"antigo\": {\n        \"id\": \"10\",\n        \"nome\": \"Rangel Silva\",\n        \"email\": \"rangelsjc1@gmail.com\",\n        \"senha\": \"$2y$10$iuuJG1azD\\/biVBm6vkWFpuC0dlcHFbXiTzQfQ.eThOkUzvSzE79d2\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98146-1663\",\n        \"palavra_chave\": \"PASCOM2026\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"1\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"1\",\n        \"data_criacao\": \"2026-04-08 13:48:03\",\n        \"ultimo_login\": \"2026-04-12 01:11:30\",\n        \"data_nascimento\": \"1983-07-18\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_10_b08392877579.jpeg\",\n        \"nivel_acesso\": \"4\",\n        \"perfil_nome\": \"DIACONO\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    },\n    \"novo\": {\n        \"id\": \"10\",\n        \"nome\": \"Rangel Silva\",\n        \"email\": \"rangelsjc1@gmail.com\",\n        \"senha\": \"$2y$10$iuuJG1azD\\/biVBm6vkWFpuC0dlcHFbXiTzQfQ.eThOkUzvSzE79d2\",\n        \"perfil_id\": \"6\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98146-1663\",\n        \"palavra_chave\": \"PASCOM2026\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"1\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"1\",\n        \"data_criacao\": \"2026-04-08 13:48:03\",\n        \"ultimo_login\": \"2026-04-12 01:11:30\",\n        \"data_nascimento\": \"1983-07-18\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_10_b08392877579.jpeg\",\n        \"nivel_acesso\": \"4\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    }\n}',2,'::1','2026-04-12 04:27:38'),(31,1,'EDITAR_USUARIO','usuarios',5,'{\n    \"antigo\": {\n        \"id\": \"5\",\n        \"nome\": \"Diácono Teixeira\",\n        \"email\": \"diacono@gmail.com\",\n        \"senha\": \"$2y$10$UfMfJlKBEB7r6qlA1BU6hu4cF1XRw61Wjw4Q\\/IpQuoatzU6mZkGUS\",\n        \"perfil_id\": \"6\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98628-0580\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": null,\n        \"foto_perfil\": \"img\\/usuarios\\/user_5_02c9ef55625c.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"5\",\n        \"nome\": \"Diácono Teixeira\",\n        \"email\": \"diacono@gmail.com\",\n        \"senha\": \"$2y$10$UfMfJlKBEB7r6qlA1BU6hu4cF1XRw61Wjw4Q\\/IpQuoatzU6mZkGUS\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98628-0580\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": null,\n        \"foto_perfil\": \"img\\/usuarios\\/user_5_02c9ef55625c.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"DIACONO\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}',2,'::1','2026-04-12 04:28:01'),(32,1,'EDITAR_USUARIO','usuarios',22,'{\n    \"antigo\": {\n        \"id\": \"22\",\n        \"nome\": \"Gustavo da Silva Correia de Santana\",\n        \"email\": \"gustavocorreia243@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99309-8880\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 23:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2006-10-15\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"22\",\n        \"nome\": \"Gustavo da Silva Correia de Santana\",\n        \"email\": \"gustavocorreia243@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"8\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99309-8880\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 23:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2006-10-15\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE 2\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}',2,'::1','2026-04-12 04:28:38'),(33,7,'LOGOUT','usuarios',7,'Sessão encerrada pelo usuário',2,'::1','2026-04-12 04:39:34'),(34,7,'LOGIN','usuarios',7,'Autenticacao bem-sucedida',2,'::1','2026-04-12 04:39:38'),(35,7,'LOGOUT','usuarios',7,'Sessão encerrada pelo usuário',2,'::1','2026-04-12 04:42:37'),(36,7,'LOGIN','usuarios',7,'Autenticacao bem-sucedida',2,'::1','2026-04-12 04:42:42'),(37,7,'EDITAR_USUARIO','usuarios',7,'{\n    \"antigo\": {\n        \"id\": \"7\",\n        \"nome\": \"Amanda Leal\",\n        \"email\": \"amandaleal@gmail.com\",\n        \"senha\": \"$2y$10$i3YrXwlRKxww3QvuBU1QO.KoBQCBnIbC4WxHpfZQuzecZ1HAyoXdC\",\n        \"perfil_id\": \"6\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99858-6006\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": \"2026-04-12 01:42:42\",\n        \"data_nascimento\": \"1987-03-11\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_7_9988b8ae3614.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    },\n    \"novo\": {\n        \"id\": \"7\",\n        \"nome\": \"Amanda Leal\",\n        \"email\": \"amandaleal@gmail.com\",\n        \"senha\": \"$2y$10$i3YrXwlRKxww3QvuBU1QO.KoBQCBnIbC4WxHpfZQuzecZ1HAyoXdC\",\n        \"perfil_id\": \"6\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 99858-6006\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": \"2026-04-12 01:42:42\",\n        \"data_nascimento\": \"1987-03-11\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_7_9988b8ae3614.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"0\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}',2,'::1','2026-04-12 05:09:26'),(38,1,'EDITAR_USUARIO','usuarios',12,'{\n    \"antigo\": {\n        \"id\": \"12\",\n        \"nome\": \"Alif Victória Alves de Lima\",\n        \"email\": \"vitoriaalif@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 98371-1185\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 23:31:59\",\n        \"ultimo_login\": \"2026-04-09 00:36:13\",\n        \"data_nascimento\": \"2009-01-05\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"12\",\n        \"nome\": \"Alif Victória Alves de Lima\",\n        \"email\": \"vitoriaalif@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 98371-1185\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 23:31:59\",\n        \"ultimo_login\": \"2026-04-09 00:36:13\",\n        \"data_nascimento\": \"2009-01-05\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}',2,'::1','2026-04-12 05:10:21'),(39,1,'EDITAR_USUARIO','usuarios',7,'{\n    \"antigo\": {\n        \"id\": \"7\",\n        \"nome\": \"Amanda Leal\",\n        \"email\": \"amandaleal@gmail.com\",\n        \"senha\": \"$2y$10$i3YrXwlRKxww3QvuBU1QO.KoBQCBnIbC4WxHpfZQuzecZ1HAyoXdC\",\n        \"perfil_id\": \"6\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 99858-6006\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": \"2026-04-12 01:42:42\",\n        \"data_nascimento\": \"1987-03-11\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_7_9988b8ae3614.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"0\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"7\",\n        \"nome\": \"Amanda Leal\",\n        \"email\": \"amandaleal@gmail.com\",\n        \"senha\": \"$2y$10$i3YrXwlRKxww3QvuBU1QO.KoBQCBnIbC4WxHpfZQuzecZ1HAyoXdC\",\n        \"perfil_id\": \"6\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 99858-6006\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"1\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": \"2026-04-12 01:42:42\",\n        \"data_nascimento\": \"1987-03-11\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_7_9988b8ae3614.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}',2,'::1','2026-04-12 05:11:08'),(40,1,'EDITAR_USUARIO','usuarios',3,'{\n    \"antigo\": {\n        \"id\": \"3\",\n        \"nome\": \"Ana Carla de Melo\",\n        \"email\": \"secretariaparoquialiputinga@gmail.com\",\n        \"senha\": \"$2y$10$tQYJobX9dIigeO9bejndkeGx1fQM5pLxhqXS7ioa5GvXgQSobDJGu\",\n        \"perfil_id\": \"3\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"1\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-03-11 22:37:29\",\n        \"ultimo_login\": \"2026-04-10 18:54:43\",\n        \"data_nascimento\": \"1980-11-28\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_3_df51e6ef085c.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"SECRETARIA\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"3\",\n        \"nome\": \"Ana Carla de Melo\",\n        \"email\": \"secretariaparoquialiputinga@gmail.com\",\n        \"senha\": \"$2y$10$tQYJobX9dIigeO9bejndkeGx1fQM5pLxhqXS7ioa5GvXgQSobDJGu\",\n        \"perfil_id\": \"3\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"1\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-03-11 22:37:29\",\n        \"ultimo_login\": \"2026-04-10 18:54:43\",\n        \"data_nascimento\": \"1980-11-28\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_3_df51e6ef085c.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"SECRETARIA\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}',2,'::1','2026-04-12 05:11:31'),(41,1,'EDITAR_USUARIO','usuarios',5,'{\n    \"antigo\": {\n        \"id\": \"5\",\n        \"nome\": \"Diácono Teixeira\",\n        \"email\": \"diacono@gmail.com\",\n        \"senha\": \"$2y$10$UfMfJlKBEB7r6qlA1BU6hu4cF1XRw61Wjw4Q\\/IpQuoatzU6mZkGUS\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98628-0580\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": null,\n        \"foto_perfil\": \"img\\/usuarios\\/user_5_02c9ef55625c.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"DIACONO\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"5\",\n        \"nome\": \"Diácono Teixeira\",\n        \"email\": \"diacono@gmail.com\",\n        \"senha\": \"$2y$10$UfMfJlKBEB7r6qlA1BU6hu4cF1XRw61Wjw4Q\\/IpQuoatzU6mZkGUS\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98628-0580\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": null,\n        \"foto_perfil\": \"img\\/usuarios\\/user_5_02c9ef55625c.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"DIACONO\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}',2,'::1','2026-04-12 05:11:44'),(42,1,'EDITAR_USUARIO','usuarios',10,'{\n    \"antigo\": {\n        \"id\": \"10\",\n        \"nome\": \"Rangel Silva\",\n        \"email\": \"rangelsjc1@gmail.com\",\n        \"senha\": \"$2y$10$iuuJG1azD\\/biVBm6vkWFpuC0dlcHFbXiTzQfQ.eThOkUzvSzE79d2\",\n        \"perfil_id\": \"6\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98146-1663\",\n        \"palavra_chave\": \"PASCOM2026\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"1\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"1\",\n        \"data_criacao\": \"2026-04-08 13:48:03\",\n        \"ultimo_login\": \"2026-04-12 01:11:30\",\n        \"data_nascimento\": \"1983-07-18\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_10_b08392877579.jpeg\",\n        \"nivel_acesso\": \"4\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    },\n    \"novo\": {\n        \"id\": \"10\",\n        \"nome\": \"Rangel Silva\",\n        \"email\": \"rangelsjc1@gmail.com\",\n        \"senha\": \"$2y$10$iuuJG1azD\\/biVBm6vkWFpuC0dlcHFbXiTzQfQ.eThOkUzvSzE79d2\",\n        \"perfil_id\": \"6\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98146-1663\",\n        \"palavra_chave\": \"PASCOM2026\",\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"1\",\n        \"perm_admin_usuarios\": \"1\",\n        \"perm_ver_logs\": \"1\",\n        \"data_criacao\": \"2026-04-08 13:48:03\",\n        \"ultimo_login\": \"2026-04-12 01:11:30\",\n        \"data_nascimento\": \"1983-07-18\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_10_b08392877579.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"1\",\n        \"perm_gerenciar_grupos\": \"1\"\n    }\n}',2,'::1','2026-04-12 05:12:06'),(43,7,'ALTERAR_STATUS_USUARIO','usuarios',6,'{\n    \"antigo\": {\n        \"id\": 6,\n        \"nome\": \"Maria Eduarda\",\n        \"email\": \"mariaeduarda@gmail.com\",\n        \"senha\": \"$2y$10$v2RYQM5fmXBWU5Gjyh0GXuDintU6BDDf4f6JCdQWQjN16BUs2Wdti\",\n        \"perfil_id\": 5,\n        \"paroquia_id\": 2,\n        \"sexo\": \"F\",\n        \"telefone\": \"(81 ) 98362-5306\",\n        \"palavra_chave\": null,\n        \"ativo\": 1,\n        \"perm_criar_eventos\": 1,\n        \"perm_editar_eventos\": 1,\n        \"perm_excluir_eventos\": 1,\n        \"perm_ver_restritos\": 0,\n        \"perm_cadastrar_usuario\": 0,\n        \"perm_admin_usuarios\": 0,\n        \"perm_ver_logs\": 0,\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": \"2026-04-10 21:38:09\",\n        \"data_nascimento\": null,\n        \"foto_perfil\": \"img\\/usuarios\\/user_6_b7012fef1728.jpeg\",\n        \"nivel_acesso\": 5,\n        \"perfil_nome\": \"DIACONO\",\n        \"perm_ver_calendario\": 1,\n        \"perm_admin_sistema\": 0,\n        \"perm_gerenciar_catalogo\": 0,\n        \"perm_gerenciar_grupos\": 0\n    },\n    \"novo\": {\n        \"id\": \"6\",\n        \"nome\": \"Maria Eduarda\",\n        \"email\": \"mariaeduarda@gmail.com\",\n        \"senha\": \"$2y$10$v2RYQM5fmXBWU5Gjyh0GXuDintU6BDDf4f6JCdQWQjN16BUs2Wdti\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81 ) 98362-5306\",\n        \"palavra_chave\": null,\n        \"ativo\": \"0\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": \"2026-04-10 21:38:09\",\n        \"data_nascimento\": null,\n        \"foto_perfil\": \"img\\/usuarios\\/user_6_b7012fef1728.jpeg\",\n        \"nivel_acesso\": \"5\",\n        \"perfil_nome\": \"DIACONO\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}',2,'::1','2026-04-12 05:13:06'),(44,7,'ALTERAR_STATUS_USUARIO','usuarios',8,'{\n    \"antigo\": {\n        \"id\": 8,\n        \"nome\": \"Gabriel Bonfin\",\n        \"email\": \"gabriel@gmail.com\",\n        \"senha\": \"$2y$10$vsVhp60bGkJyCfIeMKT.be3IrkW5wwwTjYqcU2FrAhAbIzJ4YgEGm\",\n        \"perfil_id\": 6,\n        \"paroquia_id\": 2,\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99693-4222\",\n        \"palavra_chave\": null,\n        \"ativo\": 1,\n        \"perm_criar_eventos\": 1,\n        \"perm_editar_eventos\": 1,\n        \"perm_excluir_eventos\": 1,\n        \"perm_ver_restritos\": 0,\n        \"perm_cadastrar_usuario\": 0,\n        \"perm_admin_usuarios\": 0,\n        \"perm_ver_logs\": 0,\n        \"data_criacao\": \"2026-04-08 03:34:04\",\n        \"ultimo_login\": \"2026-04-08 21:30:00\",\n        \"data_nascimento\": \"1997-05-12\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_8_24dc5ad775cd.jpeg\",\n        \"nivel_acesso\": 5,\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": 1,\n        \"perm_admin_sistema\": 0,\n        \"perm_gerenciar_catalogo\": 0,\n        \"perm_gerenciar_grupos\": 0\n    },\n    \"novo\": {\n        \"id\": \"8\",\n        \"nome\": \"Gabriel Bonfin\",\n        \"email\": \"gabriel@gmail.com\",\n        \"senha\": \"$2y$10$vsVhp60bGkJyCfIeMKT.be3IrkW5wwwTjYqcU2FrAhAbIzJ4YgEGm\",\n        \"perfil_id\": \"6\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99693-4222\",\n        \"palavra_chave\": null,\n        \"ativo\": \"0\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 03:34:04\",\n        \"ultimo_login\": \"2026-04-08 21:30:00\",\n        \"data_nascimento\": \"1997-05-12\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_8_24dc5ad775cd.jpeg\",\n        \"nivel_acesso\": \"5\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}',2,'::1','2026-04-12 05:13:12'),(45,7,'ALTERAR_STATUS_USUARIO','usuarios',11,'{\n    \"antigo\": {\n        \"id\": 11,\n        \"nome\": \"Danusa Maria Silva do Nascimento\",\n        \"email\": \"danusa@gmail.com\",\n        \"senha\": \"$2y$10$lqu1oJFDZxEeGcDwpSZUi.CPOjCBS9Ywxf6INPq9bGY6gFZsoSXxm\",\n        \"perfil_id\": 7,\n        \"paroquia_id\": 2,\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 97317-3773\",\n        \"palavra_chave\": null,\n        \"ativo\": 1,\n        \"perm_criar_eventos\": 0,\n        \"perm_editar_eventos\": 0,\n        \"perm_excluir_eventos\": 0,\n        \"perm_ver_restritos\": 0,\n        \"perm_cadastrar_usuario\": 0,\n        \"perm_admin_usuarios\": 0,\n        \"perm_ver_logs\": 0,\n        \"data_criacao\": \"2026-04-08 22:52:03\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2011-05-11\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": 6,\n        \"perfil_nome\": \"PASCOM AGENTE\",\n        \"perm_ver_calendario\": 1,\n        \"perm_admin_sistema\": 0,\n        \"perm_gerenciar_catalogo\": 0,\n        \"perm_gerenciar_grupos\": 0\n    },\n    \"novo\": {\n        \"id\": \"11\",\n        \"nome\": \"Danusa Maria Silva do Nascimento\",\n        \"email\": \"danusa@gmail.com\",\n        \"senha\": \"$2y$10$lqu1oJFDZxEeGcDwpSZUi.CPOjCBS9Ywxf6INPq9bGY6gFZsoSXxm\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 97317-3773\",\n        \"palavra_chave\": null,\n        \"ativo\": \"0\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 22:52:03\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2011-05-11\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"6\",\n        \"perfil_nome\": \"PASCOM AGENTE\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}',2,'::1','2026-04-12 05:13:15'),(46,7,'ALTERAR_STATUS_USUARIO','usuarios',6,'{\n    \"antigo\": {\n        \"id\": 6,\n        \"nome\": \"Maria Eduarda\",\n        \"email\": \"mariaeduarda@gmail.com\",\n        \"senha\": \"$2y$10$v2RYQM5fmXBWU5Gjyh0GXuDintU6BDDf4f6JCdQWQjN16BUs2Wdti\",\n        \"perfil_id\": 5,\n        \"paroquia_id\": 2,\n        \"sexo\": \"F\",\n        \"telefone\": \"(81 ) 98362-5306\",\n        \"palavra_chave\": null,\n        \"ativo\": 0,\n        \"perm_criar_eventos\": 1,\n        \"perm_editar_eventos\": 1,\n        \"perm_excluir_eventos\": 1,\n        \"perm_ver_restritos\": 0,\n        \"perm_cadastrar_usuario\": 0,\n        \"perm_admin_usuarios\": 0,\n        \"perm_ver_logs\": 0,\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": \"2026-04-10 21:38:09\",\n        \"data_nascimento\": null,\n        \"foto_perfil\": \"img\\/usuarios\\/user_6_b7012fef1728.jpeg\",\n        \"nivel_acesso\": 5,\n        \"perfil_nome\": \"DIACONO\",\n        \"perm_ver_calendario\": 1,\n        \"perm_admin_sistema\": 0,\n        \"perm_gerenciar_catalogo\": 0,\n        \"perm_gerenciar_grupos\": 0\n    },\n    \"novo\": {\n        \"id\": \"6\",\n        \"nome\": \"Maria Eduarda\",\n        \"email\": \"mariaeduarda@gmail.com\",\n        \"senha\": \"$2y$10$v2RYQM5fmXBWU5Gjyh0GXuDintU6BDDf4f6JCdQWQjN16BUs2Wdti\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81 ) 98362-5306\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": \"2026-04-10 21:38:09\",\n        \"data_nascimento\": null,\n        \"foto_perfil\": \"img\\/usuarios\\/user_6_b7012fef1728.jpeg\",\n        \"nivel_acesso\": \"5\",\n        \"perfil_nome\": \"DIACONO\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}',2,'::1','2026-04-12 05:13:21'),(47,7,'ALTERAR_STATUS_USUARIO','usuarios',11,'{\n    \"antigo\": {\n        \"id\": 11,\n        \"nome\": \"Danusa Maria Silva do Nascimento\",\n        \"email\": \"danusa@gmail.com\",\n        \"senha\": \"$2y$10$lqu1oJFDZxEeGcDwpSZUi.CPOjCBS9Ywxf6INPq9bGY6gFZsoSXxm\",\n        \"perfil_id\": 7,\n        \"paroquia_id\": 2,\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 97317-3773\",\n        \"palavra_chave\": null,\n        \"ativo\": 0,\n        \"perm_criar_eventos\": 0,\n        \"perm_editar_eventos\": 0,\n        \"perm_excluir_eventos\": 0,\n        \"perm_ver_restritos\": 0,\n        \"perm_cadastrar_usuario\": 0,\n        \"perm_admin_usuarios\": 0,\n        \"perm_ver_logs\": 0,\n        \"data_criacao\": \"2026-04-08 22:52:03\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2011-05-11\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": 6,\n        \"perfil_nome\": \"PASCOM AGENTE\",\n        \"perm_ver_calendario\": 1,\n        \"perm_admin_sistema\": 0,\n        \"perm_gerenciar_catalogo\": 0,\n        \"perm_gerenciar_grupos\": 0\n    },\n    \"novo\": {\n        \"id\": \"11\",\n        \"nome\": \"Danusa Maria Silva do Nascimento\",\n        \"email\": \"danusa@gmail.com\",\n        \"senha\": \"$2y$10$lqu1oJFDZxEeGcDwpSZUi.CPOjCBS9Ywxf6INPq9bGY6gFZsoSXxm\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 97317-3773\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 22:52:03\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"2011-05-11\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"6\",\n        \"perfil_nome\": \"PASCOM AGENTE\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}',2,'::1','2026-04-12 05:13:28'),(48,7,'ALTERAR_STATUS_USUARIO','usuarios',8,'{\n    \"antigo\": {\n        \"id\": 8,\n        \"nome\": \"Gabriel Bonfin\",\n        \"email\": \"gabriel@gmail.com\",\n        \"senha\": \"$2y$10$vsVhp60bGkJyCfIeMKT.be3IrkW5wwwTjYqcU2FrAhAbIzJ4YgEGm\",\n        \"perfil_id\": 6,\n        \"paroquia_id\": 2,\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99693-4222\",\n        \"palavra_chave\": null,\n        \"ativo\": 0,\n        \"perm_criar_eventos\": 1,\n        \"perm_editar_eventos\": 1,\n        \"perm_excluir_eventos\": 1,\n        \"perm_ver_restritos\": 0,\n        \"perm_cadastrar_usuario\": 0,\n        \"perm_admin_usuarios\": 0,\n        \"perm_ver_logs\": 0,\n        \"data_criacao\": \"2026-04-08 03:34:04\",\n        \"ultimo_login\": \"2026-04-08 21:30:00\",\n        \"data_nascimento\": \"1997-05-12\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_8_24dc5ad775cd.jpeg\",\n        \"nivel_acesso\": 5,\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": 1,\n        \"perm_admin_sistema\": 0,\n        \"perm_gerenciar_catalogo\": 0,\n        \"perm_gerenciar_grupos\": 0\n    },\n    \"novo\": {\n        \"id\": \"8\",\n        \"nome\": \"Gabriel Bonfin\",\n        \"email\": \"gabriel@gmail.com\",\n        \"senha\": \"$2y$10$vsVhp60bGkJyCfIeMKT.be3IrkW5wwwTjYqcU2FrAhAbIzJ4YgEGm\",\n        \"perfil_id\": \"6\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 99693-4222\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"1\",\n        \"perm_editar_eventos\": \"1\",\n        \"perm_excluir_eventos\": \"1\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 03:34:04\",\n        \"ultimo_login\": \"2026-04-08 21:30:00\",\n        \"data_nascimento\": \"1997-05-12\",\n        \"foto_perfil\": \"img\\/usuarios\\/user_8_24dc5ad775cd.jpeg\",\n        \"nivel_acesso\": \"5\",\n        \"perfil_nome\": \"PASCOM ADM\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}',2,'::1','2026-04-12 05:13:31'),(49,1,'EDITAR_USUARIO','usuarios',16,'{\n    \"antigo\": {\n        \"id\": \"16\",\n        \"nome\": \"Cristiane Silva Serejo\",\n        \"email\": \"cristianeserejo797@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 99929-6896\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 23:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"1972-05-03\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"PASCOM AGENTE\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"16\",\n        \"nome\": \"Cristiane Silva Serejo\",\n        \"email\": \"cristianeserejo797@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 99929-6896\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 23:31:59\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": \"1972-05-03\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"4\",\n        \"perfil_nome\": \"PASCOM AGENTE\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}',2,'::1','2026-04-12 05:16:19'),(50,7,'ALTERAR_STATUS_USUARIO','usuarios',12,'{\n    \"antigo\": {\n        \"id\": 12,\n        \"nome\": \"Alif Victória Alves de Lima\",\n        \"email\": \"vitoriaalif@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": 7,\n        \"paroquia_id\": 2,\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 98371-1185\",\n        \"palavra_chave\": null,\n        \"ativo\": 1,\n        \"perm_criar_eventos\": 0,\n        \"perm_editar_eventos\": 0,\n        \"perm_excluir_eventos\": 0,\n        \"perm_ver_restritos\": 0,\n        \"perm_cadastrar_usuario\": 0,\n        \"perm_admin_usuarios\": 0,\n        \"perm_ver_logs\": 0,\n        \"data_criacao\": \"2026-04-08 23:31:59\",\n        \"ultimo_login\": \"2026-04-09 00:36:13\",\n        \"data_nascimento\": \"2009-01-05\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": 6,\n        \"perfil_nome\": \"PASCOM AGENTE\",\n        \"perm_ver_calendario\": 1,\n        \"perm_admin_sistema\": 0,\n        \"perm_gerenciar_catalogo\": 0,\n        \"perm_gerenciar_grupos\": 0\n    },\n    \"novo\": {\n        \"id\": \"12\",\n        \"nome\": \"Alif Victória Alves de Lima\",\n        \"email\": \"vitoriaalif@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 98371-1185\",\n        \"palavra_chave\": null,\n        \"ativo\": \"0\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 23:31:59\",\n        \"ultimo_login\": \"2026-04-09 00:36:13\",\n        \"data_nascimento\": \"2009-01-05\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"6\",\n        \"perfil_nome\": \"PASCOM AGENTE\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}',2,'::1','2026-04-12 05:31:03'),(51,7,'ALTERAR_STATUS_USUARIO','usuarios',12,'{\n    \"antigo\": {\n        \"id\": 12,\n        \"nome\": \"Alif Victória Alves de Lima\",\n        \"email\": \"vitoriaalif@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": 7,\n        \"paroquia_id\": 2,\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 98371-1185\",\n        \"palavra_chave\": null,\n        \"ativo\": 0,\n        \"perm_criar_eventos\": 0,\n        \"perm_editar_eventos\": 0,\n        \"perm_excluir_eventos\": 0,\n        \"perm_ver_restritos\": 0,\n        \"perm_cadastrar_usuario\": 0,\n        \"perm_admin_usuarios\": 0,\n        \"perm_ver_logs\": 0,\n        \"data_criacao\": \"2026-04-08 23:31:59\",\n        \"ultimo_login\": \"2026-04-09 00:36:13\",\n        \"data_nascimento\": \"2009-01-05\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": 6,\n        \"perfil_nome\": \"PASCOM AGENTE\",\n        \"perm_ver_calendario\": 1,\n        \"perm_admin_sistema\": 0,\n        \"perm_gerenciar_catalogo\": 0,\n        \"perm_gerenciar_grupos\": 0\n    },\n    \"novo\": {\n        \"id\": \"12\",\n        \"nome\": \"Alif Victória Alves de Lima\",\n        \"email\": \"vitoriaalif@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 98371-1185\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 23:31:59\",\n        \"ultimo_login\": \"2026-04-09 00:36:13\",\n        \"data_nascimento\": \"2009-01-05\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"6\",\n        \"perfil_nome\": \"PASCOM AGENTE\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}',2,'::1','2026-04-12 05:31:04'),(52,7,'LOGOUT','usuarios',7,'Sessão encerrada pelo usuário',2,'::1','2026-04-12 05:33:12'),(53,7,'LOGIN','usuarios',7,'Autenticacao bem-sucedida',2,'::1','2026-04-12 05:33:15'),(54,7,'LOGOUT','usuarios',7,'Sessão encerrada pelo usuário',2,'::1','2026-04-12 05:33:55'),(55,10,'LOGIN','usuarios',10,'Autenticacao bem-sucedida',2,'::1','2026-04-12 05:33:59'),(56,10,'LOGOUT','usuarios',10,'Sessão encerrada pelo usuário',2,'::1','2026-04-12 05:42:55'),(57,10,'LOGIN','usuarios',10,'Autenticacao bem-sucedida',2,'::1','2026-04-12 05:42:58'),(58,10,'LOGOUT','usuarios',10,'Sessão encerrada pelo usuário',2,'::1','2026-04-12 05:43:26'),(59,7,'LOGIN','usuarios',7,'Autenticacao bem-sucedida',2,'::1','2026-04-12 05:43:31'),(60,7,'LOGOUT','usuarios',7,'Sessão encerrada pelo usuário',2,'::1','2026-04-12 05:44:19'),(61,10,'LOGIN','usuarios',10,'Autenticacao bem-sucedida',2,'::1','2026-04-12 05:44:23'),(62,10,'LOGOUT','usuarios',10,'Sessão encerrada pelo usuário',2,'::1','2026-04-12 05:46:21'),(63,10,'LOGIN','usuarios',10,'Autenticacao bem-sucedida',2,'::1','2026-04-12 05:46:25'),(64,1,'EDITAR_USUARIO','usuarios',5,'{\n    \"antigo\": {\n        \"id\": \"5\",\n        \"nome\": \"Diácono Teixeira\",\n        \"email\": \"diacono@gmail.com\",\n        \"senha\": \"$2y$10$UfMfJlKBEB7r6qlA1BU6hu4cF1XRw61Wjw4Q\\/IpQuoatzU6mZkGUS\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98628-0580\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": null,\n        \"foto_perfil\": \"img\\/usuarios\\/user_5_02c9ef55625c.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"DIACONO\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"5\",\n        \"nome\": \"Diácono Teixeira\",\n        \"email\": \"diacono@gmail.com\",\n        \"senha\": \"$2y$10$UfMfJlKBEB7r6qlA1BU6hu4cF1XRw61Wjw4Q\\/IpQuoatzU6mZkGUS\",\n        \"perfil_id\": \"5\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"M\",\n        \"telefone\": \"(81) 98628-0580\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 02:11:32\",\n        \"ultimo_login\": null,\n        \"data_nascimento\": null,\n        \"foto_perfil\": \"img\\/usuarios\\/user_5_02c9ef55625c.jpeg\",\n        \"nivel_acesso\": \"3\",\n        \"perfil_nome\": \"DIACONO\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}',2,'::1','2026-04-12 05:47:31'),(65,10,'LOGOUT','usuarios',10,'Sessão encerrada pelo usuário',2,'::1','2026-04-12 05:49:40'),(66,10,'LOGIN','usuarios',10,'Autenticacao bem-sucedida',2,'::1','2026-04-12 05:49:43'),(67,10,'LOGOUT','usuarios',10,'Sessão encerrada pelo usuário',2,'::1','2026-04-12 06:03:54'),(68,10,'LOGIN','usuarios',10,'Autenticacao bem-sucedida',2,'::1','2026-04-12 06:03:57'),(69,1,'LOGOUT','usuarios',1,'Sessão encerrada pelo usuário',2,'::1','2026-04-12 06:06:50'),(70,1,'LOGIN','usuarios',1,'Autenticacao bem-sucedida',2,'::1','2026-04-12 06:06:52'),(71,1,'EDITAR_PERFIL','perfis',11,'{\n    \"antigo\": {\n        \"id\": 11,\n        \"paroquia_id\": 2,\n        \"nome_perfil\": \"Perfil #11\",\n        \"descricao\": \"\",\n        \"perm_ver_calendario\": 1,\n        \"perm_criar_eventos\": 0,\n        \"perm_editar_eventos\": 0,\n        \"perm_excluir_eventos\": 0,\n        \"perm_ver_restritos\": 0,\n        \"perm_admin_usuarios\": 0,\n        \"perm_admin_sistema\": 0,\n        \"perm_ver_logs\": 0,\n        \"perm_cadastrar_usuario\": 0\n    },\n    \"novo\": {\n        \"id\": 11,\n        \"paroquia_id\": 2,\n        \"nome_perfil\": \"VIGÁRIO\",\n        \"descricao\": null,\n        \"perm_ver_calendario\": 1,\n        \"perm_criar_eventos\": 0,\n        \"perm_editar_eventos\": 0,\n        \"perm_excluir_eventos\": 0,\n        \"perm_ver_restritos\": 0,\n        \"perm_admin_usuarios\": 0,\n        \"perm_admin_sistema\": 0,\n        \"perm_ver_logs\": 0,\n        \"perm_cadastrar_usuario\": 0\n    }\n}',2,'::1','2026-04-12 06:13:28'),(72,1,'EDITAR_PERFIL','perfis',4,'{\n    \"antigo\": {\n        \"id\": 4,\n        \"paroquia_id\": 2,\n        \"nome_perfil\": \"Perfil #4\",\n        \"descricao\": null,\n        \"perm_ver_calendario\": 1,\n        \"perm_criar_eventos\": 1,\n        \"perm_editar_eventos\": 1,\n        \"perm_excluir_eventos\": 1,\n        \"perm_ver_restritos\": 1,\n        \"perm_admin_usuarios\": 0,\n        \"perm_admin_sistema\": 0,\n        \"perm_ver_logs\": 0,\n        \"perm_cadastrar_usuario\": 1\n    },\n    \"novo\": {\n        \"id\": 4,\n        \"paroquia_id\": 2,\n        \"nome_perfil\": \"VIGARIO\",\n        \"descricao\": null,\n        \"perm_ver_calendario\": 1,\n        \"perm_criar_eventos\": 1,\n        \"perm_editar_eventos\": 1,\n        \"perm_excluir_eventos\": 1,\n        \"perm_ver_restritos\": 1,\n        \"perm_admin_usuarios\": 0,\n        \"perm_admin_sistema\": 0,\n        \"perm_ver_logs\": 0,\n        \"perm_cadastrar_usuario\": 1\n    }\n}',2,'::1','2026-04-12 06:13:43'),(73,1,'EDITAR_PERFIL','perfis',9,'{\n    \"antigo\": {\n        \"id\": 9,\n        \"paroquia_id\": 2,\n        \"nome_perfil\": \"Perfil #9\",\n        \"descricao\": \"\",\n        \"perm_ver_calendario\": 1,\n        \"perm_criar_eventos\": 0,\n        \"perm_editar_eventos\": 0,\n        \"perm_excluir_eventos\": 0,\n        \"perm_ver_restritos\": 0,\n        \"perm_admin_usuarios\": 0,\n        \"perm_admin_sistema\": 0,\n        \"perm_ver_logs\": 0,\n        \"perm_cadastrar_usuario\": 0\n    },\n    \"novo\": {\n        \"id\": 9,\n        \"paroquia_id\": 2,\n        \"nome_perfil\": \"CORDENADOR PASTORAL\",\n        \"descricao\": null,\n        \"perm_ver_calendario\": 1,\n        \"perm_criar_eventos\": 0,\n        \"perm_editar_eventos\": 0,\n        \"perm_excluir_eventos\": 0,\n        \"perm_ver_restritos\": 0,\n        \"perm_admin_usuarios\": 0,\n        \"perm_admin_sistema\": 0,\n        \"perm_ver_logs\": 0,\n        \"perm_cadastrar_usuario\": 0\n    }\n}',2,'::1','2026-04-12 06:14:14'),(74,1,'MOVER_PERFIL','perfis',11,'{\n    \"dir\": \"up\",\n    \"a\": 11,\n    \"b\": 10\n}',2,'::1','2026-04-12 06:14:23'),(75,1,'MOVER_PERFIL','perfis',11,'{\n    \"dir\": \"up\",\n    \"a\": 11,\n    \"b\": 10\n}',2,'::1','2026-04-12 06:14:40'),(76,1,'MOVER_PERFIL','perfis',11,'{\n    \"dir\": \"up\",\n    \"a\": 11,\n    \"b\": 10\n}',2,'::1','2026-04-12 06:14:46'),(77,1,'MOVER_PERFIL','perfis',10,'{\n    \"dir\": \"up\",\n    \"a\": 10,\n    \"b\": 9\n}',2,'::1','2026-04-12 06:14:49'),(78,1,'MOVER_PERFIL','perfis',10,'{\n    \"dir\": \"down\",\n    \"a\": 10,\n    \"b\": 11\n}',2,'::1','2026-04-12 06:15:33'),(79,1,'MOVER_PERFIL','perfis',11,'{\n    \"dir\": \"up\",\n    \"a\": 11,\n    \"b\": 10\n}',2,'::1','2026-04-12 06:15:36'),(80,10,'LOGOUT','usuarios',10,'Sessão encerrada pelo usuário',2,'::1','2026-04-12 06:18:54'),(81,10,'LOGIN','usuarios',10,'Autenticacao bem-sucedida',2,'::1','2026-04-12 06:18:59'),(82,1,'EDITAR_PERFIL','perfis',10,'{\n    \"antigo\": {\n        \"id\": 10,\n        \"paroquia_id\": 2,\n        \"nome_perfil\": \"Perfil #10\",\n        \"descricao\": \"\",\n        \"perm_ver_calendario\": 1,\n        \"perm_criar_eventos\": 0,\n        \"perm_editar_eventos\": 0,\n        \"perm_excluir_eventos\": 0,\n        \"perm_ver_restritos\": 0,\n        \"perm_admin_usuarios\": 0,\n        \"perm_admin_sistema\": 0,\n        \"perm_ver_logs\": 0,\n        \"perm_cadastrar_usuario\": 0\n    },\n    \"novo\": {\n        \"id\": 10,\n        \"paroquia_id\": 2,\n        \"nome_perfil\": \"VISITANTE\",\n        \"descricao\": null,\n        \"perm_ver_calendario\": 1,\n        \"perm_criar_eventos\": 0,\n        \"perm_editar_eventos\": 0,\n        \"perm_excluir_eventos\": 0,\n        \"perm_ver_restritos\": 0,\n        \"perm_admin_usuarios\": 0,\n        \"perm_admin_sistema\": 0,\n        \"perm_ver_logs\": 0,\n        \"perm_cadastrar_usuario\": 0\n    }\n}',2,'::1','2026-04-12 06:20:57'),(83,1,'MOVER_PERFIL','perfis',10,'{\n    \"dir\": \"down\",\n    \"a\": 10,\n    \"b\": 11\n}',2,'::1','2026-04-12 06:21:01'),(84,1,'MOVER_PERFIL','perfis',10,'{\n    \"dir\": \"up\",\n    \"a\": 10,\n    \"b\": 9\n}',2,'::1','2026-04-12 06:21:04'),(85,1,'MOVER_PERFIL','perfis',9,'{\n    \"dir\": \"up\",\n    \"a\": 9,\n    \"b\": 8\n}',2,'::1','2026-04-12 06:21:06'),(86,1,'MOVER_PERFIL','perfis',8,'{\n    \"dir\": \"up\",\n    \"a\": 8,\n    \"b\": 7\n}',2,'::1','2026-04-12 06:21:10'),(87,1,'MOVER_PERFIL','perfis',7,'{\n    \"dir\": \"up\",\n    \"a\": 7,\n    \"b\": 6\n}',2,'::1','2026-04-12 06:21:15'),(88,1,'MOVER_PERFIL','perfis',6,'{\n    \"dir\": \"up\",\n    \"a\": 6,\n    \"b\": 5\n}',2,'::1','2026-04-12 06:21:18'),(89,1,'MOVER_PERFIL','perfis',4,'{\n    \"dir\": \"up\",\n    \"a\": 4,\n    \"b\": 3\n}',2,'::1','2026-04-12 06:21:25'),(90,1,'MOVER_PERFIL','perfis',5,'{\n    \"dir\": \"up\",\n    \"a\": 5,\n    \"b\": 4\n}',2,'::1','2026-04-12 06:21:30'),(91,1,'MOVER_PERFIL','perfis',6,'{\n    \"dir\": \"up\",\n    \"a\": 6,\n    \"b\": 5\n}',2,'::1','2026-04-12 06:21:37'),(92,1,'MOVER_PERFIL','perfis',3,'{\n    \"dir\": \"down\",\n    \"a\": 3,\n    \"b\": 4\n}',2,'::1','2026-04-12 06:21:54'),(93,1,'MOVER_PERFIL','perfis',4,'{\n    \"dir\": \"down\",\n    \"a\": 4,\n    \"b\": 5\n}',2,'::1','2026-04-12 06:21:59'),(94,1,'MOVER_PERFIL','perfis',5,'{\n    \"dir\": \"down\",\n    \"a\": 5,\n    \"b\": 6\n}',2,'::1','2026-04-12 06:22:09'),(95,1,'MOVER_PERFIL','perfis',6,'{\n    \"dir\": \"down\",\n    \"a\": 6,\n    \"b\": 7\n}',2,'::1','2026-04-12 06:22:11'),(96,1,'MOVER_PERFIL','perfis',7,'{\n    \"dir\": \"down\",\n    \"a\": 7,\n    \"b\": 8\n}',2,'::1','2026-04-12 06:22:13'),(97,1,'MOVER_PERFIL','perfis',8,'{\n    \"dir\": \"down\",\n    \"a\": 8,\n    \"b\": 9\n}',2,'::1','2026-04-12 06:22:19'),(98,1,'MOVER_PERFIL','perfis',9,'{\n    \"dir\": \"down\",\n    \"a\": 9,\n    \"b\": 10\n}',2,'::1','2026-04-12 06:22:22'),(99,1,'EDITAR_PERFIL','perfis',10,'{\n    \"antigo\": {\n        \"id\": 10,\n        \"paroquia_id\": 2,\n        \"nome_perfil\": \"VIGARIO\",\n        \"descricao\": \"\",\n        \"perm_ver_calendario\": 1,\n        \"perm_criar_eventos\": 1,\n        \"perm_editar_eventos\": 1,\n        \"perm_excluir_eventos\": 1,\n        \"perm_ver_restritos\": 1,\n        \"perm_admin_usuarios\": 0,\n        \"perm_admin_sistema\": 0,\n        \"perm_ver_logs\": 0,\n        \"perm_cadastrar_usuario\": 1\n    },\n    \"novo\": {\n        \"id\": 10,\n        \"paroquia_id\": 2,\n        \"nome_perfil\": \"FIEL DA IGREJA\",\n        \"descricao\": null,\n        \"perm_ver_calendario\": 1,\n        \"perm_criar_eventos\": 1,\n        \"perm_editar_eventos\": 1,\n        \"perm_excluir_eventos\": 1,\n        \"perm_ver_restritos\": 1,\n        \"perm_admin_usuarios\": 0,\n        \"perm_admin_sistema\": 0,\n        \"perm_ver_logs\": 0,\n        \"perm_cadastrar_usuario\": 1\n    }\n}',2,'::1','2026-04-12 06:22:51'),(100,10,'EDITAR_USUARIO','usuarios',12,'{\n    \"antigo\": {\n        \"id\": \"12\",\n        \"nome\": \"Alif Victória Alves de Lima\",\n        \"email\": \"vitoriaalif@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 98371-1185\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 23:31:59\",\n        \"ultimo_login\": \"2026-04-09 00:36:13\",\n        \"data_nascimento\": \"2009-01-05\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"6\",\n        \"perfil_nome\": \"PASCOM AGENTE\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    },\n    \"novo\": {\n        \"id\": \"12\",\n        \"nome\": \"Alif Victória Alves de Lima\",\n        \"email\": \"vitoriaalif@gmail.com\",\n        \"senha\": \"$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd\\/QWU6\",\n        \"perfil_id\": \"7\",\n        \"paroquia_id\": \"2\",\n        \"sexo\": \"F\",\n        \"telefone\": \"(81) 98371-1185\",\n        \"palavra_chave\": null,\n        \"ativo\": \"1\",\n        \"perm_criar_eventos\": \"0\",\n        \"perm_editar_eventos\": \"0\",\n        \"perm_excluir_eventos\": \"0\",\n        \"perm_ver_restritos\": \"0\",\n        \"perm_cadastrar_usuario\": \"0\",\n        \"perm_admin_usuarios\": \"0\",\n        \"perm_ver_logs\": \"0\",\n        \"data_criacao\": \"2026-04-08 23:31:59\",\n        \"ultimo_login\": \"2026-04-09 00:36:13\",\n        \"data_nascimento\": \"2009-01-05\",\n        \"foto_perfil\": null,\n        \"nivel_acesso\": \"6\",\n        \"perfil_nome\": \"PASCOM AGENTE\",\n        \"perm_ver_calendario\": \"1\",\n        \"perm_admin_sistema\": \"0\",\n        \"perm_gerenciar_catalogo\": \"0\",\n        \"perm_gerenciar_grupos\": \"0\"\n    }\n}',2,'::1','2026-04-12 06:23:45');
/*!40000 ALTER TABLE `log_alteracoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paroquias`
--

DROP TABLE IF EXISTS `paroquias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paroquias` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` char(2) DEFAULT NULL,
  `diocese` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `data_criacao` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paroquias`
--

LOCK TABLES `paroquias` WRITE;
/*!40000 ALTER TABLE `paroquias` DISABLE KEYS */;
INSERT INTO `paroquias` VALUES (1,'Igreja Católica Apostólica Romana','Vaticano','IT','Santa Sé',1,'2026-03-12 01:37:29'),(2,'Nossa Senhora da Conceição','Pernambuco','PE','AOR',1,'2026-04-08 05:33:29');
/*!40000 ALTER TABLE `paroquias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perfis`
--

DROP TABLE IF EXISTS `perfis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `perfis` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `paroquia_id` int(10) unsigned NOT NULL DEFAULT 2,
  `nome_perfil` varchar(80) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `perm_ver_calendario` tinyint(1) DEFAULT 1,
  `perm_criar_eventos` tinyint(1) DEFAULT 0,
  `perm_editar_eventos` tinyint(1) DEFAULT 0,
  `perm_excluir_eventos` tinyint(1) DEFAULT 0,
  `perm_ver_restritos` tinyint(1) DEFAULT 0,
  `perm_admin_usuarios` tinyint(1) DEFAULT 0,
  `perm_admin_sistema` tinyint(1) DEFAULT 0,
  `perm_ver_logs` tinyint(1) DEFAULT 0,
  `perm_cadastrar_usuario` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perfis`
--

LOCK TABLES `perfis` WRITE;
/*!40000 ALTER TABLE `perfis` DISABLE KEYS */;
INSERT INTO `perfis` VALUES (2,2,'ADMINISTRADOR PAROQUIAL',NULL,1,1,1,1,1,1,1,1,1),(3,2,'VIGÁRIO','',1,0,0,0,0,0,0,0,0),(4,2,'DIACONO','',1,0,0,0,0,0,0,0,0),(5,2,'SECRETARIA','',1,1,1,1,1,0,0,0,0),(6,2,'PASCOM ADM','',1,1,1,1,0,0,0,0,1),(7,2,'PASCOM AGENTE','',1,1,0,0,0,0,0,0,0),(8,2,'PASCOM AGENTE 2','',1,0,0,0,0,0,0,0,0),(9,2,'CORDENADOR PASTORAL','',1,0,0,0,0,0,0,0,0),(10,2,'FIEL DA IGREJA',NULL,1,1,1,1,1,0,0,0,1),(11,2,'VISITANTE','',1,0,0,0,0,0,0,0,0);
/*!40000 ALTER TABLE `perfis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tabelaperfil`
--

DROP TABLE IF EXISTS `tabelaperfil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tabelaperfil` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `paroquia_id` int(10) unsigned NOT NULL DEFAULT 2,
  `nome_perfil` varchar(80) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `perm_ver_calendario` tinyint(1) DEFAULT 1,
  `perm_criar_eventos` tinyint(1) DEFAULT 0,
  `perm_editar_eventos` tinyint(1) DEFAULT 0,
  `perm_excluir_eventos` tinyint(1) DEFAULT 0,
  `perm_ver_restritos` tinyint(1) DEFAULT 0,
  `perm_admin_usuarios` tinyint(1) DEFAULT 0,
  `perm_admin_sistema` tinyint(1) DEFAULT 0,
  `perm_ver_logs` tinyint(1) DEFAULT 0,
  `perm_cadastrar_usuario` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tabelaperfil`
--

LOCK TABLES `tabelaperfil` WRITE;
/*!40000 ALTER TABLE `tabelaperfil` DISABLE KEYS */;
INSERT INTO `tabelaperfil` VALUES (2,2,'ADMINISTRADOR PAROQUIAL',NULL,1,1,1,1,1,1,1,1,1),(3,2,'SECRETARIA',NULL,1,1,1,1,1,0,0,0,0),(4,2,'Perfil #4',NULL,1,1,1,1,1,0,0,0,1),(5,2,'DIACONO','',1,0,0,0,0,0,0,0,0),(6,2,'PASCOM ADM','',1,1,1,1,0,0,0,0,1),(7,2,'PASCOM AGENTE','',1,1,0,0,0,0,0,0,0),(8,2,'PASCOM AGENTE 2','',1,0,0,0,0,0,0,0,0),(9,2,'Perfil #9','',1,0,0,0,0,0,0,0,0),(10,2,'Perfil #10','',1,0,0,0,0,0,0,0,0),(11,2,'VIGÁRIO',NULL,1,0,0,0,0,0,0,0,0);
/*!40000 ALTER TABLE `tabelaperfil` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_atividade`
--

DROP TABLE IF EXISTS `tipos_atividade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipos_atividade` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `paroquia_id` int(10) unsigned DEFAULT NULL,
  `nome_tipo` varchar(100) NOT NULL,
  `cor` varchar(7) DEFAULT NULL,
  `icone` varchar(50) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_atividade`
--

LOCK TABLES `tipos_atividade` WRITE;
/*!40000 ALTER TABLE `tipos_atividade` DISABLE KEYS */;
INSERT INTO `tipos_atividade` VALUES (1,2,'Celebração','#9333ea','🙏','Test description'),(2,2,'Evento Social','#db2777','🎨','Test description'),(3,2,'Formação','#2563eb','⛪','Test description'),(4,2,'Reunião','#059669','📖','Test description'),(5,2,'Quermesse','#059669','🍷','Test description'),(6,2,'Festa','#059669','🔥','Test description'),(7,2,'Programar Meta business','#059669','🖥️','Test description');
/*!40000 ALTER TABLE `tipos_atividade` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario_grupos`
--

DROP TABLE IF EXISTS `usuario_grupos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuario_grupos` (
  `usuario_id` int(10) unsigned NOT NULL,
  `grupo_id` int(10) unsigned NOT NULL,
  `paroquia_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`usuario_id`,`grupo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario_grupos`
--

LOCK TABLES `usuario_grupos` WRITE;
/*!40000 ALTER TABLE `usuario_grupos` DISABLE KEYS */;
INSERT INTO `usuario_grupos` VALUES (1,1,2),(1,2,2),(1,3,2),(1,4,2),(1,5,1),(1,6,2),(1,7,2),(1,8,2),(1,9,1),(1,10,1),(2,4,2),(2,7,2),(3,4,2),(3,7,2),(5,4,2),(5,7,2),(6,1,2),(6,2,2),(6,7,2),(7,1,2),(7,2,2),(7,3,2),(7,5,2),(7,7,2),(7,8,2),(8,1,2),(8,7,2),(10,1,2),(10,2,2),(10,3,2),(10,4,2),(10,6,2),(10,7,2),(10,8,2),(11,1,2),(11,7,2),(12,1,2),(12,7,2),(13,1,2),(13,7,2),(14,1,2),(14,7,2),(15,1,2),(15,7,2),(16,1,2),(16,7,2),(17,1,2),(17,7,2),(18,1,2),(18,7,2),(19,1,2),(19,7,2),(20,1,2),(20,7,2),(21,7,2),(22,1,2),(22,7,2),(23,1,2),(23,7,2),(25,9,1),(26,9,1),(27,9,1),(28,9,1),(29,9,1),(31,9,1);
/*!40000 ALTER TABLE `usuario_grupos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil_id` int(10) unsigned DEFAULT NULL,
  `paroquia_id` int(10) unsigned DEFAULT NULL,
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
  `perm_gerenciar_grupos` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_usuario_perfil` (`perfil_id`),
  KEY `fk_usuario_paroquia` (`paroquia_id`),
  CONSTRAINT `fk_usuario_paroquia` FOREIGN KEY (`paroquia_id`) REFERENCES `paroquias` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_usuario_perfil` FOREIGN KEY (`perfil_id`) REFERENCES `perfis` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Admin Sistema','admin@sistema.com','$2y$10$iMo.UCnR5cHn8SbDxbYpPuQ2VpI07iFss7FjrKJyEfVZwjM.0o9N2',2,2,'M','(81) 99999999','PASCOM2026',1,0,0,0,0,0,0,0,'2026-03-12 01:37:29','2026-04-12 06:06:52','1988-12-14','img/usuarios/user_1_0af8cc25e98c.png',0,'ADMINISTRADOR',0,0,0,0),(2,'Pe. Sérgio Muniz','pesergio@gmail.com','$2y$10$wRo8Qwh6jwHxTbTYfF3bJu068nLyv/5BIcRpC33pBlx.i/wnNUamq',2,2,'M','(81) 99615-8138',NULL,1,1,1,1,1,0,0,0,'2026-03-12 01:37:29','2026-04-10 21:50:13','1988-12-14','img/usuarios/user_2_95df4fe673ee.jpeg',2,'ADMINISTRADOR PAROQUIAL',1,0,0,0),(3,'Ana Carla de Melo','secretariaparoquialiputinga@gmail.com','$2y$10$tQYJobX9dIigeO9bejndkeGx1fQM5pLxhqXS7ioa5GvXgQSobDJGu',3,2,'F','',NULL,1,1,1,1,1,0,0,0,'2026-03-12 01:37:29','2026-04-10 21:54:43','1980-11-28','img/usuarios/user_3_df51e6ef085c.jpeg',2,'VIGÁRIO',1,0,0,0),(5,'Diácono Teixeira','diacono@gmail.com','$2y$10$UfMfJlKBEB7r6qlA1BU6hu4cF1XRw61Wjw4Q/IpQuoatzU6mZkGUS',5,2,'M','(81) 98628-0580',NULL,1,0,0,0,0,0,0,0,'2026-04-08 05:11:32',NULL,NULL,'img/usuarios/user_5_02c9ef55625c.jpeg',3,'SECRETARIA',1,0,0,0),(6,'Maria Eduarda','mariaeduarda@gmail.com','$2y$10$v2RYQM5fmXBWU5Gjyh0GXuDintU6BDDf4f6JCdQWQjN16BUs2Wdti',5,2,'F','(81 ) 98362-5306',NULL,1,1,1,1,0,0,0,0,'2026-04-08 05:11:32','2026-04-11 00:38:09',NULL,'img/usuarios/user_6_b7012fef1728.jpeg',5,'SECRETARIA',1,0,0,0),(7,'Amanda Leal','amandaleal@gmail.com','$2y$10$i3YrXwlRKxww3QvuBU1QO.KoBQCBnIbC4WxHpfZQuzecZ1HAyoXdC',6,2,'F','(81) 99858-6006',NULL,1,1,1,1,0,1,1,0,'2026-04-08 05:11:32','2026-04-12 05:43:31','1987-03-11','img/usuarios/user_7_9988b8ae3614.jpeg',3,'PASCOM ADM',1,0,1,0),(8,'Gabriel Bonfin','gabriel@gmail.com','$2y$10$vsVhp60bGkJyCfIeMKT.be3IrkW5wwwTjYqcU2FrAhAbIzJ4YgEGm',6,2,'M','(81) 99693-4222',NULL,1,1,1,1,0,0,0,0,'2026-04-08 06:34:04','2026-04-09 00:30:00','1997-05-12','img/usuarios/user_8_24dc5ad775cd.jpeg',5,'PASCOM ADM',1,0,0,0),(10,'Rangel Silva','rangelsjc1@gmail.com','$2y$10$iuuJG1azD/biVBm6vkWFpuC0dlcHFbXiTzQfQ.eThOkUzvSzE79d2',6,2,'M','(81) 98146-1663','PASCOM2026',1,1,1,1,0,1,1,1,'2026-04-08 16:48:03','2026-04-12 06:18:59','1983-07-18','img/usuarios/user_10_b08392877579.jpeg',3,'PASCOM ADM',1,0,1,1),(11,'Danusa Maria Silva do Nascimento','danusa@gmail.com','$2y$10$lqu1oJFDZxEeGcDwpSZUi.CPOjCBS9Ywxf6INPq9bGY6gFZsoSXxm',7,2,'F','(81) 97317-3773',NULL,1,0,0,0,0,0,0,0,'2026-04-09 01:52:03',NULL,'2011-05-11',NULL,6,'PASCOM AGENTE',1,0,0,0),(12,'Alif Victória Alves de Lima','vitoriaalif@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',7,2,'F','(81) 98371-1185',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59','2026-04-09 03:36:13','2009-01-05',NULL,6,'PASCOM AGENTE',1,0,0,0),(13,'Eduardo Henrique Almeida Martins','edusertania.dm@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',7,2,'M','(81) 97121-4576',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59',NULL,'1999-11-13',NULL,6,'PASCOM AGENTE',1,0,0,0),(14,'Kátia Keli Pessoa Silva','pessoakatiakeli@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',7,2,'F','(81) 98365-2530',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59',NULL,'1991-07-10',NULL,6,'PASCOM AGENTE',1,0,0,0),(15,'Lucas Ferreira da Silva','lucasferreiradasilvaf42@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',7,2,'M','(81) 98827-2211',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59',NULL,'2009-08-03',NULL,6,'PASCOM AGENTE',1,0,0,0),(16,'Cristiane Silva Serejo','cristianeserejo797@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',7,2,'F','(81) 99929-6896',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59',NULL,'1972-05-03',NULL,6,'PASCOM AGENTE',1,0,0,0),(17,'Lauanny Vitória Guedes Barbosa da Silva','anny.v0p@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',7,2,'F','(81) 98340-7393',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59',NULL,'2007-09-30',NULL,6,'PASCOM AGENTE',1,0,0,0),(18,'Danilo da Silva Medeiros','danilosilvamedeiros19@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',7,2,'M','(81) 99381-2347',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59',NULL,'2008-06-19',NULL,6,'PASCOM AGENTE',1,0,0,0),(19,'Marcos Anthonio Lins Moura Mariano','marcosanthonio111@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',7,2,'M','(81) 99652-0202',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59',NULL,'2007-04-30',NULL,6,'PASCOM AGENTE',1,0,0,0),(20,'Maria Eduarda Vitor Correia','mariaecorreiaa@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',7,2,'F','(81) 99916-8860',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59',NULL,'1997-04-26',NULL,6,'PASCOM AGENTE',1,0,0,0),(21,'Danilo José de Bonfim de Brito','danilojosebomfim14@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',7,2,'M','(81) 98311-4355',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59',NULL,'2004-09-04',NULL,6,'PASCOM AGENTE',1,0,0,0),(22,'Gustavo da Silva Correia de Santana','gustavocorreia243@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',8,2,'M','(81) 99309-8880',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59',NULL,'2006-10-15',NULL,6,'PASCOM AGENTE 2',1,0,0,0),(23,'Kauãne Macena','kauanetaina05@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',7,2,'F','(81) 99455-1241',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59',NULL,'2005-06-23',NULL,6,'PASCOM AGENTE',1,0,0,0),(25,'Padre Jonas (Acesso Adm)','padre@teste.com','$2y$12$pwJtKaRECwdiBygynJbAF.RiNgTS.Jlf2ceT5hFPpBaBtAwAEf8Ru',2,1,'M',NULL,NULL,1,0,0,0,0,0,1,0,'2026-04-09 22:21:44','2026-04-09 23:34:51','2026-04-12',NULL,1,'ADMINISTRADOR PAROQUIAL',0,0,0,0),(26,'Isabela (Pascom - Sem Logs e Admin)','isabela@teste.com','$2y$12$pwJtKaRECwdiBygynJbAF.RiNgTS.Jlf2ceT5hFPpBaBtAwAEf8Ru',5,1,'F',NULL,NULL,1,0,0,0,0,0,0,0,'2026-04-09 22:21:44',NULL,'2026-04-20',NULL,2,'DIACONO',0,0,0,0),(27,'Test User','test@system.com','$2y$10$wQq5/yODLZNuUEFB1ss7O.UqBa9O4xuA3uyl45o2/2Pl191rF6RAa',9,1,'M','11999999999',NULL,1,0,0,0,0,0,0,0,'2026-04-10 20:50:15',NULL,NULL,NULL,3,'COORDENADOR PASTORAL',1,0,0,0),(28,'User To Delete','delete@me.com','$2y$10$AJLdv5Jm2bBKMAXOMaW8IuY8vJCLZ3oHI0IPobR43X2p4dGeS312C',9,1,'M','11988887777',NULL,1,0,0,0,0,0,0,0,'2026-04-10 20:53:14',NULL,NULL,NULL,3,'COORDENADOR PASTORAL',1,0,0,0),(29,'Final Test User Edited','final@test.com','$2y$10$M8QVwxGdgylbCT5X2Q4LU.gtNZIWCPNmkt7EYFGINchPwZ5xCEU9O',9,1,'M','11999999999',NULL,0,0,0,0,0,0,0,0,'2026-04-10 21:00:13',NULL,NULL,NULL,3,'COORDENADOR PASTORAL',1,0,0,0),(31,'Supervisor Test','supervisor@sistema.com','$2y$10$bIWFyEb/M/plg.veSQglKebLg7F0GchZLHcakQ0mvH/KuKQ2b6qTK',9,1,'M','11988887777',NULL,1,0,0,0,0,0,0,0,'2026-04-10 21:10:36','2026-04-10 21:15:13',NULL,NULL,3,'COORDENADOR PASTORAL',1,0,0,0);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-12  3:29:36
