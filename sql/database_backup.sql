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
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atividade_evento_inscricoes`
--

LOCK TABLES `atividade_evento_inscricoes` WRITE;
/*!40000 ALTER TABLE `atividade_evento_inscricoes` DISABLE KEYS */;
INSERT INTO `atividade_evento_inscricoes` VALUES (1,12,7,'2026-04-10 22:27:27'),(12,20,10,'2026-04-10 04:53:39'),(19,26,1,'2026-04-10 06:31:58'),(25,27,7,'2026-04-10 06:47:24'),(40,26,10,'2026-04-10 07:54:33'),(42,29,6,'2026-04-10 16:07:06'),(44,14,7,'2026-04-11 00:27:14'),(45,22,7,'2026-04-11 00:27:28'),(46,30,6,'2026-04-11 00:50:51'),(47,32,6,'2026-04-11 00:51:08');
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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atividade_evento_itens`
--

LOCK TABLES `atividade_evento_itens` WRITE;
/*!40000 ALTER TABLE `atividade_evento_itens` DISABLE KEYS */;
INSERT INTO `atividade_evento_itens` VALUES (2,99,6,1,'2026-04-10 20:46:16','2026-04-10 20:46:16'),(12,100,16,1,'2026-04-10 22:16:15','2026-04-10 22:16:15'),(13,100,15,2,'2026-04-10 22:16:15','2026-04-10 22:16:15'),(14,101,16,1,'2026-04-10 22:16:15','2026-04-10 22:16:15'),(15,101,15,2,'2026-04-10 22:16:15','2026-04-10 22:16:15'),(16,27,12,1,'2026-04-10 22:20:53','2026-04-10 22:20:53'),(17,27,16,2,'2026-04-10 22:20:53','2026-04-10 22:20:53'),(18,13,16,1,'2026-04-10 22:21:19','2026-04-10 22:21:19'),(19,13,15,2,'2026-04-10 22:21:19','2026-04-10 22:21:19'),(20,13,20,3,'2026-04-10 22:21:19','2026-04-10 22:21:19'),(21,13,12,4,'2026-04-10 22:21:19','2026-04-10 22:21:19'),(22,24,12,1,'2026-04-10 22:29:38','2026-04-10 22:29:38'),(23,24,15,2,'2026-04-10 22:29:38','2026-04-10 22:29:38'),(24,24,13,3,'2026-04-10 22:29:38','2026-04-10 22:29:38'),(25,102,2,1,'2026-04-11 00:09:49','2026-04-11 00:09:49'),(26,103,5,1,'2026-04-11 00:10:25','2026-04-11 00:10:25'),(30,105,16,1,'2026-04-11 00:49:45','2026-04-11 00:49:45'),(31,105,19,2,'2026-04-11 00:49:45','2026-04-11 00:49:45'),(32,105,13,3,'2026-04-11 00:49:45','2026-04-11 00:49:45');
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
INSERT INTO `atividade_grupos` VALUES (102,9),(102,10),(103,9),(103,10),(105,1),(105,2),(105,7);
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
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atividades`
--

LOCK TABLES `atividades` WRITE;
/*!40000 ALTER TABLE `atividades` DISABLE KEYS */;
INSERT INTO `atividades` VALUES (2,'Reunião PASCOM','Planejamento de comunicação','b82f6??','2026-03-16','20:00:00','2026-03-16','21:30:00',3,4,NULL,1,1,'ativo',0,NULL,NULL,NULL,'0017-09-19','2026-04-07 19:28:21','2026-04-10 21:05:11',-123,-106),(3,'Catequese Infantil','Encontro semanal','b82f6??','2026-03-17','18:00:00','2026-03-17','19:30:00',3,3,NULL,1,1,'ativo',0,NULL,NULL,NULL,'0017-09-19','2026-04-07 19:28:21','2026-04-10 21:04:45',-123,-106),(5,'Adoração ao Santissimo','','b82f6??','2026-04-09','19:00:00',NULL,'23:59:59',1,1,NULL,1,1,'ativo',0,NULL,NULL,NULL,'0017-09-19','2026-04-08 03:56:02','2026-04-10 21:04:45',-123,-106),(10,'Santa Missa','Missa com o Diacono também','#16a34a','2026-04-12','07:00:00',NULL,'23:59:59',NULL,NULL,NULL,2,2,'ativo',0,NULL,NULL,NULL,'0017-09-19','2026-04-08 20:40:55','2026-04-10 22:29:05',1,0),(13,'2º Tríduo Divina Misericódia','','#ec4899','2026-04-10','18:00:00',NULL,'23:59:59',NULL,NULL,NULL,7,2,'ativo',0,NULL,NULL,NULL,'0017-09-19','2026-04-08 23:07:22','2026-04-10 22:21:19',1,0),(14,'Adoração São J. Batista - Editado','Com  Diácono Teixeira','#16a34a','2026-04-14','00:00:00',NULL,'23:59:59',NULL,NULL,NULL,10,2,'ativo',0,NULL,NULL,NULL,'0017-09-19','2026-04-08 23:29:07','2026-04-10 22:29:28',1,0),(22,'Evento Teste Estilo Premium','','c4899??','2026-04-12','19:00:00',NULL,'23:59:59',1,9,NULL,1,1,'ativo',0,NULL,NULL,NULL,'0017-11-05','2026-04-10 03:17:44','2026-04-10 21:04:45',-123,-128),(24,'teste dia 14','','#f472b6','2026-04-14','18:00:00',NULL,'23:59:59',NULL,NULL,NULL,10,2,'ativo',0,NULL,NULL,NULL,'0017-09-19','2026-04-10 04:49:47','2026-04-10 22:29:38',1,0),(27,'Padre  Missa Oratório','','#f43f5e','2026-04-09','07:00:00',NULL,'23:59:59',NULL,NULL,NULL,2,2,'ativo',0,NULL,NULL,NULL,'0145-09-19','2026-04-10 05:10:36','2026-04-10 22:20:53',1,0),(96,'Santa Missa','','#6366f1','2026-04-11','00:00:00',NULL,'23:59:59',NULL,NULL,NULL,10,2,'ativo',0,NULL,NULL,NULL,'0017-09-19','2026-04-10 07:27:58','2026-04-10 22:24:37',1,0),(98,'Santa Missa','','#f472b6','2026-04-13','17:00:00',NULL,'23:59:59',NULL,NULL,NULL,10,2,'ativo',0,NULL,NULL,NULL,'0017-09-19','2026-04-10 07:27:58','2026-04-10 22:26:55',1,0),(102,'Santa Missa','','#16a34a','2026-04-10',NULL,NULL,NULL,1,9,NULL,1,1,'ativo',0,NULL,NULL,NULL,NULL,'2026-04-11 00:09:49','2026-04-11 00:09:49',0,0),(103,'Santa Missa','','#16a34a','2026-04-10','19:00:00',NULL,NULL,1,9,NULL,1,1,'ativo',0,NULL,NULL,NULL,NULL,'2026-04-11 00:10:25','2026-04-11 00:10:25',0,0),(105,'Enc. Festa da Divina Misericórida','','#f59e0b','2026-04-12','14:00:00',NULL,NULL,NULL,NULL,NULL,7,2,'ativo',0,NULL,NULL,NULL,NULL,'2026-04-11 00:49:22','2026-04-11 00:49:45',1,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `atividades_catalogo`
--

LOCK TABLES `atividades_catalogo` WRITE;
/*!40000 ALTER TABLE `atividades_catalogo` DISABLE KEYS */;
INSERT INTO `atividades_catalogo` VALUES (2,1,'Feed','Leitores e proclamadores',1),(3,1,'Transmissão Instagram','Equipe de música e canto',1),(4,1,'Transmissão Youtube','Recepção e apoio aos participantes',1),(5,1,'Telão Projetor','Cobertura, avisos e apoio da PASCOM',1),(6,1,'Criação de Artes','Organização litúrgica do evento',1),(7,1,'Edição de Artes','Leitores e proclamadores',1),(8,1,'Publicar Artes Story','Equipe de música e canto',1),(9,1,'sP 9','Recepção e apoio aos participantes',1),(12,2,'Feed','Leitores e proclamadores',1),(13,2,'Transmissão Instagram','Equipe de música e canto',1),(15,2,'Telão Projetor','Cobertura, avisos e apoio da PASCOM',1),(16,2,'Criação de Artes','Organização litúrgica',1),(18,2,'Publicar Artes Story','Equipe de música e canto',1),(19,2,'Publicar Arte Informes','Recepção e apoio aos participantes',1),(20,2,'Teste Unico \'Aspas\' 123','Teste de aspas simples para verificar corre',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_throttle`
--

LOCK TABLES `auth_throttle` WRITE;
/*!40000 ALTER TABLE `auth_throttle` DISABLE KEYS */;
INSERT INTO `auth_throttle` VALUES (2,'recovery','recovery|admin@sistema.com|::1',2,NULL,'2026-04-10 17:33:54','2026-04-10 20:33:38','2026-04-10 20:33:54'),(3,'login','login|test@sistema.com|::1',2,NULL,'2026-04-10 18:18:02','2026-04-10 21:14:41','2026-04-10 21:18:02'),(4,'login','login|master@teste.com|::1',2,NULL,'2026-04-09 20:21:09','2026-04-09 23:20:27','2026-04-09 23:21:09'),(6,'login','login|admin@test.com|::1',1,NULL,'2026-04-09 21:25:19','2026-04-10 00:25:19','2026-04-10 00:25:19'),(15,'login','login|admin@paroquia.com|::1',1,NULL,'2026-04-10 04:02:44','2026-04-10 07:02:44','2026-04-10 07:02:44');
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grupos_trabalho`
--

LOCK TABLES `grupos_trabalho` WRITE;
/*!40000 ALTER TABLE `grupos_trabalho` DISABLE KEYS */;
INSERT INTO `grupos_trabalho` VALUES (1,2,'Quermesse','Grupo padrão para novos cadastros (Sala de Espera)','#165bbb',1,1),(2,2,'Pascom Artes','0','#3bf78c',1,1),(3,2,'Pascom Transmissão','Pascom Transmissão','#043920',1,1),(4,2,'Secretaria','0','#bb309d',1,1),(5,1,'Pascom Youtube','Grupo padrão para novos cadastros (Sala de Espera)','#94a3b8',1,0),(6,2,'Acolhida','Grupo padrão para novos cadastros (Sala de Espera)','#2b8619',1,1),(7,2,'Todos','Grupo padrão — todos os membros da paróquia','#94a3b8',1,1),(8,2,'Jornal da Imaculada','0','#61c3db',1,1),(9,1,'Todos','Grupo padrão — todos os membros da paróquia','#3b82f6',1,1),(10,1,'Acolhida','acolhida','#053685',1,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inscricoes`
--

LOCK TABLES `inscricoes` WRITE;
/*!40000 ALTER TABLE `inscricoes` DISABLE KEYS */;
INSERT INTO `inscricoes` VALUES (1,98,7,'2026-04-10 22:27:15'),(13,14,10,'2026-04-08 23:30:26'),(27,13,3,'2026-04-10 06:47:32'),(28,10,10,'2026-04-10 07:18:43'),(29,96,10,'2026-04-10 07:33:35'),(31,95,10,'2026-04-10 07:36:38'),(32,10,7,'2026-04-11 00:28:40'),(33,14,7,'2026-04-11 00:32:56');
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
) ENGINE=InnoDB AUTO_INCREMENT=850 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_alteracoes`
--

LOCK TABLES `log_alteracoes` WRITE;
/*!40000 ALTER TABLE `log_alteracoes` DISABLE KEYS */;
INSERT INTO `log_alteracoes` VALUES (846,1,'LOGOUT','usuarios',1,'Sessão encerrada pelo usuário',2,'::1','2026-04-11 02:31:46'),(847,10,'LOGIN','usuarios',10,'Autenticacao bem-sucedida',2,'::1','2026-04-11 02:32:15'),(848,10,'LOGOUT','usuarios',10,'Sessão encerrada pelo usuário',2,'::1','2026-04-11 03:05:31'),(849,10,'LOGIN','usuarios',10,'Autenticacao bem-sucedida',2,'::1','2026-04-11 03:05:35');
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
INSERT INTO `paroquias` VALUES (1,'Igreja Católica Apostólica Romana','Vaticano','IT','Santa Sé',1,'2026-03-12 01:37:29'),(2,'Paróquia Nossa Senhora da Conceição','Pernambuco','PE','AOR',1,'2026-04-08 05:33:29');
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
  `nome` varchar(50) NOT NULL,
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perfis`
--

LOCK TABLES `perfis` WRITE;
/*!40000 ALTER TABLE `perfis` DISABLE KEYS */;
INSERT INTO `perfis` VALUES (2,'ADMINISTRADOR PAROQUIAL',NULL,1,1,1,1,1,1,1,1,1),(3,'SECRETARIA',NULL,1,1,1,1,1,0,0,0,0),(4,'VIGÁRIO',NULL,1,1,1,1,1,0,0,0,1),(5,'PASCOM ADM',NULL,1,1,1,1,0,0,0,0,1),(6,'PASCOM AGENTE',NULL,1,1,0,0,0,0,0,0,0),(7,'PASCOM AGENTE 2',NULL,1,0,0,0,0,0,0,0,0),(8,'COORDENADOR PASTORAL',NULL,1,0,0,0,0,0,0,0,0),(9,'SEMINARISTA',NULL,1,0,0,0,0,0,0,0,0),(10,'VISITANTE',NULL,1,0,0,0,0,0,0,0,0),(11,'DIACONO',NULL,1,0,0,0,0,0,0,0,0);
/*!40000 ALTER TABLE `perfis` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_atividade`
--

LOCK TABLES `tipos_atividade` WRITE;
/*!40000 ALTER TABLE `tipos_atividade` DISABLE KEYS */;
INSERT INTO `tipos_atividade` VALUES (1,2,'Celebração','#9333ea','🙏','Test description'),(2,2,'Evento Social','#db2777','🎨','Test description'),(3,2,'Formação','#2563eb','⛪','Test description'),(4,2,'Reunião','#059669','📖','Test description'),(5,2,'Quermesse','#059669','🍷','Test description'),(6,2,'Festa','#059669','🔥','Test description'),(7,2,'Programar Meta business','#059669','🖥️','Test description'),(8,1,'Test Category P1','#ff0000','🔥','Test description'),(9,1,'Other Category P1','#00ff00','🙏','Another description');
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
INSERT INTO `usuario_grupos` VALUES (1,1,2),(1,2,2),(1,3,2),(1,4,2),(1,5,1),(1,6,2),(1,7,2),(1,8,2),(1,9,1),(1,10,1),(2,4,2),(2,7,2),(3,7,2),(5,7,2),(6,1,2),(6,2,2),(6,7,2),(7,1,2),(7,2,2),(7,7,2),(8,1,2),(8,7,2),(10,1,2),(10,2,2),(10,3,2),(10,4,2),(10,6,2),(10,7,2),(10,8,2),(11,1,2),(11,7,2),(12,1,2),(12,7,2),(13,1,2),(13,7,2),(14,1,2),(14,7,2),(15,1,2),(15,7,2),(16,1,2),(16,7,2),(17,1,2),(17,7,2),(18,1,2),(18,7,2),(19,1,2),(19,7,2),(20,1,2),(20,7,2),(21,7,2),(22,1,2),(22,7,2),(23,1,2),(23,7,2),(25,9,1),(26,9,1),(27,9,1),(28,9,1),(29,9,1),(31,9,1);
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
INSERT INTO `usuarios` VALUES (1,'Admin Sistema','admin@sistema.com','$2y$10$iMo.UCnR5cHn8SbDxbYpPuQ2VpI07iFss7FjrKJyEfVZwjM.0o9N2',2,2,'M','(81) 99999999','PASCOM2026',1,0,0,0,0,0,0,0,'2026-03-12 01:37:29','2026-04-11 00:04:44','1988-12-14','img/usuarios/user_1_0af8cc25e98c.png',0,'ADMINISTRADOR PAROQUIAL',0,0,0,0),(2,'Pe. Sérgio Muniz','pesergio@gmail.com','$2y$10$wRo8Qwh6jwHxTbTYfF3bJu068nLyv/5BIcRpC33pBlx.i/wnNUamq',2,2,'M','(81) 99615-8138',NULL,1,1,1,1,1,0,0,0,'2026-03-12 01:37:29','2026-04-10 21:50:13','1988-12-14','img/usuarios/user_2_7911de630570.jpeg',3,'ADMINISTRADOR PAROQUIAL',1,0,0,0),(3,'Ana Karla','secretaria@paroquia.com','$2y$10$tQYJobX9dIigeO9bejndkeGx1fQM5pLxhqXS7ioa5GvXgQSobDJGu',3,2,'F','',NULL,1,1,1,1,1,0,0,0,'2026-03-12 01:37:29','2026-04-10 21:54:43',NULL,NULL,3,'SECRETARIA',1,0,0,0),(5,'Diácono Teixeira','diacono@gmail.com','$2y$10$UfMfJlKBEB7r6qlA1BU6hu4cF1XRw61Wjw4Q/IpQuoatzU6mZkGUS',11,2,'M','(81) 98628-0580',NULL,1,0,0,0,0,0,0,0,'2026-04-08 05:11:32',NULL,NULL,'img/usuarios/user_5_02c9ef55625c.jpeg',5,'DIACONO',1,0,0,0),(6,'Maria Eduarda','mariaeduarda@gmail.com','$2y$10$v2RYQM5fmXBWU5Gjyh0GXuDintU6BDDf4f6JCdQWQjN16BUs2Wdti',5,2,'F','(81 ) 98362-5306',NULL,1,1,1,1,0,0,0,0,'2026-04-08 05:11:32','2026-04-11 00:38:09',NULL,'img/usuarios/user_6_b7012fef1728.jpeg',5,'PASCOM ADM',1,0,0,0),(7,'Amanda Leal','amandaleal@gmail.com','$2y$10$i3YrXwlRKxww3QvuBU1QO.KoBQCBnIbC4WxHpfZQuzecZ1HAyoXdC',5,2,'M','(81) 99858-6006',NULL,1,1,1,1,0,0,1,0,'2026-04-08 05:11:32','2026-04-11 00:26:06','1987-03-11','img/usuarios/user_7_9988b8ae3614.jpeg',4,'PASCOM ADM',1,0,1,1),(8,'Gabriel Bonfin','gabriel@gmail.com','$2y$10$vsVhp60bGkJyCfIeMKT.be3IrkW5wwwTjYqcU2FrAhAbIzJ4YgEGm',6,2,'M','(81) 99693-4222',NULL,1,1,1,1,0,0,0,0,'2026-04-08 06:34:04','2026-04-09 00:30:00','1997-05-12','img/usuarios/user_8_24dc5ad775cd.jpeg',5,'PASCOM AGENTE',1,0,0,0),(10,'Rangel Silva','rangelsjc1@gmail.com','$2y$10$iuuJG1azD/biVBm6vkWFpuC0dlcHFbXiTzQfQ.eThOkUzvSzE79d2',5,2,'M','(81) 98146-1663',NULL,1,1,1,1,0,1,1,1,'2026-04-08 16:48:03','2026-04-11 03:05:35','1983-07-18','img/usuarios/user_10_b08392877579.jpeg',4,'PASCOM ADM',1,1,1,1),(11,'Danusa Maria Silva do Nascimento','danusa@gmail.com','$2y$10$lqu1oJFDZxEeGcDwpSZUi.CPOjCBS9Ywxf6INPq9bGY6gFZsoSXxm',7,2,'F','(81) 97317-3773',NULL,1,0,0,0,0,0,0,0,'2026-04-09 01:52:03',NULL,'2011-05-11',NULL,6,'PASCOM AGENTE 2',1,0,0,0),(12,'Alif Victória Alves de Lima','vitoriaalif@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',7,2,'F','(81) 98371-1185',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59','2026-04-09 03:36:13','2009-01-05',NULL,3,'PASCOM AGENTE 2',1,0,0,0),(13,'Eduardo Henrique Almeida Martins','edusertania.dm@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',7,2,'M','(81) 97121-4576',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59',NULL,'1999-11-13',NULL,3,'PASCOM AGENTE 2',1,0,0,0),(14,'Kátia Keli Pessoa Silva','pessoakatiakeli@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',7,2,'F','(81) 98365-2530',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59',NULL,'1991-07-10',NULL,3,'PASCOM AGENTE 2',1,0,0,0),(15,'Lucas Ferreira da Silva','lucasferreiradasilvaf42@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',7,2,'M','(81) 98827-2211',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59',NULL,'2009-08-03',NULL,3,'PASCOM AGENTE 2',1,0,0,0),(16,'Cristiane Silva Serejo','cristianeserejo797@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',7,2,'F','(81) 99929-6896',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59',NULL,'1972-05-03',NULL,3,'PASCOM AGENTE 2',1,0,0,0),(17,'Lauanny Vitória Guedes Barbosa da Silva','anny.v0p@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',7,2,'F','(81) 98340-7393',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59',NULL,'2007-09-30',NULL,3,'PASCOM AGENTE 2',1,0,0,0),(18,'Danilo da Silva Medeiros','danilosilvamedeiros19@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',7,2,'M','(81) 99381-2347',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59',NULL,'2008-06-19',NULL,3,'PASCOM AGENTE 2',1,0,0,0),(19,'Marcos Anthonio Lins Moura Mariano','marcosanthonio111@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',7,2,'M','(81) 99652-0202',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59',NULL,'2007-04-30',NULL,3,'PASCOM AGENTE 2',1,0,0,0),(20,'Maria Eduarda Vitor Correia','mariaecorreiaa@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',7,2,'F','(81) 99916-8860',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59',NULL,'1997-04-26',NULL,3,'PASCOM AGENTE 2',1,0,0,0),(21,'Danilo José de Bonfim de Brito','danilojosebomfim14@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',7,2,'M','(81) 98311-4355',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59',NULL,'2004-09-04',NULL,3,'PASCOM AGENTE 2',1,0,0,0),(22,'Gustavo da Silva Correia de Santana','gustavocorreia243@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',7,2,'M','(81) 99309-8880',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59',NULL,'2006-10-15',NULL,3,'PASCOM AGENTE 2',1,0,0,0),(23,'Kauãne Macena','kauanetaina05@gmail.com','$2y$12$ztdsryI7xcBHNwC91a6aMOeOGx5Pm.GqkvzxyST7sUj8XFYd/QWU6',7,2,'F','(81) 99455-1241',NULL,1,0,0,0,0,0,0,0,'2026-04-09 02:31:59',NULL,'2005-06-23',NULL,3,'PASCOM AGENTE 2',1,0,0,0),(25,'Padre Jonas (Acesso Adm)','padre@teste.com','$2y$12$pwJtKaRECwdiBygynJbAF.RiNgTS.Jlf2ceT5hFPpBaBtAwAEf8Ru',2,1,'M',NULL,NULL,1,0,0,0,0,0,1,0,'2026-04-09 22:21:44','2026-04-09 23:34:51','2026-04-12',NULL,1,NULL,0,0,0,0),(26,'Isabela (Pascom - Sem Logs e Admin)','isabela@teste.com','$2y$12$pwJtKaRECwdiBygynJbAF.RiNgTS.Jlf2ceT5hFPpBaBtAwAEf8Ru',5,1,'F',NULL,NULL,1,0,0,0,0,0,0,0,'2026-04-09 22:21:44',NULL,'2026-04-20',NULL,2,NULL,0,0,0,0),(27,'Test User','test@system.com','$2y$10$wQq5/yODLZNuUEFB1ss7O.UqBa9O4xuA3uyl45o2/2Pl191rF6RAa',9,1,'M','11999999999',NULL,1,0,0,0,0,0,0,0,'2026-04-10 20:50:15',NULL,NULL,NULL,3,'SEMINARISTA',1,0,0,0),(28,'User To Delete','delete@me.com','$2y$10$AJLdv5Jm2bBKMAXOMaW8IuY8vJCLZ3oHI0IPobR43X2p4dGeS312C',9,1,'M','11988887777',NULL,1,0,0,0,0,0,0,0,'2026-04-10 20:53:14',NULL,NULL,NULL,3,'SEMINARISTA',1,0,0,0),(29,'Final Test User Edited','final@test.com','$2y$10$M8QVwxGdgylbCT5X2Q4LU.gtNZIWCPNmkt7EYFGINchPwZ5xCEU9O',9,1,'M','11999999999',NULL,0,0,0,0,0,0,0,0,'2026-04-10 21:00:13',NULL,NULL,NULL,3,'SEMINARISTA',1,0,0,0),(31,'Supervisor Test','supervisor@sistema.com','$2y$10$bIWFyEb/M/plg.veSQglKebLg7F0GchZLHcakQ0mvH/KuKQ2b6qTK',9,1,'M','11988887777',NULL,1,0,0,0,0,0,0,0,'2026-04-10 21:10:36','2026-04-10 21:15:13',NULL,NULL,3,'SEMINARISTA',1,0,0,0);
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

-- Dump completed on 2026-04-11  1:13:20
