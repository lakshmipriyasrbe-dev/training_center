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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_attendance`
--

LOCK TABLES `tc_attendance` WRITE;
/*!40000 ALTER TABLE `tc_attendance` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_bank`
--

LOCK TABLES `tc_bank` WRITE;
/*!40000 ALTER TABLE `tc_bank` DISABLE KEYS */;
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
INSERT INTO `tc_company` VALUES (1,'2026-05-27 09:53:06','2026-05-27 09:53:06','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','We Grow Skill Campus','wegrowskilldevelopment@gmail.com','9898989898','sivakasi',0,'27ABCDE1234F2Z5','Sivakasi','company_1779520085_6a1152556254d.png','Mm9TTDFkd1NMTjB5bThSc0dPelFtV3l2YlpMcXhUSXZ5Smx0NnhJbitwck0vU2FrS0pOVXlsVVF3dnFlaFR0NlpjaWN5c3FLY0cyb21aZTcyQkFIcGh5MWRsZW85V0QzT0ZheHZoa3lBY0hjQytNbnZVdlAzQTkxaXNvc3ZTZ3JwMk41QnY1Uk5WRXIwVEh0UWduNkpRZEVHY0NxZmxiYWJsOVB2UEw5RWxRPQ=='),(2,'2026-05-27 09:52:55','2026-05-27 09:52:55','MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','We Grow Skill Campus','wegrowskilldevelopment@gmail.com','9874999933','Srivilliputhur',0,'27ABCDE1234F2Z4','Srivilliputhur','company_1779429506_6a0ff0822e9f0.png','Mm9TTDFkd1NMTjB5bThSc0dPelFtYmJySnQwSEhSNVE0U2U3Q1NpMzNUMUYraysrbTJpbFNJWFN6MkY0czBtcjN1emZxc3FHbHZOaGQ4S2NyUGpTTllBTm5CUDN4NUg4cSthZXpBVTdORjlJK0NJZjViMmRFUnN1MjdwYlVXYTFRZUNHMm1oVzlPWlRZc1Vlams3Q0pVcFNmSFRGdThPS1RvT3RubGV2YzZsbS9QQ2FYV3N6UDhmVFQ4YUY4dkV2');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_course_enquiry`
--

LOCK TABLES `tc_course_enquiry` WRITE;
/*!40000 ALTER TABLE `tc_course_enquiry` DISABLE KEYS */;
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
  `enrollment_number` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_enrollment`
--

LOCK TABLES `tc_enrollment` WRITE;
/*!40000 ALTER TABLE `tc_enrollment` DISABLE KEYS */;
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
  `enrollment_number` varchar(255) DEFAULT NULL,
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
-- Table structure for table `tc_event`
--

DROP TABLE IF EXISTS `tc_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tc_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` varchar(255) DEFAULT NULL,
  `event_number` mediumtext DEFAULT NULL,
  `company_id` varchar(255) DEFAULT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `role_id` varchar(255) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_name` varchar(255) DEFAULT NULL,
  `event_description` text DEFAULT NULL,
  `images` text DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `created_date_time` datetime DEFAULT NULL,
  `updated_date_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_event`
--

LOCK TABLES `tc_event` WRITE;
/*!40000 ALTER TABLE `tc_event` DISABLE KEYS */;
/*!40000 ALTER TABLE `tc_event` ENABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_expense_category`
--

LOCK TABLES `tc_expense_category` WRITE;
/*!40000 ALTER TABLE `tc_expense_category` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_expense_entry`
--

LOCK TABLES `tc_expense_entry` WRITE;
/*!40000 ALTER TABLE `tc_expense_entry` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_logins`
--

LOCK TABLES `tc_logins` WRITE;
/*!40000 ALTER TABLE `tc_logins` DISABLE KEYS */;
INSERT INTO `tc_logins` VALUES (1,'2026-05-27 16:46:17',NULL,NULL,1,0),(2,'2026-05-27 16:47:01',NULL,NULL,2,0),(3,'2026-05-27 16:58:13',NULL,NULL,2,0);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_payment`
--

LOCK TABLES `tc_payment` WRITE;
/*!40000 ALTER TABLE `tc_payment` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_payment_mode`
--

LOCK TABLES `tc_payment_mode` WRITE;
/*!40000 ALTER TABLE `tc_payment_mode` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_payroll`
--

LOCK TABLES `tc_payroll` WRITE;
/*!40000 ALTER TABLE `tc_payroll` DISABLE KEYS */;
/*!40000 ALTER TABLE `tc_payroll` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=295 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_role_permissions`
--

LOCK TABLES `tc_role_permissions` WRITE;
/*!40000 ALTER TABLE `tc_role_permissions` DISABLE KEYS */;
INSERT INTO `tc_role_permissions` VALUES (209,'2026-05-27 13:46:00',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','cFlxWHg1N2RYSzg5OEczMGFSTmNvaFJjU2tQdEJlZVJoR1ZLL3ZYZitzUT0=','course','V$$A$$E$$D',0),(210,'2026-05-27 13:46:00',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','cFlxWHg1N2RYSzg5OEczMGFSTmNvaFJjU2tQdEJlZVJoR1ZLL3ZYZitzUT0=','course','V$$A$$E$$D',0),(211,'2026-05-27 13:46:27',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','course_closure','V$$A$$E$$D',0),(212,'2026-05-27 13:46:27',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','tasks','V$$A$$E',0),(213,'2026-05-27 13:46:27',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','enrollment','V$$A$$E',0),(214,'2026-05-27 13:46:27',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','enrollment_internship','V$$A$$E',0),(215,'2026-05-27 13:46:27',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','course_enquiry','V$$A$$E$$D',0),(216,'2026-05-27 13:46:27',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','receipt','V$$A$$E',0),(217,'2026-05-27 13:46:27',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','payment_mode','V$$A$$E$$D',0),(218,'2026-05-27 13:46:27',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','bank','V$$A$$E$$D',0),(219,'2026-05-27 13:46:27',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','expense_category','V$$A$$E$$D',0),(220,'2026-05-27 13:46:27',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','expense_entry','V$$A$$E$$D',0),(221,'2026-05-27 13:46:27',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','daily_reports','V',0),(222,'2026-05-27 13:46:27',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','report_enrollment','V',0),(223,'2026-05-27 13:46:27',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','report_attendance','V',0),(224,'2026-05-27 13:46:27',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','report_placement','V',0),(225,'2026-05-27 13:46:27',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','course','V$$A$$E$$D',0),(226,'2026-05-27 13:46:27',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','tasks','V$$A$$E$$D',0),(227,'2026-05-27 13:46:27',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','enrollment','V$$A$$E',0),(228,'2026-05-27 13:46:27',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','enrollment_internship','V$$A$$E',0),(229,'2026-05-27 13:46:27',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','course_enquiry','V$$A$$E$$D',0),(230,'2026-05-27 13:46:27',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','receipt','V$$A$$E',0),(231,'2026-05-27 13:46:27',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','payment_mode','V$$A$$E$$D',0),(232,'2026-05-27 13:46:27',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','bank','V$$A$$E$$D',0),(233,'2026-05-27 13:46:27',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','expense_category','V$$A$$E$$D',0),(234,'2026-05-27 13:46:27',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','expense_entry','V$$A$$E$$D',0),(235,'2026-05-27 13:46:27',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','daily_reports','V',0),(236,'2026-05-27 13:46:27',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','report_enrollment','V',0),(237,'2026-05-27 13:46:27',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','report_attendance','V',0),(238,'2026-05-27 13:46:27',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','report_placement','V',0),(239,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','course','V$$A$$E$$D',0),(240,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','course_closure','V$$A$$E$$D',0),(241,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','tasks','V$$A$$E$$D',0),(242,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','event','V$$A$$E$$D',0),(243,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','enrollment','V$$A$$E$$D',0),(244,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','enrollment_internship','V$$A$$E$$D',0),(245,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','course_enquiry','V$$A$$E$$D',0),(246,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','attendance','V',0),(247,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','payroll','V$$A$$E$$D',0),(248,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','receipt','V$$A$$E$$D',0),(249,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','payment_mode','V$$A$$E$$D',0),(250,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','bank','V$$A$$E$$D',0),(251,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','expense_category','V$$A$$E$$D',0),(252,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','expense_entry','V$$A$$E$$D',0),(253,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','daily_reports','V',0),(254,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','report_enrollment','V',0),(255,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','report_payroll','V',0),(256,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','report_payments','V',0),(257,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','report_attendance','V',0),(258,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','report_placement','V',0),(259,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','student_attendance','V',0),(260,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','student_tasks','V',0),(261,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','student_reports','V',0),(262,'2026-05-27 13:46:43',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','daily_report','V',0),(263,'2026-05-27 13:46:43',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','course','V$$A$$E$$D',0),(264,'2026-05-27 13:46:43',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','course_closure','V$$A$$E$$D',0),(265,'2026-05-27 13:46:43',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','tasks','V$$A$$E$$D',0),(266,'2026-05-27 13:46:43',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','event','V$$A$$E$$D',0),(267,'2026-05-27 13:46:43',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','enrollment','V$$A$$E$$D',0),(268,'2026-05-27 13:46:43',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','enrollment_internship','V$$A$$E$$D',0),(269,'2026-05-27 13:46:43',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','course_enquiry','V$$A$$E$$D',0),(270,'2026-05-27 13:46:43',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','attendance','V',0),(271,'2026-05-27 13:46:43',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','payroll','V$$A$$E$$D',0),(272,'2026-05-27 13:46:43',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','receipt','V$$A$$E$$D',0),(273,'2026-05-27 13:46:43',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','payment_mode','V$$A$$E$$D',0),(274,'2026-05-27 13:46:43',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','bank','V$$A$$E$$D',0),(275,'2026-05-27 13:46:43',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','expense_category','V$$A$$E$$D',0),(276,'2026-05-27 13:46:43',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','expense_entry','V$$A$$E$$D',0),(277,'2026-05-27 13:46:43',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','daily_reports','V',0),(278,'2026-05-27 13:46:43',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','report_enrollment','V',0),(279,'2026-05-27 13:46:43',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','report_payroll','V',0),(280,'2026-05-27 13:46:43',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','report_payments','V',0),(281,'2026-05-27 13:46:43',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','report_attendance','V',0),(282,'2026-05-27 13:46:43',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','report_placement','V',0),(283,'2026-05-27 13:46:43',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','student_attendance','V',0),(284,'2026-05-27 13:46:43',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','student_tasks','V',0),(285,'2026-05-27 13:46:43',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','student_reports','V',0),(286,'2026-05-27 13:46:44',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','daily_report','V',0),(287,'2026-05-27 13:49:53',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RXZ5NE1pZjRlVUExSk5VUXY2TUpuYUcwOVBSMUt2NjNPZ3NWMWZSeEplZz0=','student_tasks','V$$A$$E',0),(288,'2026-05-27 13:49:53',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RXZ5NE1pZjRlVUExSk5VUXY2TUpuYUcwOVBSMUt2NjNPZ3NWMWZSeEplZz0=','student_reports','V',0),(289,'2026-05-27 13:49:53',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RXZ5NE1pZjRlVUExSk5VUXY2TUpuYUcwOVBSMUt2NjNPZ3NWMWZSeEplZz0=','daily_report','V',0),(290,'2026-05-27 13:49:53',NULL,'MW5INjVsRjhNVmxnUnJYN3g1NE1uY3pNSGVINW5QSnptYzRoa01tQmxtaz0=','RXZ5NE1pZjRlVUExSk5VUXY2TUpuYUcwOVBSMUt2NjNPZ3NWMWZSeEplZz0=','student_profile','V$$A$$E',0),(291,'2026-05-27 13:49:53',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RXZ5NE1pZjRlVUExSk5VUXY2TUpuYUcwOVBSMUt2NjNPZ3NWMWZSeEplZz0=','student_tasks','V$$A$$E',0),(292,'2026-05-27 13:49:53',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RXZ5NE1pZjRlVUExSk5VUXY2TUpuYUcwOVBSMUt2NjNPZ3NWMWZSeEplZz0=','student_reports','V',0),(293,'2026-05-27 13:49:53',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RXZ5NE1pZjRlVUExSk5VUXY2TUpuYUcwOVBSMUt2NjNPZ3NWMWZSeEplZz0=','daily_report','V',0),(294,'2026-05-27 13:49:53',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RXZ5NE1pZjRlVUExSk5VUXY2TUpuYUcwOVBSMUt2NjNPZ3NWMWZSeEplZz0=','student_profile','V$$A$$E',0);
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_roles`
--

LOCK TABLES `tc_roles` WRITE;
/*!40000 ALTER TABLE `tc_roles` DISABLE KEYS */;
INSERT INTO `tc_roles` VALUES (1,'2026-05-25 15:38:12','2026-05-27 13:46:00','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','cFlxWHg1N2RYSzg5OEczMGFSTmNvaFJjU2tQdEJlZVJoR1ZLL3ZYZitzUT0=','staff','handle training',0),(2,'2026-05-25 15:38:26','2026-05-27 13:46:27','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b28yZjZ1em94cXhxcFU1ZDhtWlkxWHVqbTIydXpzeUJzMVlZd01uZzlDWT0=','manager','Need to handle Management',0),(3,'2026-05-26 14:18:27','2026-05-27 13:49:53','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RXZ5NE1pZjRlVUExSk5VUXY2TUpuYUcwOVBSMUt2NjNPZ3NWMWZSeEplZz0=','aspirants','these are students',0),(4,'2026-05-27 13:10:43','2026-05-27 13:46:43','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','management','Whole management',0);
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_staff`
--

LOCK TABLES `tc_staff` WRITE;
/*!40000 ALTER TABLE `tc_staff` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_student_reports`
--

LOCK TABLES `tc_student_reports` WRITE;
/*!40000 ALTER TABLE `tc_student_reports` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_student_tasks`
--

LOCK TABLES `tc_student_tasks` WRITE;
/*!40000 ALTER TABLE `tc_student_tasks` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_tasks`
--

LOCK TABLES `tc_tasks` WRITE;
/*!40000 ALTER TABLE `tc_tasks` DISABLE KEYS */;
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
  `role_id` varchar(255) DEFAULT NULL,
  `name` mediumtext DEFAULT NULL,
  `email` mediumtext DEFAULT NULL,
  `mobile` mediumtext DEFAULT NULL,
  `custom_id` mediumtext DEFAULT NULL,
  `unique_number` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_users`
--

LOCK TABLES `tc_users` WRITE;
/*!40000 ALTER TABLE `tc_users` DISABLE KEYS */;
INSERT INTO `tc_users` VALUES (1,'2026-05-25 15:37:35','2026-05-25 15:37:35',NULL,'YUhCdktQRG9nK2dWb1JONGNOUW1KcExBWldSZEx3cU9yMVJ2YzhKVjVhVT0=','Lakshmi','NENqZjNTNkhLMUZsZHhDbDZ5NFVwUT09','admin','admin','Lakshmi',NULL,'9898998794',NULL,'U2hsSWlPUWErZzN0K084WU96RjY1Zz09',0),(2,'2026-05-27 13:13:22','2026-05-27 16:56:58','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','UnYyQzFOY09DL2pzN2Job29Jb3pGV0lLZC9XTDBLSDRndjVZNnROK2doTT0=','ThavaBalan','SDBiaEtkNUorc0xwdE9iL2J1UkMxQT09','management','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','ThavaBalan',NULL,'9898983348',NULL,'enZsL24wbzZuckpuZU9qWXBEcVBVZz09',0),(3,'2026-05-27 13:14:08','2026-05-27 16:57:25','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','dFV6eVcyQ2V2T2dPOTFNSGZEZ2s1NlVPUlJWWU1Cc05BOFhiUDBlbXgvQT0=','LakshmiPriya','Z2RsalRyelJuYld0Z1g3V0tIaGtWZz09','management','RnczMnIyVWpqVEtmYkJlTUJsVU0vMS9la2hINEs1SERVQjl2YVFlY3ZDaz0=','Lakshmi Priya',NULL,'8773389393',NULL,'Z1dsS2dPRFVUakIvVUVvTm11NmMxQT09',0);
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

-- Dump completed on 2026-05-27 17:05:14
