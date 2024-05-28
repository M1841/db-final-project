-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: db_final_project
-- ------------------------------------------------------
-- Server version	8.4.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `_member_of_`
--

DROP TABLE IF EXISTS `_member_of_`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `_member_of_` (
  `user_id` varchar(64) NOT NULL,
  `team_id` varchar(64) NOT NULL,
  KEY `fk_user_member_of_` (`user_id`),
  KEY `fk__member_of_team` (`team_id`),
  CONSTRAINT `fk__member_of_team` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_member_of_` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_member_of_`
--

LOCK TABLES `_member_of_` WRITE;
/*!40000 ALTER TABLE `_member_of_` DISABLE KEYS */;
INSERT INTO `_member_of_` (`user_id`, `team_id`) VALUES ('6369fb7d-66d9-4f31-8bff-c4565835b21e','f3ac1cc1-948f-4111-87ae-2d83d73b99f9'),('6369fb7d-66d9-4f31-8bff-c4565835b21e','4dbce9e6-27da-4a48-8f95-f033696f43fe'),('0190e49c-4706-4726-931c-79c8cd15c47d','f3ac1cc1-948f-4111-87ae-2d83d73b99f9'),('828222ba-250b-4afa-bfc0-4be650f45acc','4dbce9e6-27da-4a48-8f95-f033696f43fe'),('d69740b8-8a54-41f4-9e0b-9c51af28c32c','4b8546a5-35ab-470a-8756-65614ab99d58'),('48894b56-de95-4ec5-b6b1-37db987b4294','426d72a6-2f2e-4872-85f8-46ee6d325ed6'),('6403df2b-d515-45ad-80e7-45695e0ffe52','426d72a6-2f2e-4872-85f8-46ee6d325ed6'),('b97d87d8-b581-4f01-afbf-c240d587647f','4dbce9e6-27da-4a48-8f95-f033696f43fe'),('dd4c288e-0e77-4869-951b-cfea56b6004f','f3ac1cc1-948f-4111-87ae-2d83d73b99f9'),('0190e49c-4706-4726-931c-79c8cd15c47d','4b8546a5-35ab-470a-8756-65614ab99d58'),('828222ba-250b-4afa-bfc0-4be650f45acc','f3ac1cc1-948f-4111-87ae-2d83d73b99f9'),('d69740b8-8a54-41f4-9e0b-9c51af28c32c','f3ac1cc1-948f-4111-87ae-2d83d73b99f9'),('48894b56-de95-4ec5-b6b1-37db987b4294','4b8546a5-35ab-470a-8756-65614ab99d58'),('6403df2b-d515-45ad-80e7-45695e0ffe52','4dbce9e6-27da-4a48-8f95-f033696f43fe'),('b97d87d8-b581-4f01-afbf-c240d587647f','4b8546a5-35ab-470a-8756-65614ab99d58'),('dd4c288e-0e77-4869-951b-cfea56b6004f','4dbce9e6-27da-4a48-8f95-f033696f43fe');
/*!40000 ALTER TABLE `_member_of_` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projects` (
  `id` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL,
  `lead_id` varchar(64) NOT NULL,
  `team_id` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_project_lead` (`lead_id`),
  KEY `fk_project_team` (`team_id`),
  CONSTRAINT `fk_project_lead` FOREIGN KEY (`lead_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_project_team` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` (`id`, `name`, `description`, `lead_id`, `team_id`) VALUES ('07bebef7-c88d-4f9d-8232-0c4a9c761331','erat curabitur','donec posuere metus vitae','48894b56-de95-4ec5-b6b1-37db987b4294','426d72a6-2f2e-4872-85f8-46ee6d325ed6'),('3dd51ead-f750-4498-b68a-da368973bfe3','bibendum','euismod scelerisque quam turpis adipiscing lorem','dd4c288e-0e77-4869-951b-cfea56b6004f','f3ac1cc1-948f-4111-87ae-2d83d73b99f9'),('504530a0-d48d-4e79-92c2-1976cf7ed3d6','neque aenean','mauris enim leo rhoncus sed vestibulum','6369fb7d-66d9-4f31-8bff-c4565835b21e','f3ac1cc1-948f-4111-87ae-2d83d73b99f9'),('5807f5c1-982a-4092-ac6d-7d4bc26647a9','vivamus','elementum nullam varius nulla','6369fb7d-66d9-4f31-8bff-c4565835b21e','4dbce9e6-27da-4a48-8f95-f033696f43fe'),('5fa664e2-65de-4bac-8c55-e1b52b568e34','sit amet','ultrices vel augue vestibulum','b97d87d8-b581-4f01-afbf-c240d587647f','4dbce9e6-27da-4a48-8f95-f033696f43fe'),('a4d57b38-40c9-4036-be53-5c2a410a1957','phasellus','quis tortor id nulla','6403df2b-d515-45ad-80e7-45695e0ffe52','426d72a6-2f2e-4872-85f8-46ee6d325ed6'),('aee661ce-fc66-4b7f-b35a-db4af51baf4e','tristique in','blandit non interdum','d69740b8-8a54-41f4-9e0b-9c51af28c32c','4b8546a5-35ab-470a-8756-65614ab99d58'),('f32c7c26-42e4-4517-bb53-d80d8ed674e0','quam a','sit amet sem fusce consequat','0190e49c-4706-4726-931c-79c8cd15c47d','4dbce9e6-27da-4a48-8f95-f033696f43fe');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `id` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL,
  `status` varchar(64) NOT NULL,
  `priority` varchar(64) NOT NULL,
  `user_id` varchar(64) DEFAULT NULL,
  `project_id` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_works_on_` (`user_id`),
  KEY `fk__part_of_project` (`project_id`),
  CONSTRAINT `fk__part_of_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_works_on_` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks`
--

LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
INSERT INTO `tasks` (`id`, `name`, `description`, `status`, `priority`, `user_id`, `project_id`) VALUES ('11e650a4-f58e-4bc5-889b-6cda1d56ff27','lectus','justo sit amet','In Progress','High',NULL,'504530a0-d48d-4e79-92c2-1976cf7ed3d6'),('1bd9baf7-c2f7-4139-afee-c7f2d90e5027','consectetuer adipiscing','nisl ut volutpat sapien','Completed','High','dd4c288e-0e77-4869-951b-cfea56b6004f','3dd51ead-f750-4498-b68a-da368973bfe3'),('1e1a3e1a-bcae-4809-abde-4c9eb704b8ad','donec','turpis adipiscing lorem','In Progress','High','6369fb7d-66d9-4f31-8bff-c4565835b21e','5807f5c1-982a-4092-ac6d-7d4bc26647a9'),('29698bb3-fd96-42c7-9cc7-b5974b7063ad','potenti nullam','in lectus pellentesque','Completed','High',NULL,'a4d57b38-40c9-4036-be53-5c2a410a1957'),('362fccfb-2838-4958-8544-3f78ca27c0ca','interdum mauris','suscipit a feugiat et','Not Started','Low','d69740b8-8a54-41f4-9e0b-9c51af28c32c','aee661ce-fc66-4b7f-b35a-db4af51baf4e'),('3d068847-f103-4e76-b1f9-3acfddfa300e','suspendisse','euismod scelerisque quam','Not Started','Low',NULL,'aee661ce-fc66-4b7f-b35a-db4af51baf4e'),('3e0ea78f-d405-45e9-8660-00b0a7217809','ornare consequat','vel pede morbi porttitor lorem id','Completed','Low','0190e49c-4706-4726-931c-79c8cd15c47d','f32c7c26-42e4-4517-bb53-d80d8ed674e0'),('3f2f63df-0983-4ccb-b474-0138b79fd0b8','vestibulum ante','luctus nec molestie sed justo pellentesque','In Progress','High','b97d87d8-b581-4f01-afbf-c240d587647f','5fa664e2-65de-4bac-8c55-e1b52b568e34'),('4ea95bcd-3c9f-49a6-957e-9bd1b5f732cf','natoque penatibus','phasellus sit amet','Completed','Low',NULL,'5807f5c1-982a-4092-ac6d-7d4bc26647a9'),('6b29f471-7c95-4d99-87c0-71bab790924a','suspendisse','quis orci nullam molestie nibh','In Progress','Low',NULL,'5fa664e2-65de-4bac-8c55-e1b52b568e34'),('6feac03d-8dd1-4b45-a24a-c9c6cc892f65','nisi eu','erat nulla tempus vivamus in felis','Not Started','High','6369fb7d-66d9-4f31-8bff-c4565835b21e','504530a0-d48d-4e79-92c2-1976cf7ed3d6'),('87386324-8fa5-4983-8cbd-78f61b448d8a','condimentum neque','ultrices mattis odio donec vitae nisi','Not Started','High','48894b56-de95-4ec5-b6b1-37db987b4294','07bebef7-c88d-4f9d-8232-0c4a9c761331'),('87672bf1-c104-4e67-a374-9b0e2554b764','nonummy integer','congue risus semper porta volutpat quam','In Progress','Low',NULL,'07bebef7-c88d-4f9d-8232-0c4a9c761331'),('b2128baf-d7b1-4c78-9ea1-0ee72e90366b','amet','in faucibus orci luctus et','In Progress','Low','6403df2b-d515-45ad-80e7-45695e0ffe52','a4d57b38-40c9-4036-be53-5c2a410a1957'),('d55260ea-a52f-4ab9-bb2c-9e4d13af1b45','praesent id','non mi integer ac','Completed','High',NULL,'f32c7c26-42e4-4517-bb53-d80d8ed674e0'),('ddc8ee6f-10d4-4b85-987c-6167e9428e3d','ac','morbi sem mauris laoreet','Completed','Low',NULL,'3dd51ead-f750-4498-b68a-da368973bfe3');
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teams`
--

DROP TABLE IF EXISTS `teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teams` (
  `id` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teams`
--

LOCK TABLES `teams` WRITE;
/*!40000 ALTER TABLE `teams` DISABLE KEYS */;
INSERT INTO `teams` (`id`, `name`, `description`) VALUES ('426d72a6-2f2e-4872-85f8-46ee6d325ed6','Schulist-Turcotte','Ergonomic dedicated workforce'),('4b8546a5-35ab-470a-8756-65614ab99d58','Green Inc','Total intangible function'),('4dbce9e6-27da-4a48-8f95-f033696f43fe','Block Group','Optimized multi-state info-mediaries'),('f3ac1cc1-948f-4111-87ae-2d83d73b99f9','Gleichner-Legros','Face to face background concept');
/*!40000 ALTER TABLE `teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `name`, `password`) VALUES ('0190e49c-4706-4726-931c-79c8cd15c47d','Lucy','$2a$04$Yw6tFPDZkqrUDAYQsXskleuHoQGFlp8yO/gfZSVhGhA.q7/PA21Z6'),('48894b56-de95-4ec5-b6b1-37db987b4294','Wini','$2a$04$.rJ.GVMAQlt/p7ieyM.CZulIlKbLtvljdN6OJmwMP5/gTgPBh.LKy'),('6369fb7d-66d9-4f31-8bff-c4565835b21e','Mihai','$2y$10$BxG8PxBha/.JmgncW8RqEeJEbKo0zuSxHEXJeWrdBtQ1ecD6THkwC'),('6403df2b-d515-45ad-80e7-45695e0ffe52','Curtis','$2a$04$vGTDnxk3MIa9EA9tm2r1ruw8Nxe7g2v1h1HXZle47K7ykPAzT6mr6'),('828222ba-250b-4afa-bfc0-4be650f45acc','Christoph','$2a$04$ZOhgl9u0rQVIneIHKDlPKeAPE8LDr4f4C7jex2p9X5kHAPZHkxy.C'),('b97d87d8-b581-4f01-afbf-c240d587647f','Davina','$2a$04$RAt/bMnsYcEEQDG.RsU3QOkVn6q9NvX0Zyn3az5.y1lv9KdB3fuXS'),('d69740b8-8a54-41f4-9e0b-9c51af28c32c','Elizabeth','$2a$04$Y9ZwvnP5cfhpOfKTrTkyr.zTe4P2lx3b82E5s3sNcjGA5zVCpS6JW'),('dd4c288e-0e77-4869-951b-cfea56b6004f','Zenia','$2a$04$Ygzk4k.iy6EtLMqKFsv7j.QUOYM/HTQlO8pcHWmkWCtSnZBWXeqkC');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-05-28 14:56:13
