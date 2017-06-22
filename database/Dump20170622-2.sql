-- MySQL dump 10.13  Distrib 5.7.18, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: fred
-- ------------------------------------------------------
-- Server version	5.7.18-0ubuntu0.16.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tab_convite`
--

DROP TABLE IF EXISTS `tab_convite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tab_convite` (
  `id_convite` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_convite` int(11) NOT NULL DEFAULT '0',
  `email_convite` varchar(200) DEFAULT NULL,
  `aceito_convite` int(11) NOT NULL DEFAULT '0',
  `dataCriacao_convite` datetime DEFAULT NULL,
  `grupo_convite` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_convite`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tab_convite`
--

LOCK TABLES `tab_convite` WRITE;
/*!40000 ALTER TABLE `tab_convite` DISABLE KEYS */;
INSERT INTO `tab_convite` VALUES (1,0,'teste',0,'2017-06-22 00:00:00',1),(2,0,'xxx',0,'2017-06-22 00:00:00',1),(3,0,'Fred',0,'2017-06-22 00:00:00',1),(4,0,'fsds',0,'2017-06-22 00:00:00',1),(5,0,'fred.fmm@gmail.com',0,'2017-06-22 00:00:00',1),(6,0,'fred.fmm@gmail.com',1,'2017-06-22 00:00:00',1),(7,0,'fred.fmm@gmail.com',1,'2017-06-22 00:00:00',1);
/*!40000 ALTER TABLE `tab_convite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tab_grupo`
--

DROP TABLE IF EXISTS `tab_grupo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tab_grupo` (
  `id_grupo` int(11) NOT NULL AUTO_INCREMENT,
  `nome_grupo` varchar(100) DEFAULT NULL,
  `descricao_grupo` varchar(299) DEFAULT NULL,
  `ativo_grupo` int(11) NOT NULL DEFAULT '0',
  `tipo_grupo` int(11) NOT NULL DEFAULT '0',
  `dataSorteio_grupo` datetime DEFAULT NULL,
  `admin_grupo` int(11) DEFAULT NULL,
  `faixaPreco_grupo` decimal(10,2) NOT NULL DEFAULT '0.00',
  `dataCriacao_grupo` datetime DEFAULT NULL,
  `sorteio_grupo` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_grupo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tab_grupo`
--

LOCK TABLES `tab_grupo` WRITE;
/*!40000 ALTER TABLE `tab_grupo` DISABLE KEYS */;
INSERT INTO `tab_grupo` VALUES (1,'teste21212','teste',1,2,'2017-06-22 00:00:00',1,23.00,'2017-06-08 00:00:00',0);
/*!40000 ALTER TABLE `tab_grupo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tab_listaDesejo`
--

DROP TABLE IF EXISTS `tab_listaDesejo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tab_listaDesejo` (
  `id_listaDesejo` int(11) NOT NULL AUTO_INCREMENT,
  `produto_listaDesejo` int(11) NOT NULL,
  `inativo` varchar(45) NOT NULL,
  PRIMARY KEY (`id_listaDesejo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tab_listaDesejo`
--

LOCK TABLES `tab_listaDesejo` WRITE;
/*!40000 ALTER TABLE `tab_listaDesejo` DISABLE KEYS */;
/*!40000 ALTER TABLE `tab_listaDesejo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tab_mensagem`
--

DROP TABLE IF EXISTS `tab_mensagem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tab_mensagem` (
  `id_mensagem` int(11) NOT NULL,
  `usuario_mensagem` int(11) NOT NULL,
  `usuarioDestino_Mensagem` int(11) NOT NULL,
  `texto_mensagem` varchar(299) DEFAULT NULL,
  `inativo` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_mensagem`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tab_mensagem`
--

LOCK TABLES `tab_mensagem` WRITE;
/*!40000 ALTER TABLE `tab_mensagem` DISABLE KEYS */;
/*!40000 ALTER TABLE `tab_mensagem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tab_produtos`
--

DROP TABLE IF EXISTS `tab_produtos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tab_produtos` (
  `id_produtos` int(11) NOT NULL AUTO_INCREMENT,
  `nome__produtos` varchar(299) DEFAULT NULL,
  `descricao_produtos` varchar(299) DEFAULT NULL,
  `link__produtos` varchar(299) DEFAULT NULL,
  `inativo` int(11) NOT NULL,
  PRIMARY KEY (`id_produtos`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tab_produtos`
--

LOCK TABLES `tab_produtos` WRITE;
/*!40000 ALTER TABLE `tab_produtos` DISABLE KEYS */;
/*!40000 ALTER TABLE `tab_produtos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tab_sorteio`
--

DROP TABLE IF EXISTS `tab_sorteio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tab_sorteio` (
  `id_sorteio` int(11) NOT NULL AUTO_INCREMENT,
  `grupo_sorteio` int(11) NOT NULL,
  `usuario_sorteio` int(11) NOT NULL,
  `usuarioSorteado_sorteio` int(11) NOT NULL,
  PRIMARY KEY (`id_sorteio`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tab_sorteio`
--

LOCK TABLES `tab_sorteio` WRITE;
/*!40000 ALTER TABLE `tab_sorteio` DISABLE KEYS */;
/*!40000 ALTER TABLE `tab_sorteio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tab_usuario`
--

DROP TABLE IF EXISTS `tab_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tab_usuario` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nome_usuario` varchar(100) DEFAULT NULL,
  `senha_usuario` varchar(299) DEFAULT NULL,
  `login_usuario` varchar(100) DEFAULT NULL,
  `email_usuario` varchar(100) DEFAULT NULL,
  `ativo_usuario` int(11) NOT NULL DEFAULT '0',
  `nivelAcesso_usuario` int(11) NOT NULL DEFAULT '0',
  `dataCriacao_usuario` datetime DEFAULT NULL,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tab_usuario`
--

LOCK TABLES `tab_usuario` WRITE;
/*!40000 ALTER TABLE `tab_usuario` DISABLE KEYS */;
INSERT INTO `tab_usuario` VALUES (1,'Admin','$2y$10$1M8GPMTrJzZJe2PhQ9x3c.ZBUTSXOeOfVmV5IoGFNP0HaVB8Ilq3O','admin','fred.fmm@gmail.com',0,3,NULL),(2,'teste2','$2y$10$H7FEc15fhbvuoFsIJ6szCesCR5RzOt3LWurgnHcHAEHWbp2okLCtG','teste','teste@gmail.com',0,2,'2017-06-20 00:00:00'),(3,'teste','$2y$10$KTmU80wSgFxbSRxgpUUgmu8tITE800uctyDry9aneE/wk8IAjRPQO','teste2','teste@mail.com',0,0,'2017-06-21 00:00:00'),(4,'Frederico','$2y$10$FhbXKxtF6eEnvvvINvp27eq1jVW.pu2NMQANRncfd88.nIEbG79lG','fred.fmm','fred.fmm@gmail.com',0,3,'2017-06-22 00:00:00');
/*!40000 ALTER TABLE `tab_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tab_usuarioGrupo`
--

DROP TABLE IF EXISTS `tab_usuarioGrupo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tab_usuarioGrupo` (
  `id_usuarioGrupo` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_usuarioGrupo` int(11) DEFAULT NULL,
  `grupo_usuarioGrupo` int(11) DEFAULT NULL,
  `dataCriacao_usuarioGrupo` datetime DEFAULT NULL,
  `sorteio_usuarioGrupo` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_usuarioGrupo`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tab_usuarioGrupo`
--

LOCK TABLES `tab_usuarioGrupo` WRITE;
/*!40000 ALTER TABLE `tab_usuarioGrupo` DISABLE KEYS */;
INSERT INTO `tab_usuarioGrupo` VALUES (1,1,1,'2017-06-20 00:00:00',0),(2,2,1,'2017-06-21 00:00:00',0);
/*!40000 ALTER TABLE `tab_usuarioGrupo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'fred'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-06-22 17:42:45
