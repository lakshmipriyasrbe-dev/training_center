-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: training_center_db
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
-- Table structure for table `tc_company`
--

DROP TABLE IF EXISTS `tc_company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_company` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  `company_id` mediumtext DEFAULT NULL,
  `company_name` mediumtext DEFAULT NULL,
  `company_email` mediumtext DEFAULT NULL,
  `company_mobile` mediumtext DEFAULT NULL,
  `company_address` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_company`
--

LOCK TABLES `tc_company` WRITE;
/*!40000 ALTER TABLE `tc_company` DISABLE KEYS */;
INSERT INTO `tc_company` VALUES (1,'2026-05-08 11:24:14','2026-05-08 11:24:25','YURicXRlQmF1NEsxOE1pNWhQVlZXL1E3TkdTRjhMczZydE9QaW1obXpBYz0=','We Grow Skill Development','wegrowskilldevelopment@gmail.com','8989898988','Sivakasi Main Road\r\nSivakasi',0);
/*!40000 ALTER TABLE `tc_company` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tc_course`
--

DROP TABLE IF EXISTS `tc_course`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  `course_id` mediumtext DEFAULT NULL,
  `course_name` mediumtext DEFAULT NULL,
  `course_duration` mediumtext DEFAULT NULL,
  `course_fee` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_course`
--

LOCK TABLES `tc_course` WRITE;
/*!40000 ALTER TABLE `tc_course` DISABLE KEYS */;
INSERT INTO `tc_course` VALUES (1,'2026-05-08 17:32:42','2026-05-08 17:32:42','RlNGcDJma2xOczhZdTFCd1dQME1uSGswcFg3UjR6NGlmTU9ydVZMbkxoYz0=','PYTHON','4','3000',0),(2,'2026-05-08 17:37:11','2026-05-08 18:03:24','bEdlUDB0cVM5WUVrcEo3U1ArNlhyUlBtTFp1amVPN3h0QVRJYXVPeGwyTT0=','test','4','49848',1),(3,'2026-05-09 09:37:08','2026-05-09 09:37:08','TE9XL3VPUGp3Mkx2Z2lFYkR3YlNNcm0rYTRXc3dQbElPd3VTaUZwcWllZz0=','test','5','5555',0),(4,'2026-05-09 09:37:26','2026-05-09 09:41:26','NVR6MkdzNzRlWWZBa2s0c3ZwQnZsdms2U3JiUmZaZVlvK3IwYUtjdkhsVT0=','dsadas','4','4',1),(5,'2026-05-09 09:57:22','2026-05-09 09:57:22','MVY3WGZsY0tOazhnQmFkSWhSQkM2elc4dTZmQnpwanFCOXh2R1ViSjVYYz0=','php','5','20000',0);
/*!40000 ALTER TABLE `tc_course` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tc_daily_reports`
--

DROP TABLE IF EXISTS `tc_daily_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_daily_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  `report_id` mediumtext DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `report_date` date DEFAULT NULL,
  `activity_details` mediumtext DEFAULT NULL,
  `hours_spent` decimal(4,2) DEFAULT NULL,
  `custom_id` mediumtext DEFAULT NULL,
  `unique_number` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_daily_reports`
--

LOCK TABLES `tc_daily_reports` WRITE;
/*!40000 ALTER TABLE `tc_daily_reports` DISABLE KEYS */;
INSERT INTO `tc_daily_reports` VALUES (1,'2026-05-08 11:41:27','2026-05-08 11:41:27',NULL,2,'2026-05-08','check trainer job',4.00,'T0hXdnlqaStYUTVURVhmRGdaRGNFWEorcFp3TXFIMzV1RG40SmkvRVh4Zz0=','TmtOTFM5b3M5bXlRclF2dFR5SGdlZz09',0),(2,'2026-05-08 11:41:36','2026-05-08 11:41:36',NULL,2,'2026-05-08','czxc',4.00,'YXZBdTNyTVhpZGZPOWlBY0hqYWNUWHc0UXRSMVRKR2g5d21KUHM5bnRYcz0=','N1ZoSkpjWjhtZlFDaUJqbGNVVkI3QT09',0);
/*!40000 ALTER TABLE `tc_daily_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tc_enrollment`
--

DROP TABLE IF EXISTS `tc_enrollment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_enrollment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  `enrollment_id` mediumtext DEFAULT NULL,
  `student_id` mediumtext DEFAULT NULL,
  `student_name` mediumtext DEFAULT NULL,
  `father_spouse_name` mediumtext DEFAULT NULL,
  `address` mediumtext DEFAULT NULL,
  `mobile_number` mediumtext DEFAULT NULL,
  `parent_contact_no` mediumtext DEFAULT NULL,
  `course_id` mediumtext DEFAULT NULL,
  `duration` mediumtext DEFAULT NULL,
  `from_time` mediumtext DEFAULT NULL,
  `to_time` mediumtext DEFAULT NULL,
  `staff_id` mediumtext DEFAULT NULL,
  `fees_type` mediumtext DEFAULT NULL,
  `fees_amount` decimal(10,2) DEFAULT NULL,
  `paid_amount` decimal(10,2) DEFAULT NULL,
  `balance_amount` decimal(10,2) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `doj` date DEFAULT NULL,
  `blood_group` mediumtext DEFAULT NULL,
  `candidate_photo` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_enrollment`
--

LOCK TABLES `tc_enrollment` WRITE;
/*!40000 ALTER TABLE `tc_enrollment` DISABLE KEYS */;
INSERT INTO `tc_enrollment` VALUES (1,'2026-05-09 10:48:12','2026-05-09 10:52:36','bmlGdytYL3F4Y2dtakRYb3U0R3ZRUTZrVnA5aUZueU5nRjQ5YUtuNmY4az0=','dXRXVmFubzZ0bWIyVlZXNTN4ZWNOUT09','Priya','Sellapandian','Sivakasi','7584375847','','MVY3WGZsY0tOazhnQmFkSWhSQkM2elc4dTZmQnpwanFCOXh2R1ViSjVYYz0=','4','12:00','13:00','UUJBWW1TeW8wamlZM1Zuc2dlZXl0Q0VzdDNtVS9WU05BSVdyQUFZZjVJST0=','Full Payment',10000.00,10000.00,0.00,'2001-12-12','2026-05-01','B+','1778303892.jpg',0);
/*!40000 ALTER TABLE `tc_enrollment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tc_logins`
--

DROP TABLE IF EXISTS `tc_logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_logins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login_date_time` datetime DEFAULT NULL,
  `logout_date_time` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_logins`
--

LOCK TABLES `tc_logins` WRITE;
/*!40000 ALTER TABLE `tc_logins` DISABLE KEYS */;
INSERT INTO `tc_logins` VALUES (1,'2026-05-08 11:17:48','2026-05-08 11:36:44',1,0),(2,'2026-05-08 11:37:10','2026-05-08 11:38:25',1,0),(3,'2026-05-08 11:38:34','2026-05-08 11:39:04',2,0),(4,'2026-05-08 11:39:14','2026-05-08 11:46:57',1,0),(5,'2026-05-08 11:40:01','2026-05-08 11:41:50',2,0),(6,'2026-05-08 12:32:35',NULL,1,0),(7,'2026-05-09 09:25:53','2026-05-09 13:20:41',1,0);
/*!40000 ALTER TABLE `tc_logins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tc_roles`
--

DROP TABLE IF EXISTS `tc_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  `role_id` mediumtext DEFAULT NULL,
  `role_name` mediumtext DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_roles`
--

LOCK TABLES `tc_roles` WRITE;
/*!40000 ALTER TABLE `tc_roles` DISABLE KEYS */;
INSERT INTO `tc_roles` VALUES (1,'2026-05-08 11:21:59',NULL,'RFhLeWNBcWxsT3RqVGtBc2orNks2Szc4NjFmNE1LSnZyVytVaEZZaEljQT0=','manager','have done',0),(2,'2026-05-09 09:52:02',NULL,'S1JqWFFsQTRPQ1RoUFdjMGlDSE9YNmJiMmpZN1M2OEg3SVRPdk9rVzdidz0=','trainer','handle training',0);
/*!40000 ALTER TABLE `tc_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tc_staff`
--

DROP TABLE IF EXISTS `tc_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  `staff_id` mediumtext DEFAULT NULL,
  `staff_name` mediumtext DEFAULT NULL,
  `staff_number` mediumtext DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `course_id` mediumtext DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `username` mediumtext DEFAULT NULL,
  `password` mediumtext DEFAULT NULL,
  `address` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_staff`
--

LOCK TABLES `tc_staff` WRITE;
/*!40000 ALTER TABLE `tc_staff` DISABLE KEYS */;
INSERT INTO `tc_staff` VALUES (1,'2026-05-09 09:59:45','2026-05-09 09:59:55','Qy9DUkxUbmd3SldjYm9OY1ZTNmRlV0ZHS0JuR3MwbkN0SmF2cEZZQjJJZz0=','Lakshmi','8989898989',2,'MVY3WGZsY0tOazhnQmFkSWhSQkM2elc4dTZmQnpwanFCOXh2R1ViSjVYYz0=,TE9XL3VPUGp3Mkx2Z2lFYkR3YlNNcm0rYTRXc3dQbElPd3VTaUZwcWllZz0=',12000.00,'Priya','Z0ZJZXhMdVNXZk1wNVhuck5XZytvUT09','Thiruthangal',0),(2,'2026-05-09 10:00:43','2026-05-09 10:00:50','YStVYWU2WHo1VUZoSHpPY0lpOFdOYWF5NmEwZFN0bVVoQmxoOGU3UVJyMD0=','test','7435897483',1,'TE9XL3VPUGp3Mkx2Z2lFYkR3YlNNcm0rYTRXc3dQbElPd3VTaUZwcWllZz0=',15000.00,'test','WE1KSUJGcktIRlI0WEFKbStyZEgyZz09','cxzczxc',1),(3,'2026-05-09 10:02:19','2026-05-09 10:02:19','UUJBWW1TeW8wamlZM1Zuc2dlZXl0Q0VzdDNtVS9WU05BSVdyQUFZZjVJST0=','Kumar','7878787878',2,'RlNGcDJma2xOczhZdTFCd1dQME1uSGswcFg3UjR6NGlmTU9ydVZMbkxoYz0=',12000.00,'Kumar','M1FycEFqNWozVjBMWTB3YWExb1crQT09','ccxzcxzc',0);
/*!40000 ALTER TABLE `tc_staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tc_tasks`
--

DROP TABLE IF EXISTS `tc_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  `task_id` mediumtext DEFAULT NULL,
  `title` mediumtext DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `assigned_by` int(11) DEFAULT NULL,
  `status` mediumtext DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `custom_id` mediumtext DEFAULT NULL,
  `unique_number` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_tasks`
--

LOCK TABLES `tc_tasks` WRITE;
/*!40000 ALTER TABLE `tc_tasks` DISABLE KEYS */;
INSERT INTO `tc_tasks` VALUES (1,'2026-05-08 11:39:41','2026-05-08 11:39:41',NULL,'check trainer','trainer details need to check',2,1,'in_progress','2026-05-08','eGZhby9leGgyUGtER0VmdUovendYSFhZbXNMQmFOczdpWGE2RGJSVEs2az0=','dHN0NTJXRVpQK2hlQ3Iwa0w5dzNodz09',0),(2,'2026-05-09 13:20:35','2026-05-09 13:20:35',NULL,'training concepts','provide training concepts for trainees',0,1,'pending','2026-05-09','Zm95YjYwZ2xWaUJlNmJsY1hrVjZiaGNWQXdQdFdrcUtaQXZVZ2pKUmJubz0=','dFhoNmpNOFFtRzZCOE1jd0czbXJ6Zz09',0);
/*!40000 ALTER TABLE `tc_tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tc_users`
--

DROP TABLE IF EXISTS `tc_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  `user_id` mediumtext DEFAULT NULL,
  `username` mediumtext DEFAULT NULL,
  `password` mediumtext DEFAULT NULL,
  `role` mediumtext DEFAULT NULL,
  `name` mediumtext DEFAULT NULL,
  `email` mediumtext DEFAULT NULL,
  `mobile` mediumtext DEFAULT NULL,
  `custom_id` mediumtext DEFAULT NULL,
  `unique_number` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_users`
--

LOCK TABLES `tc_users` WRITE;
/*!40000 ALTER TABLE `tc_users` DISABLE KEYS */;
INSERT INTO `tc_users` VALUES (1,'2026-05-08 11:17:33','2026-05-08 11:17:33',NULL,'Lakshmi','NENqZjNTNkhLMUZsZHhDbDZ5NFVwUT09','admin','Lakshmi',NULL,'9898989898','MVNnUmQ5Qk9FNFU1c0VxTEV4eHNobWRpWmx0SFZWTkZFN3cyTjc1NGRLYz0=','NXJIR3BCTFkrOFFMdWl3YzA0SUNGdz09',0),(2,'2026-05-08 11:25:03','2026-05-08 11:25:03','Z1IrYkNiaEF3UlZCNFd4WTJiRFJpRStRZU9WTTZydjJUdXZMTk5wL09UZz0=','Selvam','R3JZbEpIREdiK0hwZHdLTVVIN0VLQT09','manager','Selvam',NULL,'8923883822',NULL,'WVFZZTJOL1I0QnZ3ZzFpdk5FdDhwUT09',0);
/*!40000 ALTER TABLE `tc_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-09 13:20:41
