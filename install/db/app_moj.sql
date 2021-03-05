-- MySQL dump 10.13  Distrib 8.0.23, for Linux (x86_64)
--
-- Host: localhost    Database: app_moj
-- ------------------------------------------------------
-- Server version	8.0.23-0ubuntu0.20.04.1

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
-- Current Database: `app_moj`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `app_moj` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `app_moj`;

--
-- Table structure for table `best_ac_submissions`
--

DROP TABLE IF EXISTS `best_ac_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `best_ac_submissions` (
  `problem_id` int NOT NULL,
  `submitter` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `submission_id` int NOT NULL,
  `used_time` int NOT NULL,
  `used_memory` int NOT NULL,
  `tot_size` int NOT NULL,
  `shortest_id` int NOT NULL,
  `shortest_used_time` int NOT NULL,
  `shortest_used_memory` int NOT NULL,
  `shortest_tot_size` int NOT NULL,
  PRIMARY KEY (`problem_id`,`submitter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blogs`
--

DROP TABLE IF EXISTS `blogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blogs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_time` datetime NOT NULL,
  `poster` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_md` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `zan` int NOT NULL,
  `is_hidden` tinyint(1) NOT NULL,
  `type` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'B',
  `is_draft` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blogs_comments`
--

DROP TABLE IF EXISTS `blogs_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blogs_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `blog_id` int NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_time` datetime NOT NULL,
  `poster` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `zan` int NOT NULL,
  `reply_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blogs_tags`
--

DROP TABLE IF EXISTS `blogs_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blogs_tags` (
  `id` int NOT NULL AUTO_INCREMENT,
  `blog_id` int NOT NULL,
  `tag` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `blog_id` (`blog_id`),
  KEY `tag` (`tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `click_zans`
--

DROP TABLE IF EXISTS `click_zans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `click_zans` (
  `type` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_id` int NOT NULL,
  `val` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`type`,`target_id`,`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contests`
--

DROP TABLE IF EXISTS `contests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `start_time` datetime NOT NULL,
  `last_min` int NOT NULL,
  `player_num` int NOT NULL,
  `status` varchar(50) NOT NULL,
  `extra_config` varchar(200) NOT NULL,
  `zan` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contests_asks`
--

DROP TABLE IF EXISTS `contests_asks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contests_asks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `contest_id` int NOT NULL,
  `username` varchar(20) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `post_time` datetime NOT NULL,
  `reply_time` datetime NOT NULL,
  `is_hidden` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contests_notice`
--

DROP TABLE IF EXISTS `contests_notice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contests_notice` (
  `contest_id` int NOT NULL,
  `title` varchar(30) NOT NULL,
  `content` varchar(500) NOT NULL,
  `time` datetime NOT NULL,
  KEY `contest_id` (`contest_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contests_permissions`
--

DROP TABLE IF EXISTS `contests_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contests_permissions` (
  `username` varchar(20) NOT NULL,
  `contest_id` int NOT NULL,
  PRIMARY KEY (`username`,`contest_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contests_problems`
--

DROP TABLE IF EXISTS `contests_problems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contests_problems` (
  `problem_id` int NOT NULL,
  `contest_id` int NOT NULL,
  PRIMARY KEY (`problem_id`,`contest_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contests_registrants`
--

DROP TABLE IF EXISTS `contests_registrants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contests_registrants` (
  `username` varchar(20) NOT NULL,
  `user_rating` int NOT NULL,
  `contest_id` int NOT NULL,
  `has_participated` tinyint(1) NOT NULL,
  `rank` int NOT NULL,
  PRIMARY KEY (`contest_id`,`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contests_submissions`
--

DROP TABLE IF EXISTS `contests_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contests_submissions` (
  `contest_id` int NOT NULL,
  `submitter` varchar(20) NOT NULL,
  `problem_id` int NOT NULL,
  `submission_id` int NOT NULL,
  `score` int NOT NULL,
  `penalty` int NOT NULL,
  PRIMARY KEY (`contest_id`,`submitter`,`problem_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `courses` (
  `id` int NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `courses_problems`
--

DROP TABLE IF EXISTS `courses_problems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `courses_problems` (
  `problem_id` int NOT NULL,
  `course_id` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `custom_test_submissions`
--

DROP TABLE IF EXISTS `custom_test_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custom_test_submissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `problem_id` int unsigned NOT NULL,
  `submit_time` datetime NOT NULL,
  `submitter` varchar(20) NOT NULL,
  `content` text NOT NULL,
  `judge_time` datetime DEFAULT NULL,
  `result` blob NOT NULL,
  `status` varchar(20) NOT NULL,
  `status_details` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hacks`
--

DROP TABLE IF EXISTS `hacks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hacks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `problem_id` int unsigned NOT NULL,
  `contest_id` int unsigned DEFAULT NULL,
  `submission_id` int unsigned NOT NULL,
  `hacker` varchar(20) NOT NULL,
  `owner` varchar(20) NOT NULL,
  `input` varchar(150) NOT NULL,
  `input_type` char(20) NOT NULL,
  `submit_time` datetime NOT NULL,
  `judge_time` datetime DEFAULT NULL,
  `success` tinyint(1) DEFAULT NULL,
  `details` blob NOT NULL,
  `is_hidden` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `submission_id` (`submission_id`),
  KEY `is_hidden` (`is_hidden`,`problem_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `important_blogs`
--

DROP TABLE IF EXISTS `important_blogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `important_blogs` (
  `blog_id` int NOT NULL,
  `level` int NOT NULL,
  PRIMARY KEY (`blog_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `judger_info`
--

DROP TABLE IF EXISTS `judger_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `judger_info` (
  `judger_name` varchar(50) NOT NULL,
  `password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` char(20) NOT NULL,
  PRIMARY KEY (`judger_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `problems`
--

DROP TABLE IF EXISTS `problems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `problems` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `is_hidden` tinyint(1) NOT NULL DEFAULT '0',
  `submission_requirement` text,
  `hackable` tinyint(1) NOT NULL DEFAULT '0',
  `extra_config` varchar(500) NOT NULL DEFAULT '{"view_content_type":"ALL","view_details_type":"ALL"}',
  `zan` int NOT NULL,
  `ac_num` int NOT NULL DEFAULT '0',
  `submit_num` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `problems_contents`
--

DROP TABLE IF EXISTS `problems_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `problems_contents` (
  `id` int NOT NULL,
  `statement` mediumtext NOT NULL,
  `statement_md` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `problems_permissions`
--

DROP TABLE IF EXISTS `problems_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `problems_permissions` (
  `username` varchar(20) NOT NULL,
  `problem_id` int NOT NULL,
  PRIMARY KEY (`username`,`problem_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `problems_tags`
--

DROP TABLE IF EXISTS `problems_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `problems_tags` (
  `id` int NOT NULL AUTO_INCREMENT,
  `problem_id` int NOT NULL,
  `tag` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `problem_id` (`problem_id`),
  KEY `tag` (`tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `search_requests`
--

DROP TABLE IF EXISTS `search_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `search_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `remote_addr` varchar(50) NOT NULL,
  `type` enum('search','autocomplete') NOT NULL,
  `cache_id` int NOT NULL,
  `q` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `result` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `remote_addr` (`remote_addr`,`created_at`),
  KEY `created_at` (`created_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `submissions`
--

DROP TABLE IF EXISTS `submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `submissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `problem_id` int unsigned NOT NULL,
  `contest_id` int unsigned DEFAULT NULL,
  `submit_time` datetime NOT NULL,
  `submitter` varchar(20) NOT NULL,
  `content` text NOT NULL,
  `language` varchar(15) NOT NULL,
  `tot_size` int NOT NULL,
  `judge_time` datetime DEFAULT NULL,
  `result` blob NOT NULL,
  `status` varchar(20) NOT NULL,
  `result_error` varchar(20) DEFAULT NULL,
  `score` int DEFAULT NULL,
  `used_time` int NOT NULL DEFAULT '0',
  `used_memory` int NOT NULL DEFAULT '0',
  `is_hidden` tinyint(1) NOT NULL,
  `status_details` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `is_hidden` (`is_hidden`,`problem_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `trainings`
--

DROP TABLE IF EXISTS `trainings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trainings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_hidden` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `trainings_completion`
--

DROP TABLE IF EXISTS `trainings_completion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trainings_completion` (
  `training_id` int NOT NULL,
  `user` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`training_id`,`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `trainings_cond`
--

DROP TABLE IF EXISTS `trainings_cond`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trainings_cond` (
  `t_id` int NOT NULL,
  `c_id` int NOT NULL,
  `type` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`t_id`,`c_id`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `trainings_contents`
--

DROP TABLE IF EXISTS `trainings_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trainings_contents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `statement` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `statement_md` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `trainings_includes`
--

DROP TABLE IF EXISTS `trainings_includes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trainings_includes` (
  `p_id` int NOT NULL,
  `s_id` int NOT NULL,
  PRIMARY KEY (`p_id`,`s_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `upgrades`
--

DROP TABLE IF EXISTS `upgrades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `upgrades` (
  `name` varchar(50) NOT NULL,
  `status` enum('up','down') DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_info`
--

DROP TABLE IF EXISTS `user_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_info` (
  `usergroup` char(1) NOT NULL DEFAULT 'U',
  `username` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` char(32) NOT NULL,
  `svn_password` char(10) NOT NULL,
  `rating` int NOT NULL DEFAULT '1500',
  `qq` bigint NOT NULL,
  `sex` char(1) NOT NULL DEFAULT 'U',
  `ac_num` int NOT NULL,
  `register_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `remote_addr` varchar(50) NOT NULL,
  `http_x_forwarded_for` varchar(50) NOT NULL,
  `remember_token` char(60) NOT NULL,
  `motto` varchar(200) NOT NULL,
  PRIMARY KEY (`username`),
  KEY `rating` (`rating`,`username`),
  KEY `ac_num` (`ac_num`,`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_msg`
--

DROP TABLE IF EXISTS `user_msg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_msg` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sender` varchar(20) NOT NULL,
  `receiver` varchar(20) NOT NULL,
  `message` varchar(5000) NOT NULL,
  `send_time` datetime NOT NULL,
  `read_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_system_msg`
--

DROP TABLE IF EXISTS `user_system_msg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_system_msg` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `receiver` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `send_time` datetime NOT NULL,
  `read_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-03-04 23:30:26
