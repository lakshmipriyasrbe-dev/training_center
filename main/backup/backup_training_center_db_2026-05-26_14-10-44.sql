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
-- Table structure for table `tc_attendance`
--

DROP TABLE IF EXISTS `tc_attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  `company_id` mediumtext DEFAULT NULL,
  `attendance_id` mediumtext DEFAULT NULL,
  `attendance_date` date DEFAULT NULL,
  `staff_id` mediumtext DEFAULT NULL,
  `staff_name` mediumtext DEFAULT NULL,
  `staff_number` mediumtext DEFAULT NULL,
  `staff_role` mediumtext DEFAULT NULL,
  `fn_present` mediumtext DEFAULT NULL,
  `an_present` mediumtext DEFAULT NULL,
  `present_code` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_attendance`
--

LOCK TABLES `tc_attendance` WRITE;
/*!40000 ALTER TABLE `tc_attendance` DISABLE KEYS */;
INSERT INTO `tc_attendance` VALUES (1,'2026-05-25 17:19:52','2026-05-25 17:19:52','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','S3F2TVErTGZEMjhvNXZIVHhMT0ZxMFBTYk9qd1FNQkh5aVJMTGxZMXY1dz0=','2026-05-25','R0xLSC83TmJCVThleGVmeURqY21Lek9vNVhOMWRWbEZ6S1JaeThDZDkwND0=','Cheran','9834758943','manager','P','P','PP',0),(2,'2026-05-25 17:19:52','2026-05-25 17:19:52','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','1','2026-05-25','ckNPemN5RnFHSnk0QzJ6U0FpeEUvbjZDd0xWZ1cwb29pb3E0cEYrWVBBaz0=','Subha','9347594354','trainer','P','P','PP',0),(3,'2026-05-25 17:20:02','2026-05-25 17:20:02','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','a1ZteHo5UjhMbXlJREdQQkpheUtxVGd2YTJEVkpTKzduYm14MlBIYml0az0=','2026-05-24','R0xLSC83TmJCVThleGVmeURqY21Lek9vNVhOMWRWbEZ6S1JaeThDZDkwND0=','Cheran','9834758943','manager','A','P','AP',0),(4,'2026-05-25 17:20:02','2026-05-25 17:20:02','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','3','2026-05-24','ckNPemN5RnFHSnk0QzJ6U0FpeEUvbjZDd0xWZ1cwb29pb3E0cEYrWVBBaz0=','Subha','9347594354','trainer','P','P','PP',0);
/*!40000 ALTER TABLE `tc_attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tc_bank`
--

DROP TABLE IF EXISTS `tc_bank`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_bank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  `company_id` mediumtext DEFAULT NULL,
  `bank_id` mediumtext DEFAULT NULL,
  `bank_name` mediumtext DEFAULT NULL,
  `account_number` mediumtext DEFAULT NULL,
  `account_name` mediumtext DEFAULT NULL,
  `branch` mediumtext DEFAULT NULL,
  `ifsc_code` mediumtext DEFAULT NULL,
  `payment_mode` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_bank`
--

LOCK TABLES `tc_bank` WRITE;
/*!40000 ALTER TABLE `tc_bank` DISABLE KEYS */;
INSERT INTO `tc_bank` VALUES (1,'2026-05-25 17:15:58','2026-05-25 17:15:58','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','ZnJFNWRUSldTY3dEcjZ3NzlXSjByNUU3ZTZ1NDVmeFZ0cTExKzZzRTNzMD0=','AXis','8759589659957','Lakshmi','','','YTVpZjFjV21yeWFwSkpGYlFIWEZlZ2JSa2xZMkFLb0dDaUc2QXN1dS9ocz0=',0);
/*!40000 ALTER TABLE `tc_bank` ENABLE KEYS */;
UNLOCK TABLES;

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
  `gst` mediumtext DEFAULT NULL,
  `branch` mediumtext DEFAULT NULL,
  `logo_image` mediumtext DEFAULT NULL,
  `company_details` longtext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_company`
--

LOCK TABLES `tc_company` WRITE;
/*!40000 ALTER TABLE `tc_company` DISABLE KEYS */;
INSERT INTO `tc_company` VALUES (1,'2026-05-23 12:38:05','2026-05-23 12:38:05','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','We Grow Skill Development','wegrowskilldevelopment@gmail.com','9898989898','sivakasi',0,'27ABCDE1234F2Z5','Sivakasi','company_1779520085_6a1152556254d.png','amcyQ1VvZi96bWszckEzRSthVVdXeSttaXh0cXNaa0xWWjFYb3FyYjd0anEyZW4rL3ExZ1Ard0dHSDRXOEdJaUxRN20vSS9TVGhZSmxNVkh0NDhzVzRFYTBqamtVRTJPMkZTWFFNK3NtenlsQkE5Yk9RWDcvNjc3N28vZTJ5Q2FObWVwRzlQbHFUbE1qbnlRcDVYOEIvNHU0NG1UMi9aNDYzWUFkcVpyS3RjPQ=='),(2,'2026-05-22 11:28:26','2026-05-22 11:28:26','MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','We Grow Skill Development','wegrowskilldevelopment@gmail.com','9874999933','Srivilliputhur',0,'27ABCDE1234F2Z4','Srivilliputhur','company_1779429506_6a0ff0822e9f0.png','amcyQ1VvZi96bWszckEzRSthVVdXeSttaXh0cXNaa0xWWjFYb3FyYjd0ZzJDTzlvcmZiNTBjOWxPVms2clNFL3dhOERKTTZ4eDdwMUV2NkpOWmc0VW1ISW80c2FFMWI0UkpvazhmRnVZRllxTjBxS2FTSFBZQVNWQURJaE92c3dLN055Nk9DUm9xZmRLVmM0YjNOL1krazYyTTNER3p6M1NPVEhDRGVza1RqM0NidmZLc2NhcjlTMXlkVkdNditK');
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
  `company_id` mediumtext DEFAULT NULL,
  `course_id` mediumtext DEFAULT NULL,
  `course_name` mediumtext DEFAULT NULL,
  `course_duration` mediumtext DEFAULT NULL,
  `course_fee` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_course`
--

LOCK TABLES `tc_course` WRITE;
/*!40000 ALTER TABLE `tc_course` DISABLE KEYS */;
INSERT INTO `tc_course` VALUES (1,'2026-05-25 15:40:07','2026-05-25 15:40:07','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','VnZScUFGamJwQlphUndRQm1JMGZwdS82Z2k0NVorZ05IdjMxdTlhQ3ZqST0=','php','5','20000',0),(2,'2026-05-25 15:40:18','2026-05-25 15:40:18','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','V0l5T2JQd01IekJuZDl1QndPenloT0NTbnN1QnY4cmlGK0h0QVB2b0MyMD0=','Python','8','30000',0);
/*!40000 ALTER TABLE `tc_course` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tc_course_closure`
--

DROP TABLE IF EXISTS `tc_course_closure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_course_closure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  `company_id` mediumtext DEFAULT NULL,
  `closure_id` mediumtext DEFAULT NULL,
  `closure_date` mediumtext DEFAULT NULL,
  `course_type` mediumtext DEFAULT NULL,
  `student_id` mediumtext DEFAULT NULL,
  `enrollment_id` mediumtext DEFAULT NULL,
  `student_name` mediumtext DEFAULT NULL,
  `course_closed` int(11) DEFAULT 2,
  `certificate_got` int(11) NOT NULL DEFAULT 2,
  `placed` int(11) NOT NULL DEFAULT 2,
  `company_name` mediumtext DEFAULT NULL,
  `company_address` mediumtext DEFAULT NULL,
  `designation` mediumtext DEFAULT NULL,
  `ctc` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_course_closure`
--

LOCK TABLES `tc_course_closure` WRITE;
/*!40000 ALTER TABLE `tc_course_closure` DISABLE KEYS */;
/*!40000 ALTER TABLE `tc_course_closure` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tc_course_enquiry`
--

DROP TABLE IF EXISTS `tc_course_enquiry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_course_enquiry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enquiry_id` varchar(255) DEFAULT NULL,
  `company_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `mobile_number` varchar(255) DEFAULT NULL,
  `degree_completed` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `course_id` varchar(255) DEFAULT NULL,
  `converted_type` varchar(255) DEFAULT 'none',
  `converted_id` varchar(255) DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_course_enquiry`
--

LOCK TABLES `tc_course_enquiry` WRITE;
/*!40000 ALTER TABLE `tc_course_enquiry` DISABLE KEYS */;
INSERT INTO `tc_course_enquiry` VALUES (1,'001/26-27','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','Senthil','8934759834','BSC','Meenakshi Amman, Sivakasi','VnZScUFGamJwQlphUndRQm1JMGZwdS82Z2k0NVorZ05IdjMxdTlhQ3ZqST0=','enrollment','b2xtVGZhWkY0TWhabGFIQzhHTXdnaGlZSGlkMmVFWnh6L2JPUDVaNDk3ST0=',0,'2026-05-25 17:03:53','2026-05-25 17:03:53'),(2,'002/26-27','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','test','8798797979','BCA','Sivakasi','V0l5T2JQd01IekJuZDl1QndPenloT0NTbnN1QnY4cmlGK0h0QVB2b0MyMD0=','enrollment','LzF2cmpxeFEvTGpYcW40RHA4bVVIb0Q3OTFpVkt4bnU3dE82YU91eTFSST0=',0,'2026-05-25 17:12:06','2026-05-25 17:12:06');
/*!40000 ALTER TABLE `tc_course_enquiry` ENABLE KEYS */;
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
  `company_id` mediumtext DEFAULT NULL,
  `report_id` mediumtext DEFAULT NULL,
  `user_id` mediumtext DEFAULT NULL,
  `report_date` date DEFAULT NULL,
  `activity_details` mediumtext DEFAULT NULL,
  `hours_spent` decimal(4,2) DEFAULT NULL,
  `custom_id` mediumtext DEFAULT NULL,
  `unique_number` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_daily_reports`
--

LOCK TABLES `tc_daily_reports` WRITE;
/*!40000 ALTER TABLE `tc_daily_reports` DISABLE KEYS */;
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
  `company_id` mediumtext DEFAULT NULL,
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
  `course_closed` int(11) NOT NULL DEFAULT 2,
  `lead_source` mediumtext DEFAULT NULL,
  `referred_staff_id` mediumtext DEFAULT NULL,
  `payment_id` mediumtext DEFAULT NULL,
  `username` mediumtext DEFAULT NULL,
  `password` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_enrollment`
--

LOCK TABLES `tc_enrollment` WRITE;
/*!40000 ALTER TABLE `tc_enrollment` DISABLE KEYS */;
INSERT INTO `tc_enrollment` VALUES (1,'2026-05-25 15:45:49','2026-05-25 15:45:49','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','SU9kV05YV3RXSHlxN2pPOVgxbU9HMUkwSXN1UDdXSUdscWhCMEcwYXhpYz0=','UkZVdldTR2lORW1Pdm1Xa3FVR1RCQT09','Selvam','Subramani','Sivakasi','8934759847','','VnZScUFGamJwQlphUndRQm1JMGZwdS82Z2k0NVorZ05IdjMxdTlhQ3ZqST0=','5','10:00','13:00','ckNPemN5RnFHSnk0QzJ6U0FpeEUvbjZDd0xWZ1cwb29pb3E0cEYrWVBBaz0=','Full Payment',20000.00,20000.00,0.00,'2000-05-07','0000-00-00','',NULL,2,'instagram','',NULL,'ENT001/26-27','TFFmTXB6KzI4SVlPUGovMGtxallvUT09',0),(2,'2026-05-25 17:05:10','2026-05-25 17:05:10','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b2xtVGZhWkY0TWhabGFIQzhHTXdnaGlZSGlkMmVFWnh6L2JPUDVaNDk3ST0=','OXUxRlFlZDFXaGtzZ2FPWndpY2VHZz09','Senthil','Kumar','Meenakshi Amman, Sivakasi','8934759834','','VnZScUFGamJwQlphUndRQm1JMGZwdS82Z2k0NVorZ05IdjMxdTlhQ3ZqST0=','5','14:00','17:00','ckNPemN5RnFHSnk0QzJ6U0FpeEUvbjZDd0xWZ1cwb29pb3E0cEYrWVBBaz0=','Installment',20000.00,5000.00,15000.00,'0000-00-00','2026-05-01','','1779708910.jpg',2,'website','',NULL,'ENT002/26-27','OXUxRlFlZDFXaGtzZ2FPWndpY2VHZz09',0),(3,'2026-05-25 17:13:29','2026-05-25 17:13:29','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','LzF2cmpxeFEvTGpYcW40RHA4bVVIb0Q3OTFpVkt4bnU3dE82YU91eTFSST0=','ZThySE5tZGZXMDVtb0JMN2JmR0dsUT09','test','Selvam','Sivakasi','8798797979','','V0l5T2JQd01IekJuZDl1QndPenloT0NTbnN1QnY4cmlGK0h0QVB2b0MyMD0=','8','','','','Installment',30000.00,3000.00,27000.00,'2000-05-27','2026-05-02','',NULL,2,'','',NULL,'ENT003/26-27','RXNjQlJzQzdGV2ZUTERFcWFDcEl5UT09',0);
/*!40000 ALTER TABLE `tc_enrollment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tc_enrollment_internship`
--

DROP TABLE IF EXISTS `tc_enrollment_internship`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_enrollment_internship` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  `company_id` mediumtext DEFAULT NULL,
  `enrollment_internship_id` mediumtext DEFAULT NULL,
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
  `course_closed` int(11) NOT NULL DEFAULT 2,
  `lead_source` mediumtext DEFAULT NULL,
  `referred_staff_id` mediumtext DEFAULT NULL,
  `username` mediumtext DEFAULT NULL,
  `password` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_enrollment_internship`
--

LOCK TABLES `tc_enrollment_internship` WRITE;
/*!40000 ALTER TABLE `tc_enrollment_internship` DISABLE KEYS */;
/*!40000 ALTER TABLE `tc_enrollment_internship` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tc_expense_category`
--

DROP TABLE IF EXISTS `tc_expense_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_expense_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  `company_id` mediumtext DEFAULT NULL,
  `expense_category_id` mediumtext DEFAULT NULL,
  `expense_category_name` mediumtext DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_expense_category`
--

LOCK TABLES `tc_expense_category` WRITE;
/*!40000 ALTER TABLE `tc_expense_category` DISABLE KEYS */;
INSERT INTO `tc_expense_category` VALUES (1,'2026-05-25 17:17:43','2026-05-25 17:17:43','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','am55MFc4YjcycGNaekdSdklVdGMzZGpGd0hzL2cwQzYrMWp4R3d3K1Z1ND0=','furniture','',0),(2,'2026-05-25 17:17:54','2026-05-25 17:17:54','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','YnZOTXZwVmlQOFVDdWVHQ01HWStwRm5xNzcxVkF2Q0RMSmFBRU9uZDFMST0=','refreshment','',0);
/*!40000 ALTER TABLE `tc_expense_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tc_expense_entry`
--

DROP TABLE IF EXISTS `tc_expense_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_expense_entry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  `company_id` mediumtext DEFAULT NULL,
  `expense_entry_id` mediumtext DEFAULT NULL,
  `expense_category_id` mediumtext DEFAULT NULL,
  `expense_entry_date` mediumtext DEFAULT NULL,
  `payment_mode` mediumtext DEFAULT NULL,
  `bank` mediumtext DEFAULT NULL,
  `amount` mediumtext DEFAULT NULL,
  `total_amount` mediumtext DEFAULT NULL,
  `attachments` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `expense_entry_number` mediumtext DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_expense_entry`
--

LOCK TABLES `tc_expense_entry` WRITE;
/*!40000 ALTER TABLE `tc_expense_entry` DISABLE KEYS */;
INSERT INTO `tc_expense_entry` VALUES (1,'2026-05-25 17:18:50','2026-05-25 17:18:50','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','R21vRGRFMWEvWE15MGNMNzl0c1NXTHRjSDdkWWNPTVIzR3k2S0EzZkw4MD0=','am55MFc4YjcycGNaekdSdklVdGMzZGpGd0hzL2cwQzYrMWp4R3d3K1Z1ND0=','2026-05-25','QkpqMDZxSXhOVCtabnJGYVFBZ2E3a1ZwNURHSEp2ODVEd2ZaMlhOUXRJOD0=','','3000.00','3000.00','1779709730_Receipt_REC002_26-27-2.pdf,1779709730_logo.png',0,'001/26-27','fgfh');
/*!40000 ALTER TABLE `tc_expense_entry` ENABLE KEYS */;
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
  `company_id` mediumtext DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_logins`
--

LOCK TABLES `tc_logins` WRITE;
/*!40000 ALTER TABLE `tc_logins` DISABLE KEYS */;
INSERT INTO `tc_logins` VALUES (1,'2026-05-25 15:37:50',NULL,NULL,1,0),(2,'2026-05-25 15:46:58',NULL,NULL,1,0),(3,'2026-05-25 15:48:25',NULL,NULL,1,0),(4,'2026-05-25 16:00:16',NULL,NULL,1,0),(5,'2026-05-25 16:03:00',NULL,NULL,1,0),(6,'2026-05-25 17:10:08',NULL,NULL,1,0),(7,'2026-05-25 17:28:14',NULL,NULL,1,0),(8,'2026-05-25 17:32:39',NULL,NULL,1,0),(9,'2026-05-25 17:57:53',NULL,NULL,1,0),(10,'2026-05-25 18:09:06',NULL,NULL,1,0),(11,'2026-05-25 18:09:55',NULL,NULL,1,0),(12,'2026-05-26 12:54:13',NULL,NULL,1,0),(13,'2026-05-26 12:57:42',NULL,NULL,1,0),(14,'2026-05-26 14:05:51',NULL,NULL,2,0);
/*!40000 ALTER TABLE `tc_logins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tc_payment`
--

DROP TABLE IF EXISTS `tc_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  `company_id` mediumtext DEFAULT NULL,
  `payment_id` mediumtext DEFAULT NULL,
  `course_type` mediumtext DEFAULT NULL,
  `payment_date` mediumtext DEFAULT NULL,
  `payment_mode` mediumtext DEFAULT NULL,
  `bank` mediumtext DEFAULT NULL,
  `amount` mediumtext DEFAULT NULL,
  `total_amount` mediumtext DEFAULT NULL,
  `student_id` mediumtext DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `enrollment_id` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_payment`
--

LOCK TABLES `tc_payment` WRITE;
/*!40000 ALTER TABLE `tc_payment` DISABLE KEYS */;
INSERT INTO `tc_payment` VALUES (1,'2026-05-25 17:16:45','2026-05-25 17:16:45','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','REC001/26-27','training','2026-05-25','QkpqMDZxSXhOVCtabnJGYVFBZ2E3a1ZwNURHSEp2ODVEd2ZaMlhOUXRJOD0=','','2000.00','2000.00','UkZVdldTR2lORW1Pdm1Xa3FVR1RCQT09','','SU9kV05YV3RXSHlxN2pPOVgxbU9HMUkwSXN1UDdXSUdscWhCMEcwYXhpYz0=',0);
/*!40000 ALTER TABLE `tc_payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tc_payment_mode`
--

DROP TABLE IF EXISTS `tc_payment_mode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_payment_mode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  `company_id` mediumtext DEFAULT NULL,
  `payment_mode_id` mediumtext DEFAULT NULL,
  `payment_mode_name` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_payment_mode`
--

LOCK TABLES `tc_payment_mode` WRITE;
/*!40000 ALTER TABLE `tc_payment_mode` DISABLE KEYS */;
INSERT INTO `tc_payment_mode` VALUES (1,'2026-05-25 17:15:24','2026-05-25 17:15:24','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','QkpqMDZxSXhOVCtabnJGYVFBZ2E3a1ZwNURHSEp2ODVEd2ZaMlhOUXRJOD0=','cash',0),(2,'2026-05-25 17:15:30','2026-05-25 17:15:30','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','YTVpZjFjV21yeWFwSkpGYlFIWEZlZ2JSa2xZMkFLb0dDaUc2QXN1dS9ocz0=','gpay',0);
/*!40000 ALTER TABLE `tc_payment_mode` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tc_payroll`
--

DROP TABLE IF EXISTS `tc_payroll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_payroll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  `company_id` mediumtext DEFAULT NULL,
  `payroll_id` mediumtext DEFAULT NULL,
  `payroll_number` mediumtext DEFAULT NULL,
  `staff_id` mediumtext DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `monthly_salary` decimal(10,2) DEFAULT NULL,
  `per_day_salary` decimal(10,2) DEFAULT NULL,
  `cl_days` decimal(3,1) NOT NULL DEFAULT 0.0,
  `lop_days` decimal(3,1) NOT NULL DEFAULT 0.0,
  `total_deduction` decimal(10,2) DEFAULT NULL,
  `incentive_amount` decimal(10,2) DEFAULT NULL,
  `total_references` int(11) DEFAULT 0,
  `net_salary` decimal(10,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_payroll`
--

LOCK TABLES `tc_payroll` WRITE;
/*!40000 ALTER TABLE `tc_payroll` DISABLE KEYS */;
INSERT INTO `tc_payroll` VALUES (1,'2026-05-25 17:20:31','2026-05-25 17:20:31','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','OUVlZDcvMFhLVzlERHlrTVowVEY4NUxTVUYrQiszN0Z0WVRZMjVXODZRdz0=','ek4wd1lzdDNaZ1lEblVuSk9kbk5Ndz09','R0xLSC83TmJCVThleGVmeURqY21Lek9vNVhOMWRWbEZ6S1JaeThDZDkwND0=',5,2026,30000.00,967.74,0.5,2.0,1935.48,0.00,0,28064.52,'2026-05-25',0),(2,'2026-05-25 17:20:31','2026-05-25 17:20:31','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','OUVlZDcvMFhLVzlERHlrTVowVEY4eWhOS2NiU0xvdFpMT2NoNERYZlZCZz0=','ek4wd1lzdDNaZ1lEblVuSk9kbk5Ndz09','ckNPemN5RnFHSnk0QzJ6U0FpeEUvbjZDd0xWZ1cwb29pb3E0cEYrWVBBaz0=',5,2026,20000.00,645.16,0.0,0.0,0.00,0.00,0,20000.00,'2026-05-25',0);
/*!40000 ALTER TABLE `tc_payroll` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tc_role_permission`
--

DROP TABLE IF EXISTS `tc_role_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_role_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  `company_id` mediumtext DEFAULT NULL,
  `role_id` mediumtext DEFAULT NULL,
  `permission_page` mediumtext DEFAULT NULL,
  `permission_action` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_role_permission`
--

LOCK TABLES `tc_role_permission` WRITE;
/*!40000 ALTER TABLE `tc_role_permission` DISABLE KEYS */;
/*!40000 ALTER TABLE `tc_role_permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tc_role_permissions`
--

DROP TABLE IF EXISTS `tc_role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_role_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  `company_id` mediumtext DEFAULT NULL,
  `role_id` mediumtext DEFAULT NULL,
  `permission_page` mediumtext DEFAULT NULL,
  `permission_action` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_role_permissions`
--

LOCK TABLES `tc_role_permissions` WRITE;
/*!40000 ALTER TABLE `tc_role_permissions` DISABLE KEYS */;
INSERT INTO `tc_role_permissions` VALUES (1,'2026-05-26 14:01:56',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','course_closure','V$$A$$E$$D',0),(2,'2026-05-26 14:01:56',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','tasks','V$$A$$E',0),(3,'2026-05-26 14:01:56',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','enrollment','V$$A$$E',0),(4,'2026-05-26 14:01:56',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','enrollment_internship','V$$A$$E',0),(5,'2026-05-26 14:01:56',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','course_enquiry','V$$A$$E$$D',0),(6,'2026-05-26 14:01:56',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','receipt','V$$A$$E',0),(7,'2026-05-26 14:01:56',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','payment_mode','V$$A$$E$$D',0),(8,'2026-05-26 14:01:56',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','bank','V$$A$$E$$D',0),(9,'2026-05-26 14:01:56',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','expense_category','V$$A$$E$$D',0),(10,'2026-05-26 14:01:56',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','expense_entry','V$$A$$E$$D',0),(11,'2026-05-26 14:01:56',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','daily_reports','V',0),(12,'2026-05-26 14:01:56',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','report_enrollment','V',0),(13,'2026-05-26 14:01:56',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','report_attendance','V',0),(14,'2026-05-26 14:01:56',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','report_placement','V',0),(15,'2026-05-26 14:01:56',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','course','V$$A$$E$$D',0),(16,'2026-05-26 14:01:56',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','tasks','V$$A$$E$$D',0),(17,'2026-05-26 14:01:56',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','enrollment','V$$A$$E',0),(18,'2026-05-26 14:01:56',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','enrollment_internship','V$$A$$E',0),(19,'2026-05-26 14:01:56',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','course_enquiry','V$$A$$E$$D',0),(20,'2026-05-26 14:01:56',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','receipt','V$$A$$E',0),(21,'2026-05-26 14:01:56',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','payment_mode','V$$A$$E$$D',0),(22,'2026-05-26 14:01:56',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','bank','V$$A$$E$$D',0),(23,'2026-05-26 14:01:56',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','expense_category','V$$A$$E$$D',0),(24,'2026-05-26 14:01:56',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','expense_entry','V$$A$$E$$D',0),(25,'2026-05-26 14:01:56',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','daily_reports','V',0),(26,'2026-05-26 14:01:56',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','report_enrollment','V',0),(27,'2026-05-26 14:01:56',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','report_attendance','V',0),(28,'2026-05-26 14:01:56',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','report_placement','V',0);
/*!40000 ALTER TABLE `tc_role_permissions` ENABLE KEYS */;
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
  `company_id` mediumtext DEFAULT NULL,
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
INSERT INTO `tc_roles` VALUES (1,'2026-05-25 15:38:12','2026-05-26 13:55:56','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','cFlxWHg1N2RYSzg5OEczMGFSTmNvaFJjU2tQdEJlZVJoR1ZLL3ZYZitzUT0=','trainer','handle training',0),(2,'2026-05-25 15:38:26','2026-05-26 14:01:56','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','manager','Need to handle Manager',0);
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
  `company_id` mediumtext DEFAULT NULL,
  `staff_id` mediumtext DEFAULT NULL,
  `staff_name` mediumtext DEFAULT NULL,
  `staff_number` mediumtext DEFAULT NULL,
  `role_id` mediumtext DEFAULT NULL,
  `course_id` mediumtext DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `username` mediumtext DEFAULT NULL,
  `password` mediumtext DEFAULT NULL,
  `address` mediumtext DEFAULT NULL,
  `role` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_staff`
--

LOCK TABLES `tc_staff` WRITE;
/*!40000 ALTER TABLE `tc_staff` DISABLE KEYS */;
INSERT INTO `tc_staff` VALUES (1,'2026-05-25 15:41:04','2026-05-25 15:41:04','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','ckNPemN5RnFHSnk0QzJ6U0FpeEUvbjZDd0xWZ1cwb29pb3E0cEYrWVBBaz0=','Subha','9347594354','cFlxWHg1N2RYSzg5OEczMGFSTmNvaFJjU2tQdEJlZVJoR1ZLL3ZYZitzUT0=','VnZScUFGamJwQlphUndRQm1JMGZwdS82Z2k0NVorZ05IdjMxdTlhQ3ZqST0=',20000.00,'Subha','VUFGcEN5VU9tWXQwWjhKeUc5c1h0UT09','Sivakasi','trainer',0),(2,'2026-05-25 15:41:38','2026-05-25 15:41:38','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','R0xLSC83TmJCVThleGVmeURqY21Lek9vNVhOMWRWbEZ6S1JaeThDZDkwND0=','Cheran','9834758943','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','',30000.00,'Cheran','a21QaDdzK1p5ZS9MRm1XZkdxQllsZz09','','manager',0);
/*!40000 ALTER TABLE `tc_staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tc_student_attendance`
--

DROP TABLE IF EXISTS `tc_student_attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_student_attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  `company_id` mediumtext DEFAULT NULL,
  `attendance_number` mediumtext DEFAULT NULL,
  `attendance_date` date DEFAULT NULL,
  `staff_id` mediumtext DEFAULT NULL,
  `student_id` mediumtext DEFAULT NULL,
  `fn_present` mediumtext DEFAULT NULL,
  `an_present` mediumtext DEFAULT NULL,
  `present_code` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_student_attendance`
--

LOCK TABLES `tc_student_attendance` WRITE;
/*!40000 ALTER TABLE `tc_student_attendance` DISABLE KEYS */;
/*!40000 ALTER TABLE `tc_student_attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tc_student_reports`
--

DROP TABLE IF EXISTS `tc_student_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_student_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  `company_id` mediumtext DEFAULT NULL,
  `report_id` mediumtext DEFAULT NULL,
  `student_id` mediumtext DEFAULT NULL,
  `report_date` date DEFAULT NULL,
  `task_id` int(11) DEFAULT NULL,
  `work_done` longtext DEFAULT NULL,
  `remarks` longtext DEFAULT NULL,
  `attachment` mediumtext DEFAULT NULL,
  `status` mediumtext DEFAULT 'Pending',
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_student_reports`
--

LOCK TABLES `tc_student_reports` WRITE;
/*!40000 ALTER TABLE `tc_student_reports` DISABLE KEYS */;
INSERT INTO `tc_student_reports` VALUES (1,'2026-05-25 18:09:22','2026-05-25 18:10:09','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','001/26-27','ENT001/26-27','2026-05-25',1,'work done','Nice work','1779712762_1779447504_6a1036d011aad_4__1_.doc','Approved',0);
/*!40000 ALTER TABLE `tc_student_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tc_student_tasks`
--

DROP TABLE IF EXISTS `tc_student_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_student_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  `company_id` mediumtext DEFAULT NULL,
  `task_id` mediumtext DEFAULT NULL,
  `task_title` mediumtext DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `assigned_by` mediumtext DEFAULT NULL,
  `assigned_to_student` mediumtext DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `priority` mediumtext DEFAULT NULL,
  `status` mediumtext DEFAULT 'Pending',
  `completion_percentage` int(11) DEFAULT 0,
  `attachments` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_student_tasks`
--

LOCK TABLES `tc_student_tasks` WRITE;
/*!40000 ALTER TABLE `tc_student_tasks` DISABLE KEYS */;
INSERT INTO `tc_student_tasks` VALUES (1,'2026-05-25 17:30:40','2026-05-25 17:53:34','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','001/26-27','check varaiables','','YUhCdktQRG9nK2dWb1JONGNOUW1KcExBWldSZEx3cU9yMVJ2YzhKVjVhVT0=','ENT001/26-27','2026-05-25','2026-05-25','Medium','Completed',100,'1779710440_1779447504_6a1036d011aad_4__1_.doc',0);
/*!40000 ALTER TABLE `tc_student_tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tc_task_comments`
--

DROP TABLE IF EXISTS `tc_task_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_task_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` mediumtext DEFAULT NULL,
  `company_id` mediumtext DEFAULT NULL,
  `task_id` int(11) DEFAULT NULL,
  `user_role` mediumtext DEFAULT NULL,
  `username` mediumtext DEFAULT NULL,
  `comment` longtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_task_comments`
--

LOCK TABLES `tc_task_comments` WRITE;
/*!40000 ALTER TABLE `tc_task_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `tc_task_comments` ENABLE KEYS */;
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
  `company_id` mediumtext DEFAULT NULL,
  `task_id` mediumtext DEFAULT NULL,
  `title` mediumtext DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `assigned_to` mediumtext DEFAULT NULL,
  `assigned_by` mediumtext DEFAULT NULL,
  `status` mediumtext DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `custom_id` mediumtext DEFAULT NULL,
  `unique_number` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_tasks`
--

LOCK TABLES `tc_tasks` WRITE;
/*!40000 ALTER TABLE `tc_tasks` DISABLE KEYS */;
INSERT INTO `tc_tasks` VALUES (1,'2026-05-25 17:28:49','2026-05-25 17:28:49','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=',NULL,'test','','ckNPemN5RnFHSnk0QzJ6U0FpeEUvbjZDd0xWZ1cwb29pb3E0cEYrWVBBaz0=','YUhCdktQRG9nK2dWb1JONGNOUW1KcExBWldSZEx3cU9yMVJ2YzhKVjVhVT0=','in_progress','2026-05-25','MzEzbm9obkZZVzhmZU9RVktBMGQ2RnhRdU1jWkIybDhkSkljVTdYK1hPYz0=','dFhoNmpNOFFtRzZCOE1jd0czbXJ6Zz09',0);
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
  `company_id` mediumtext DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_users`
--

LOCK TABLES `tc_users` WRITE;
/*!40000 ALTER TABLE `tc_users` DISABLE KEYS */;
INSERT INTO `tc_users` VALUES (1,'2026-05-25 15:37:35','2026-05-25 15:37:35',NULL,'YUhCdktQRG9nK2dWb1JONGNOUW1KcExBWldSZEx3cU9yMVJ2YzhKVjVhVT0=','Lakshmi','NENqZjNTNkhLMUZsZHhDbDZ5NFVwUT09','admin','Lakshmi',NULL,'9898998794',NULL,'U2hsSWlPUWErZzN0K084WU96RjY1Zz09',0);
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

-- Dump completed on 2026-05-26 14:10:44
