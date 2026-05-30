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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_attendance`
--

LOCK TABLES `tc_attendance` WRITE;
/*!40000 ALTER TABLE `tc_attendance` DISABLE KEYS */;
INSERT INTO `tc_attendance` VALUES (1,'2026-05-23 12:58:40','2026-05-23 13:22:15','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','QVllMERKMkI3YkptY3hYRnRuZDZNSnpVN1FZclFPVmJMdUR4TkhpMXVKcz0=','2026-05-23','Sy9mYm1odDVYQnJDRWovWHRGdm5IdUM5Q1p6aWxLVEdjMGZmaVppaGhJMD0=','Kumar','8734957943','trainer','P','P','PP',0),(2,'2026-05-23 12:58:40','2026-05-23 13:22:15','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','QVllMERKMkI3YkptY3hYRnRuZDZNSnpVN1FZclFPVmJMdUR4TkhpMXVKcz0=','2026-05-23','cmJOMzlpQU9oaWJOVVJVUms0NEhINU11Q0JtZFk1UXUyN2U5cXB0VlBnbz0=','Subha','9834758974','trainer','P','P','PP',0),(3,'2026-05-23 12:58:49','2026-05-23 13:22:19','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','aEVCQjZXRlVnYVRmVmVRTE4yN0xmQ2lmNlpJUzB0aVF1SmhkS2NMZEdJaz0=','2026-05-22','Sy9mYm1odDVYQnJDRWovWHRGdm5IdUM5Q1p6aWxLVEdjMGZmaVppaGhJMD0=','Kumar','8734957943','trainer','A','P','AP',0),(4,'2026-05-23 12:58:49','2026-05-23 13:22:19','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','aEVCQjZXRlVnYVRmVmVRTE4yN0xmQ2lmNlpJUzB0aVF1SmhkS2NMZEdJaz0=','2026-05-22','cmJOMzlpQU9oaWJOVVJVUms0NEhINU11Q0JtZFk1UXUyN2U5cXB0VlBnbz0=','Subha','9834758974','trainer','P','P','PP',0),(5,'2026-05-23 12:59:27','2026-05-23 13:22:58','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','M3l3NkZjWVVsTWtBZ0U0VnBBcitDQU5pUFdLVDNHb1dqVGpuaG1UbVM0dz0=','2026-05-21','Sy9mYm1odDVYQnJDRWovWHRGdm5IdUM5Q1p6aWxLVEdjMGZmaVppaGhJMD0=','Kumar','8734957943','trainer','A','A','AA',0),(6,'2026-05-23 12:59:27','2026-05-23 13:22:58','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','M3l3NkZjWVVsTWtBZ0U0VnBBcitDQU5pUFdLVDNHb1dqVGpuaG1UbVM0dz0=','2026-05-21','cmJOMzlpQU9oaWJOVVJVUms0NEhINU11Q0JtZFk1UXUyN2U5cXB0VlBnbz0=','Subha','9834758974','trainer','A','A','AA',0),(7,'2026-05-23 12:59:34','2026-05-23 13:22:31','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b01ZSTd2VFBhV3Jnb1ZHU0YyQVJTa2Z4MXBwQ1ZrTGJvb0VjTENLWnRiVT0=','2026-05-20','Sy9mYm1odDVYQnJDRWovWHRGdm5IdUM5Q1p6aWxLVEdjMGZmaVppaGhJMD0=','Kumar','8734957943','trainer','P','P','PP',0),(8,'2026-05-23 12:59:34','2026-05-23 13:22:31','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','b01ZSTd2VFBhV3Jnb1ZHU0YyQVJTa2Z4MXBwQ1ZrTGJvb0VjTENLWnRiVT0=','2026-05-20','cmJOMzlpQU9oaWJOVVJVUms0NEhINU11Q0JtZFk1UXUyN2U5cXB0VlBnbz0=','Subha','9834758974','trainer','P','P','PP',0);
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
INSERT INTO `tc_bank` VALUES (1,'2026-05-23 11:26:40','2026-05-23 11:26:40','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','dHRWWW84VXVIVFpUZU9sSmlVL0d1TERuSTU1SEVhR2Z2aXdkSFZ0VFJMRT0=','Axis','9897973282','Lakshmi','Sivakasi','','amNzWVAzWmVET1FpNTdUUFU0WjJXQUFHT1hBV25KeTRYVUpwRXBWREh5bz0=',0);
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
INSERT INTO `tc_course` VALUES (1,'2026-05-23 09:49:26','2026-05-23 09:49:26','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','dlN1UWljcHUrZTJtWEVYRW5IYjlrc3BuQ2JualRTYmxZcEc1UjFuMG9VYz0=','PHP','5','20000',0),(2,'2026-05-23 09:49:37','2026-05-23 09:49:37','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','UG5zTG44Ukk3RlVYbm9nRUVwOS90bjhyRnYyUHhGenNQdnZQMzNPRlh6WT0=','Python','6','30000',0);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_enrollment`
--

LOCK TABLES `tc_enrollment` WRITE;
/*!40000 ALTER TABLE `tc_enrollment` DISABLE KEYS */;
INSERT INTO `tc_enrollment` VALUES (1,'2026-05-23 10:35:47','2026-05-23 10:35:47','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','MFZXdkY2aDF1WUg1N0c3UFI2ZVRtYk04ZVU4ZXVXUjlWTklhUXJzWVhWQT0=','UkZVdldTR2lORW1Pdm1Xa3FVR1RCQT09','Selvam','Kumar','Sivakasi','9348758987','','dlN1UWljcHUrZTJtWEVYRW5IYjlrc3BuQ2JualRTYmxZcEc1UjFuMG9VYz0=','5','12:00','14:00','cmJOMzlpQU9oaWJOVVJVUms0NEhINU11Q0JtZFk1UXUyN2U5cXB0VlBnbz0=','Full Payment',20000.00,20000.00,0.00,'1998-02-12','2026-05-01','A+','1779512747.jpg',2,'facebook','',NULL,NULL,NULL,0),(2,'2026-05-23 11:43:25','2026-05-23 11:43:25','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','Nm1mNVpHbnBRQnhCa1RSMER0bDZBUmFZWnVvdW5CaCtBeUlDQ3VLVkJEUT0=','UkZVdldTR2lORW1Pdm1Xa3FVR1RCQT09','Selvam','Kumar','Sivakasi','9348758987','','UG5zTG44Ukk3RlVYbm9nRUVwOS90bjhyRnYyUHhGenNQdnZQMzNPRlh6WT0=','6','15:00','17:00','cmJOMzlpQU9oaWJOVVJVUms0NEhINU11Q0JtZFk1UXUyN2U5cXB0VlBnbz0=','Installment',30000.00,5000.00,25000.00,'1998-02-12','2026-05-01','A+',NULL,2,'website','',NULL,NULL,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_enrollment_internship`
--

LOCK TABLES `tc_enrollment_internship` WRITE;
/*!40000 ALTER TABLE `tc_enrollment_internship` DISABLE KEYS */;
INSERT INTO `tc_enrollment_internship` VALUES (1,'2026-05-23 13:25:15','2026-05-23 13:25:15','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','aWYyc09vSFZ4U3UrdktmdUxOVVIzRXo2R2sxeG8rQUN5YUxqb3dzRHhiOD0=','RkEzeFgvVFA4OG1MK2s4aEtxa0dFdz09','kani','Karthick','sivakasi','9834798437','','dlN1UWljcHUrZTJtWEVYRW5IYjlrc3BuQ2JualRTYmxZcEc1UjFuMG9VYz0=','15','12:00','14:00','Sy9mYm1odDVYQnJDRWovWHRGdm5IdUM5Q1p6aWxLVEdjMGZmaVppaGhJMD0=','Installment',20000.00,10000.00,10000.00,'2001-02-12','0000-00-00','',NULL,2,'','',NULL,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_expense_category`
--

LOCK TABLES `tc_expense_category` WRITE;
/*!40000 ALTER TABLE `tc_expense_category` DISABLE KEYS */;
INSERT INTO `tc_expense_category` VALUES (1,'2026-05-23 12:07:07','2026-05-23 12:07:07','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','WDg5cjkzSldkQTc1STQrc3BoVys2dVZQNkVSc1NVM3R0UG9SRC9vZy9ZWT0=','furniture','all furniture items',0);
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
INSERT INTO `tc_expense_entry` VALUES (1,'2026-05-23 12:15:55','2026-05-23 12:15:55','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','RUkxL29OR3F5dFNISzJUMFhYMnhBL1ZpLzliZ3VremlZUVFDcDNyaGxPYz0=','WDg5cjkzSldkQTc1STQrc3BoVys2dVZQNkVSc1NVM3R0UG9SRC9vZy9ZWT0=','2026-05-23','akJnM0lzUSs3eEJOYzNiZjlOSGg2VzhOT1dWVHVaSU5qcFFjZ29ydTkxUT0=','','1000.00','1000.00','1779518755_1779447504_6a1036d011aad_5_.doc,1779518755_WhatsApp_Image_2026-05-15_at_9.37.11_AM.jpeg',0,'001/26-27','furniture chair');
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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_logins`
--

LOCK TABLES `tc_logins` WRITE;
/*!40000 ALTER TABLE `tc_logins` DISABLE KEYS */;
INSERT INTO `tc_logins` VALUES (1,'2026-05-19 17:04:02',NULL,NULL,NULL,0),(2,'2026-05-19 17:04:07',NULL,NULL,NULL,0),(3,'2026-05-19 17:04:09',NULL,NULL,NULL,0),(4,'2026-05-19 17:04:15',NULL,NULL,NULL,0),(5,'2026-05-19 17:10:13',NULL,NULL,1,0),(6,'2026-05-19 17:10:32',NULL,NULL,1,0),(7,'2026-05-19 17:15:13',NULL,NULL,1,0),(8,'2026-05-19 17:16:13','2026-05-19 17:34:30',NULL,1,0),(9,'2026-05-19 17:26:18','2026-05-19 17:58:26',NULL,1,0),(10,'2026-05-19 17:34:43',NULL,NULL,1,0),(11,'2026-05-19 17:36:44',NULL,NULL,1,0),(12,'2026-05-19 17:36:59',NULL,NULL,1,0),(13,'2026-05-19 17:37:32','2026-05-19 17:53:01',NULL,1,0),(14,'2026-05-19 17:53:08','2026-05-19 17:55:21',NULL,1,0),(15,'2026-05-19 17:55:35','2026-05-19 18:28:47',NULL,1,0),(16,'2026-05-19 17:58:33','2026-05-19 18:10:34',NULL,1,0),(17,'2026-05-19 18:11:24','2026-05-19 18:12:06',NULL,2,0),(18,'2026-05-19 18:15:39','2026-05-19 18:20:23',NULL,1,0),(19,'2026-05-19 18:20:56','2026-05-19 18:28:42',NULL,2,0),(20,'2026-05-21 09:28:22','2026-05-21 09:43:32',NULL,1,0),(21,'2026-05-21 09:44:01',NULL,NULL,1,0),(22,'2026-05-21 09:44:06',NULL,NULL,1,0),(23,'2026-05-21 09:44:09',NULL,NULL,1,0),(24,'2026-05-21 09:44:19',NULL,NULL,1,0),(25,'2026-05-21 11:47:27',NULL,NULL,1,0),(26,'2026-05-21 11:47:29','2026-05-21 11:48:08',NULL,1,0),(27,'2026-05-22 11:20:39',NULL,NULL,1,0),(28,'2026-05-23 09:37:19',NULL,NULL,1,0),(29,'2026-05-23 13:26:41',NULL,NULL,2,0),(30,'2026-05-23 13:27:23',NULL,NULL,1,0),(31,'2026-05-23 13:28:40',NULL,NULL,2,0),(32,'2026-05-23 13:33:07',NULL,NULL,2,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_payment`
--

LOCK TABLES `tc_payment` WRITE;
/*!40000 ALTER TABLE `tc_payment` DISABLE KEYS */;
INSERT INTO `tc_payment` VALUES (1,'2026-05-23 11:43:37','2026-05-23 11:43:37','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','REC001/26-27','training','2026-05-23','akJnM0lzUSs3eEJOYzNiZjlOSGg2VzhOT1dWVHVaSU5qcFFjZ29ydTkxUT0=','','5000.00','5000.00','UkZVdldTR2lORW1Pdm1Xa3FVR1RCQT09','Enrollment payment','MFZXdkY2aDF1WUg1N0c3UFI2ZVRtYk04ZVU4ZXVXUjlWTklhUXJzWVhWQT0=',0),(2,'2026-05-23 12:06:13','2026-05-23 12:06:13','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','REC002/26-27','training','2026-05-23','amNzWVAzWmVET1FpNTdUUFU0WjJXQUFHT1hBV25KeTRYVUpwRXBWREh5bz0=','dHRWWW84VXVIVFpUZU9sSmlVL0d1TERuSTU1SEVhR2Z2aXdkSFZ0VFJMRT0=','15000.00','15000.00','UkZVdldTR2lORW1Pdm1Xa3FVR1RCQT09','','MFZXdkY2aDF1WUg1N0c3UFI2ZVRtYk04ZVU4ZXVXUjlWTklhUXJzWVhWQT0=',0),(3,'2026-05-23 13:25:21','2026-05-23 13:25:21','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','REC003/26-27','internship','2026-05-23','akJnM0lzUSs3eEJOYzNiZjlOSGg2VzhOT1dWVHVaSU5qcFFjZ29ydTkxUT0=','','10000.00','10000.00','RkEzeFgvVFA4OG1MK2s4aEtxa0dFdz09','Enrollment payment','aWYyc09vSFZ4U3UrdktmdUxOVVIzRXo2R2sxeG8rQUN5YUxqb3dzRHhiOD0=',0);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_payment_mode`
--

LOCK TABLES `tc_payment_mode` WRITE;
/*!40000 ALTER TABLE `tc_payment_mode` DISABLE KEYS */;
INSERT INTO `tc_payment_mode` VALUES (1,'2026-05-23 11:25:27','2026-05-23 11:25:27','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','akJnM0lzUSs3eEJOYzNiZjlOSGg2VzhOT1dWVHVaSU5qcFFjZ29ydTkxUT0=','cash',0),(2,'2026-05-23 11:25:39','2026-05-23 11:25:39','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','R3RLRCtKeGMyYk5SYS9OTStkN0E3S2dxUnNXUndCRWxIZlYwTUk4Ulo5RT0=','gpay',0),(3,'2026-05-23 11:25:48','2026-05-23 11:25:48','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','amNzWVAzWmVET1FpNTdUUFU0WjJXQUFHT1hBV25KeTRYVUpwRXBWREh5bz0=','paytm',0);
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
INSERT INTO `tc_payroll` VALUES (1,'2026-05-23 13:06:50','2026-05-23 13:13:32','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','Y3NqQ0ZnSzBQYVZDdmhOTjVoeGpVa01wSklPSnpyd1NVRWJsdmxoNk44QT0=','ek4wd1lzdDNaZ1lEblVuSk9kbk5Ndz09','Sy9mYm1odDVYQnJDRWovWHRGdm5IdUM5Q1p6aWxLVEdjMGZmaVppaGhJMD0=',5,2026,20000.00,645.16,1.0,1.0,645.16,0.00,0,19354.84,'2026-05-23',0),(2,'2026-05-23 13:06:50','2026-05-23 13:13:32','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','Y3NqQ0ZnSzBQYVZDdmhOTjVoeGpVa2IrNW9iOTRnTEpSWDBFUHpFRElQOD0=','ek4wd1lzdDNaZ1lEblVuSk9kbk5Ndz09','cmJOMzlpQU9oaWJOVVJVUms0NEhINU11Q0JtZFk1UXUyN2U5cXB0VlBnbz0=',5,2026,18000.00,580.65,1.0,0.5,290.33,0.00,0,17709.67,'2026-05-23',0);
/*!40000 ALTER TABLE `tc_payroll` ENABLE KEYS */;
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
INSERT INTO `tc_roles` VALUES (1,'2026-05-23 09:43:34',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','TGtUZVhWOW4wWDVIaUpjTzduTU4vRGJVVWw5bGpCR1ZFVmNtMitUTGlPZz0=','manager','Handle management works',0),(2,'2026-05-23 09:44:13',NULL,'VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','UWlhMHl4RTdPY3YyVCtmWnVPMWFIZ29Hb0NhRGo1M0d0R1Fxem8zUnFXUT0=','trainer','Handle trainings',0);
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
INSERT INTO `tc_staff` VALUES (1,'2026-05-23 09:50:20','2026-05-23 09:50:20','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','cmJOMzlpQU9oaWJOVVJVUms0NEhINU11Q0JtZFk1UXUyN2U5cXB0VlBnbz0=','Subha','9834758974','UWlhMHl4RTdPY3YyVCtmWnVPMWFIZ29Hb0NhRGo1M0d0R1Fxem8zUnFXUT0=','dlN1UWljcHUrZTJtWEVYRW5IYjlrc3BuQ2JualRTYmxZcEc1UjFuMG9VYz0=',18000.00,'Subha','VUFGcEN5VU9tWXQwWjhKeUc5c1h0UT09','','trainer',0),(2,'2026-05-23 12:58:16','2026-05-23 12:58:16','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','Sy9mYm1odDVYQnJDRWovWHRGdm5IdUM5Q1p6aWxLVEdjMGZmaVppaGhJMD0=','Kumar','8734957943','UWlhMHl4RTdPY3YyVCtmWnVPMWFIZ29Hb0NhRGo1M0d0R1Fxem8zUnFXUT0=','UG5zTG44Ukk3RlVYbm9nRUVwOS90bjhyRnYyUHhGenNQdnZQMzNPRlh6WT0=',20000.00,'Kumar','M1FycEFqNWozVjBMWTB3YWExb1crQT09','','trainer',0);
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
  `name` mediumtext DEFAULT NULL,
  `email` mediumtext DEFAULT NULL,
  `mobile` mediumtext DEFAULT NULL,
  `custom_id` mediumtext DEFAULT NULL,
  `unique_number` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tc_users`
--

LOCK TABLES `tc_users` WRITE;
/*!40000 ALTER TABLE `tc_users` DISABLE KEYS */;
INSERT INTO `tc_users` VALUES (1,'2026-05-22 11:37:25','2026-05-22 11:37:25','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','NC9odk56ZngydDJPeUpDV2tINjZpUG5hd2piMHZnRlpaRDUrZEd3YU0rZz0=','Lakshmi','NENqZjNTNkhLMUZsZHhDbDZ5NFVwUT09','admin','Lakshmi',NULL,'9898989898',NULL,'U2hsSWlPUWErZzN0K084WU96RjY1Zz09',0),(2,'2026-05-23 09:48:22','2026-05-23 09:48:22','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','YUk2akdMaHhvYTFOZlZNOGRrSnVxbHYwRHNwNFM0eSswYW9VNmNCdkdVST0=','Senthil','Z0duZ1kzd0d1S2JNMXNWRk5lWVVEQT09','manager','Senthil',NULL,'9875989484',NULL,'enZsL24wbzZuckpuZU9qWXBEcVBVZz09',0),(3,'2026-05-23 10:35:47','2026-05-23 10:35:47','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','MFZXdkY2aDF1WUg1N0c3UFI2ZVRtU2hFckdsakx3WndmSEwrdlpoR3d4dz0=','ENT001/26-27','eUpTSndzMURVQjJCVVRhb3hFMU5OUT09','student','Selvam',NULL,'9348758987',NULL,'Z1dsS2dPRFVUakIvVUVvTm11NmMxQT09',0),(4,'2026-05-23 13:25:15','2026-05-23 13:25:15','VjF5N2FMOWEvN2MrMVAyU1J6SThORVFxZEVlMVV0cnJTTUZVY05lK211UT0=','aWYyc09vSFZ4U3UrdktmdUxOVVIzTDgxdW9WYlNBWHMzaVlGbVFoVmt5UT0=','ENI001/26-27','UDNFaDFEOU95bVd5SzNXM0JQMEVzUT09','student','kani',NULL,'9834798437',NULL,'eFZPZzdwdmhzdkRoa25pZzY5QjBHUT09',0);
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

-- Dump completed on 2026-05-23 13:33:10
