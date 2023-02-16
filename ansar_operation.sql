/*
SQLyog Community v13.1.8 (64 bit)
MySQL - 10.1.37-MariaDB : Database - ansar_operation
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`ansar_operation` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `ansar_operation`;

/*Table structure for table `avurp_vdp_ansar_bank_account_info` */

DROP TABLE IF EXISTS `avurp_vdp_ansar_bank_account_info`;

CREATE TABLE `avurp_vdp_ansar_bank_account_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vdp_id` int(11) DEFAULT NULL,
  `bank_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `branch_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `account_no` varchar(255) DEFAULT NULL,
  `mobile_bank_type` enum('bkash','rocket') DEFAULT NULL,
  `mobile_bank_account_no` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `prefer_choice` enum('general','mobile') DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5273 DEFAULT CHARSET=latin1;

/*Table structure for table `jobs` */

DROP TABLE IF EXISTS `jobs`;

CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8 NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_reserved_reserved_at_index` (`queue`,`reserved`,`reserved_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1825197 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `operation_user_action_log` */

DROP TABLE IF EXISTS `operation_user_action_log`;

CREATE TABLE `operation_user_action_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action_description` text CHARACTER SET utf8 NOT NULL,
  `action_type` enum('Entry','Edit','Verify','Approve') NOT NULL,
  `action_id` int(11) NOT NULL,
  `action_user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27135 DEFAULT CHARSET=latin1;

/*Table structure for table `sessions` */

DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8_unicode_ci,
  `payload` text COLLATE utf8_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL,
  UNIQUE KEY `sessions_id_unique` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `tbl_amsar_nominee_info` */

DROP TABLE IF EXISTS `tbl_amsar_nominee_info`;

CREATE TABLE `tbl_amsar_nominee_info` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `annsar_id` int(10) unsigned NOT NULL COMMENT 'This ID come form tbl_ansar_personal_info',
  `name_of_nominee` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `name_of_nominee_eng` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `relation_with_nominee` varchar(55) CHARACTER SET utf8 DEFAULT NULL,
  `relation_with_nominee_eng` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `nominee_parcentage` int(4) DEFAULT NULL,
  `nominee_parcentage_eng` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `nominee_contact_no` varchar(55) CHARACTER SET utf8 DEFAULT NULL,
  `nominee_contact_no_eng` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `FOREIGN_KEY_ANSAR_ID_FROM_PERSONAL_INFO` (`annsar_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=119745 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_ansar_bank_account_info` */

DROP TABLE IF EXISTS `tbl_ansar_bank_account_info`;

CREATE TABLE `tbl_ansar_bank_account_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ansar_id` int(11) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `branch_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `account_no` varchar(255) DEFAULT NULL,
  `mobile_bank_type` enum('','bkash','rocket') DEFAULT NULL,
  `mobile_bank_account_no` varchar(255) DEFAULT NULL,
  `prefer_choice` enum('general','mobile') DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ansar` (`ansar_id`),
  KEY `mobile_bank_type` (`mobile_bank_type`),
  KEY `choice` (`prefer_choice`)
) ENGINE=InnoDB AUTO_INCREMENT=6736 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_ansar_education_info` */

DROP TABLE IF EXISTS `tbl_ansar_education_info`;

CREATE TABLE `tbl_ansar_education_info` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `education_id` int(11) NOT NULL DEFAULT '0',
  `ansar_id` int(10) unsigned NOT NULL COMMENT 'This ID come from tbl_ansar_personal_info',
  `name_of_degree` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `name_of_degree_eng` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `institute_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `institute_name_eng` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `passing_year` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `passing_year_eng` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `gade_divission` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `gade_divission_eng` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `roll_no` decimal(10,0) DEFAULT '0',
  `certificate_verified` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `board_university` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FOREIGN_KEY_EDUCATION_ANSAR_ID_WITH_PERSONAL_INFO_ANSAR_ID` (`ansar_id`) USING BTREE,
  KEY `education` (`education_id`)
) ENGINE=InnoDB AUTO_INCREMENT=101993 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_ansar_future_state` */

DROP TABLE IF EXISTS `tbl_ansar_future_state`;

CREATE TABLE `tbl_ansar_future_state` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ansar_id` int(11) NOT NULL,
  `data` text NOT NULL,
  `from_status` enum('Unverified','Verified','Free','Panel','Offer','Embodiment','Freeze','Rest','Block','Black','Offer_blocked','Retire') NOT NULL,
  `to_status` enum('Unverified','Verified','Panel','Offer','Embodiment','Freeze','Rest','Block','Black','Offer_blocked','Retire','Free') NOT NULL,
  `action_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activation_date` datetime NOT NULL,
  `action_by` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ansar_id` (`ansar_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_ansar_parsonal_info` */

DROP TABLE IF EXISTS `tbl_ansar_parsonal_info`;

CREATE TABLE `tbl_ansar_parsonal_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ansar_id` int(10) unsigned NOT NULL,
  `session_id` int(10) unsigned DEFAULT NULL,
  `certificate_no` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT 'সর্বশেষ প্রশিক্ষন সনদ নং',
  `ansar_name_bng` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `ansar_name_eng` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `father_name_bng` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `father_name_eng` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `mother_name_bng` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `mother_name_eng` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `designation_id` int(4) DEFAULT NULL,
  `marital_status` enum('Married','Unmarried') DEFAULT NULL,
  `spouse_name_bng` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `spouse_name_eng` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `data_of_birth` date DEFAULT NULL,
  `national_id_no` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `division_id` int(4) unsigned NOT NULL COMMENT 'This field value come from tbl_division',
  `unit_id` int(4) unsigned NOT NULL COMMENT 'This field value come from tbl_unit',
  `thana_id` int(4) unsigned NOT NULL COMMENT 'This field value come from tbl_thana',
  `post_office_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `post_office_name_bng` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `village_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `village_name_bng` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `union_name_eng` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `union_name_bng` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `hight_feet` int(4) DEFAULT NULL,
  `hight_inch` float DEFAULT NULL,
  `blood_group_id` int(4) unsigned DEFAULT NULL COMMENT 'This field value come from tbl_blood_group',
  `sex` enum('Male','Female','Other') DEFAULT NULL,
  `eye_color` varchar(55) CHARACTER SET utf8 DEFAULT NULL,
  `eye_color_bng` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `skin_color` varchar(55) CHARACTER SET utf8 DEFAULT NULL,
  `identification_mark` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `mobile_no_self` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `mobile_no_request` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `birth_certificate_no` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  `disease_id` int(11) NOT NULL DEFAULT '0',
  `own_disease` varchar(255) DEFAULT NULL,
  `skill_id` int(11) NOT NULL DEFAULT '0',
  `own_particular_skill` varchar(255) DEFAULT NULL,
  `criminal_case` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `personal_mobile_no` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `email_self` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `email_request` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `land_phone_self` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `land_phone_request` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '1',
  `profile_pic` varchar(200) NOT NULL,
  `sign_pic` varchar(200) NOT NULL,
  `thumb_pic` varchar(200) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `skin_color_bng` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `identification_mark_bng` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `criminal_case_bng` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `avub_share_id` varchar(50) DEFAULT NULL,
  `remark` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`ansar_id`),
  UNIQUE KEY `ANSAR_SERIAL` (`id`),
  KEY `FOREIGN_KEY_DIVISION_ID_WITH_ID_FROM_TBL_DIVISION` (`division_id`),
  KEY `FOREIGN_KEY_DESIGNATION_ID_WITH_ID_FROM_TBL_DESIGNATION` (`designation_id`),
  KEY `FOREIGN_KEY_THANA_ID_WITH_ID_FROM_TBL_THANA` (`thana_id`),
  KEY `FOREIGN_KEY_UNIT_ID_WITH_ID_FROM_TBL_UNIT` (`unit_id`),
  KEY `FOREIGN_KEY_BLOOD_GROUP_ID_WITH_ID_FROM_TBL_BLOOD_GROUP` (`blood_group_id`),
  KEY `FOREIGN_KEY_DISEASE_ID_WITH_ID_FROM_TBL_LONGTIME_DISEASE` (`disease_id`),
  KEY `FOREIGN_KEY_SKILL_ID_WITH_ID_FROM_TBL_PARTICULAR_SKILL` (`skill_id`),
  KEY `verified` (`verified`),
  KEY `sex` (`sex`)
) ENGINE=InnoDB AUTO_INCREMENT=90988 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_ansar_parsonal_info_log` */

DROP TABLE IF EXISTS `tbl_ansar_parsonal_info_log`;

CREATE TABLE `tbl_ansar_parsonal_info_log` (
  `log_id` int(100) NOT NULL AUTO_INCREMENT,
  `action` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `action_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id` int(10) DEFAULT NULL,
  `ansar_id` int(10) DEFAULT NULL,
  `session_id` int(10) DEFAULT NULL,
  `certificate_no` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ansar_name_bng` varchar(765) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ansar_name_eng` varchar(765) COLLATE utf8_unicode_ci DEFAULT NULL,
  `father_name_bng` varchar(765) COLLATE utf8_unicode_ci DEFAULT NULL,
  `father_name_eng` varchar(765) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mother_name_bng` varchar(765) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mother_name_eng` varchar(765) COLLATE utf8_unicode_ci DEFAULT NULL,
  `designation_id` int(4) DEFAULT NULL,
  `marital_status` char(27) COLLATE utf8_unicode_ci DEFAULT NULL,
  `spouse_name_bng` varchar(765) COLLATE utf8_unicode_ci DEFAULT NULL,
  `spouse_name_eng` varchar(765) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data_of_birth` date DEFAULT NULL,
  `national_id_no` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `division_id` int(4) DEFAULT NULL,
  `unit_id` int(4) DEFAULT NULL,
  `thana_id` int(4) DEFAULT NULL,
  `post_office_name` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_office_name_bng` varchar(765) COLLATE utf8_unicode_ci DEFAULT NULL,
  `village_name` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `village_name_bng` varchar(765) COLLATE utf8_unicode_ci DEFAULT NULL,
  `union_name_eng` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `union_name_bng` varchar(450) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hight_feet` int(4) DEFAULT NULL,
  `hight_inch` float DEFAULT NULL,
  `blood_group_id` int(4) DEFAULT NULL,
  `sex` char(18) COLLATE utf8_unicode_ci DEFAULT NULL,
  `eye_color` varchar(165) COLLATE utf8_unicode_ci DEFAULT NULL,
  `eye_color_bng` varchar(450) COLLATE utf8_unicode_ci DEFAULT NULL,
  `skin_color` varchar(165) COLLATE utf8_unicode_ci DEFAULT NULL,
  `identification_mark` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile_no_self` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile_no_request` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `birth_certificate_no` varchar(450) COLLATE utf8_unicode_ci DEFAULT NULL,
  `disease_id` int(11) DEFAULT NULL,
  `own_disease` varchar(765) COLLATE utf8_unicode_ci DEFAULT NULL,
  `skill_id` int(11) DEFAULT NULL,
  `own_particular_skill` varchar(765) COLLATE utf8_unicode_ci DEFAULT NULL,
  `criminal_case` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `personal_mobile_no` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_self` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_request` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `land_phone_self` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `land_phone_request` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `verified` tinyint(1) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `profile_pic` varchar(600) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sign_pic` varchar(600) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thumb_pic` varchar(600) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `skin_color_bng` varchar(450) COLLATE utf8_unicode_ci DEFAULT NULL,
  `identification_mark_bng` varchar(750) COLLATE utf8_unicode_ci DEFAULT NULL,
  `criminal_case_bng` varchar(450) COLLATE utf8_unicode_ci DEFAULT NULL,
  `avub_share_id` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remark` varchar(765) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `tbl_ansar_status_info` */

DROP TABLE IF EXISTS `tbl_ansar_status_info`;

CREATE TABLE `tbl_ansar_status_info` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `ansar_id` int(10) unsigned NOT NULL,
  `free_status` int(11) NOT NULL,
  `pannel_status` tinyint(1) DEFAULT '0',
  `offer_sms_status` tinyint(1) DEFAULT '0' COMMENT 'send sms, and wait for reply',
  `offered_status` tinyint(1) DEFAULT '0' COMMENT 'offered accept, waiting for joining',
  `embodied_status` tinyint(1) DEFAULT '0',
  `offer_block_status` tinyint(1) DEFAULT '0',
  `freezing_status` tinyint(1) DEFAULT '0' COMMENT 'if he/she is in freezing state',
  `early_retierment_status` tinyint(1) DEFAULT '0' COMMENT 'if he/she is in freez for not complete 3 years of job',
  `block_list_status` tinyint(1) DEFAULT '0' COMMENT 'if he/she is in freez for any fault',
  `black_list_status` tinyint(1) DEFAULT '0' COMMENT 'mejor discipline issue',
  `rest_status` tinyint(1) DEFAULT '0' COMMENT 'after 3 years, 6 month breack status',
  `retierment_status` tinyint(1) DEFAULT '0' COMMENT 'retierment from ansar',
  `expired_status` tinyint(1) DEFAULT '0' COMMENT 'ansar who is dead',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `promotional_not_verified` tinyint(4) DEFAULT '0' COMMENT 'If he/she make unverified for promotional purpose',
  PRIMARY KEY (`ansar_id`),
  KEY `id` (`id`),
  KEY `free` (`free_status`),
  KEY `panel` (`pannel_status`),
  KEY `offer_sms_status` (`offer_sms_status`),
  KEY `offered_status` (`offered_status`),
  KEY `embodied_status` (`embodied_status`),
  KEY `offer_block` (`offer_block_status`),
  KEY `freez` (`freezing_status`),
  KEY `early_retirement_status` (`early_retierment_status`),
  KEY `block` (`block_list_status`),
  KEY `black` (`black_list_status`),
  KEY `rest` (`rest_status`),
  KEY `retierment` (`retierment_status`),
  KEY `expired` (`expired_status`)
) ENGINE=InnoDB AUTO_INCREMENT=86109 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_ansar_status_info_log` */

DROP TABLE IF EXISTS `tbl_ansar_status_info_log`;

CREATE TABLE `tbl_ansar_status_info_log` (
  `log_id` int(100) NOT NULL AUTO_INCREMENT,
  `action` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'update',
  `action_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id` int(10) DEFAULT NULL,
  `ansar_id` int(10) DEFAULT NULL,
  `free_status` tinyint(1) DEFAULT NULL,
  `pannel_status` tinyint(1) DEFAULT NULL,
  `offer_sms_status` tinyint(1) DEFAULT NULL,
  `offered_status` tinyint(1) DEFAULT NULL,
  `embodied_status` tinyint(1) DEFAULT NULL,
  `offer_block_status` tinyint(1) DEFAULT NULL,
  `freezing_status` tinyint(1) DEFAULT NULL,
  `early_retierment_status` tinyint(1) DEFAULT NULL,
  `block_list_status` tinyint(1) DEFAULT NULL,
  `black_list_status` tinyint(1) DEFAULT NULL,
  `rest_status` tinyint(1) DEFAULT NULL,
  `retierment_status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4477 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `tbl_ansar_training_info` */

DROP TABLE IF EXISTS `tbl_ansar_training_info`;

CREATE TABLE `tbl_ansar_training_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ansar_id` int(10) unsigned NOT NULL COMMENT 'This field value come from tbl_ansar_personal_info',
  `training_designation` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `training_designation_eng` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `training_institute_name` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  `training_institute_name_eng` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `training_start_date` date DEFAULT NULL,
  `training_start_date_eng` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `training_end_date` date DEFAULT NULL,
  `training_end_date_eng` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `trining_certificate_no` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `trining_certificate_no_eng` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `ansar_id_training_info` (`ansar_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=101079 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_blood_group` */

DROP TABLE IF EXISTS `tbl_blood_group`;

CREATE TABLE `tbl_blood_group` (
  `id` int(4) unsigned NOT NULL,
  `blood_group_name_eng` varchar(10) CHARACTER SET utf8 NOT NULL,
  `blood_group_name_bng` varchar(20) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_data_export_job` */

DROP TABLE IF EXISTS `tbl_data_export_job`;

CREATE TABLE `tbl_data_export_job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `file_completed` int(11) DEFAULT '0',
  `total_file` int(11) DEFAULT '1',
  `download_url` varchar(255) DEFAULT NULL,
  `notification_url` varchar(255) DEFAULT NULL,
  `delete_url` varchar(255) DEFAULT NULL,
  `status` enum('seen','unseen') DEFAULT 'unseen',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6022 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_data_export_status` */

DROP TABLE IF EXISTS `tbl_data_export_status`;

CREATE TABLE `tbl_data_export_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `data_export_job_id` int(11) NOT NULL,
  `status` enum('pending','success','downloaded','error') CHARACTER SET latin1 NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `payload` longblob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22083 DEFAULT CHARSET=utf8;

/*Table structure for table `tbl_designations` */

DROP TABLE IF EXISTS `tbl_designations`;

CREATE TABLE `tbl_designations` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name_eng` varchar(40) NOT NULL,
  `name_bng` varchar(30) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_disembodiment_reason` */

DROP TABLE IF EXISTS `tbl_disembodiment_reason`;

CREATE TABLE `tbl_disembodiment_reason` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reason_in_eng` varchar(100) NOT NULL,
  `reason_in_bng` varchar(100) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_division` */

DROP TABLE IF EXISTS `tbl_division`;

CREATE TABLE `tbl_division` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `division_code` varchar(4) CHARACTER SET latin1 NOT NULL,
  `division_name_bng` varchar(15) CHARACTER SET utf8 NOT NULL,
  `division_name_eng` varchar(12) CHARACTER SET latin1 NOT NULL,
  `sort_by` int(11) NOT NULL,
  `action_user_id` int(10) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `tbl_education_info` */

DROP TABLE IF EXISTS `tbl_education_info`;

CREATE TABLE `tbl_education_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `education_deg_bng` varchar(255) CHARACTER SET utf8 NOT NULL,
  `education_deg_eng` varchar(255) CHARACTER SET utf8 NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_embodiment` */

DROP TABLE IF EXISTS `tbl_embodiment`;

CREATE TABLE `tbl_embodiment` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `ansar_id` int(11) unsigned NOT NULL,
  `received_sms_id` int(4) NOT NULL COMMENT 'This value comes from the tbl_sms_receive_info ''id''',
  `memorandum_id` varchar(255) CHARACTER SET utf8 NOT NULL,
  `kpi_id` int(4) unsigned NOT NULL,
  `reporting_date` date NOT NULL,
  `joining_date` date DEFAULT NULL,
  `transfered_date` date DEFAULT NULL,
  `service_ended_date` date NOT NULL,
  `emboded_status` enum('Emboded','Block','Early Retier','Black List','On Leave','Freeze') DEFAULT NULL,
  `service_extension_status` int(4) NOT NULL DEFAULT '0',
  `action_user_id` int(4) NOT NULL DEFAULT '1',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `joining_kpi_id` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ansar_id` (`ansar_id`),
  KEY `kpi_id_embodiment` (`kpi_id`),
  KEY `mem_search` (`memorandum_id`)
) ENGINE=InnoDB AUTO_INCREMENT=119894 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_embodiment_daily_count_log` */

DROP TABLE IF EXISTS `tbl_embodiment_daily_count_log`;

CREATE TABLE `tbl_embodiment_daily_count_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `total` double NOT NULL,
  `date` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ansar` double DEFAULT NULL,
  `apc` double DEFAULT NULL,
  `pc` double DEFAULT NULL,
  `ansarMale` double DEFAULT NULL,
  `ansarFemale` double DEFAULT NULL,
  `apcMale` double DEFAULT NULL,
  `apcFemale` double DEFAULT NULL,
  `pcMale` double DEFAULT NULL,
  `pcFemale` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=213 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `tbl_embodiment_daily_unit_count_log` */

DROP TABLE IF EXISTS `tbl_embodiment_daily_unit_count_log`;

CREATE TABLE `tbl_embodiment_daily_unit_count_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `total` double NOT NULL,
  `date` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ansar` double DEFAULT NULL,
  `apc` double DEFAULT NULL,
  `pc` double DEFAULT NULL,
  `ansarMale` double DEFAULT NULL,
  `ansarFemale` double DEFAULT NULL,
  `apcMale` double DEFAULT NULL,
  `apcFemale` double DEFAULT NULL,
  `pcMale` double DEFAULT NULL,
  `pcFemale` double DEFAULT NULL,
  `unit_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `tbl_embodiment_log` */

DROP TABLE IF EXISTS `tbl_embodiment_log`;

CREATE TABLE `tbl_embodiment_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `old_embodiment_id` int(10) unsigned NOT NULL DEFAULT '0',
  `old_memorandum_id` varchar(255) DEFAULT NULL,
  `ansar_id` int(4) unsigned NOT NULL,
  `kpi_id` int(4) unsigned NOT NULL DEFAULT '0',
  `reporting_date` date DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `transfered_date` date DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `disembodiment_reason_id` int(10) NOT NULL,
  `move_to` enum('Panel','Freeze','Rest','Blacklist','Blocklist') CHARACTER SET latin1 DEFAULT NULL,
  `service_extension_status` int(10) NOT NULL DEFAULT '0',
  `comment` varchar(1000) CHARACTER SET latin1 NOT NULL DEFAULT 'N/A',
  `direct_status` int(4) NOT NULL DEFAULT '0',
  `action_user_id` int(4) NOT NULL DEFAULT '1',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `joining_kpi_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `search` (`old_memorandum_id`,`kpi_id`),
  KEY `ansar_id` (`ansar_id`)
) ENGINE=InnoDB AUTO_INCREMENT=115071 DEFAULT CHARSET=utf8;

/*Table structure for table `tbl_forget_password_request` */

DROP TABLE IF EXISTS `tbl_forget_password_request`;

CREATE TABLE `tbl_forget_password_request` (
  `id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_global_parameter` */

DROP TABLE IF EXISTS `tbl_global_parameter`;

CREATE TABLE `tbl_global_parameter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `param_name` varchar(255) NOT NULL,
  `param_value` int(11) NOT NULL,
  `param_unit` enum('Day','Month','Year','') NOT NULL,
  `param_description` varchar(255) NOT NULL,
  `param_piority` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_logged_in_user` */

DROP TABLE IF EXISTS `tbl_logged_in_user`;

CREATE TABLE `tbl_logged_in_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=466 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_long_term_disease` */

DROP TABLE IF EXISTS `tbl_long_term_disease`;

CREATE TABLE `tbl_long_term_disease` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `disease_name_eng` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `disease_name_bng` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_main_training_info` */

DROP TABLE IF EXISTS `tbl_main_training_info`;

CREATE TABLE `tbl_main_training_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `training_name_bng` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `training_name_eng` varchar(255) DEFAULT NULL,
  `action_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_member_id_card` */

DROP TABLE IF EXISTS `tbl_member_id_card`;

CREATE TABLE `tbl_member_id_card` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(10) unsigned NOT NULL,
  `issue_date` date NOT NULL,
  `expire_date` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `geo_id` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `issue_date` (`issue_date`),
  KEY `expire_date` (`expire_date`),
  KEY `ansar_id` (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

/*Table structure for table `tbl_memorandum_id` */

DROP TABLE IF EXISTS `tbl_memorandum_id`;

CREATE TABLE `tbl_memorandum_id` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `memorandum_id` varchar(255) CHARACTER SET utf8 NOT NULL,
  `mem_date` date DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `search` (`memorandum_id`)
) ENGINE=InnoDB AUTO_INCREMENT=207728 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_nid_request_log` */

DROP TABLE IF EXISTS `tbl_nid_request_log`;

CREATE TABLE `tbl_nid_request_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nid` varchar(25) NOT NULL,
  `dob` varchar(25) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `action_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_offer_blocked_ansar` */

DROP TABLE IF EXISTS `tbl_offer_blocked_ansar`;

CREATE TABLE `tbl_offer_blocked_ansar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ansar_id` int(11) DEFAULT NULL,
  `last_offer_unit` int(11) DEFAULT NULL,
  `status` enum('blocked','unblocked') DEFAULT 'blocked',
  `blocked_date` date DEFAULT NULL,
  `unblocked_date` date DEFAULT NULL,
  `action_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `SEARCH_INDEX` (`blocked_date`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=20541 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_offer_queue` */

DROP TABLE IF EXISTS `tbl_offer_queue`;

CREATE TABLE `tbl_offer_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_offer_quota` */

DROP TABLE IF EXISTS `tbl_offer_quota`;

CREATE TABLE `tbl_offer_quota` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_id` int(4) unsigned NOT NULL,
  `quota` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unit_name_offer_quota` (`unit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_offer_status` */

DROP TABLE IF EXISTS `tbl_offer_status`;

CREATE TABLE `tbl_offer_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ansar_id` int(11) NOT NULL,
  `last_offer_unit` int(11) DEFAULT NULL,
  `last_offer_units` varchar(100) DEFAULT NULL,
  `offer_type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `RELATION` (`ansar_id`,`last_offer_unit`),
  KEY `ansar_id` (`ansar_id`),
  KEY `last_offer_unit` (`last_offer_unit`)
) ENGINE=InnoDB AUTO_INCREMENT=47323 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_offer_zone` */

DROP TABLE IF EXISTS `tbl_offer_zone`;

CREATE TABLE `tbl_offer_zone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `range_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `offer_zone_range_id` int(11) NOT NULL,
  `offer_zone_unit_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `range_id` (`range_id`),
  KEY `unit_id` (`unit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=734 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_organization_type` */

DROP TABLE IF EXISTS `tbl_organization_type`;

CREATE TABLE `tbl_organization_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_name_eng` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `organization_name_bng` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `org_status` tinyint(4) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `tbl_panel_info` */

DROP TABLE IF EXISTS `tbl_panel_info`;

CREATE TABLE `tbl_panel_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'this auto id will track serial for list',
  `ansar_id` int(10) unsigned NOT NULL,
  `ansar_merit_list` int(4) NOT NULL DEFAULT '1',
  `panel_date` datetime DEFAULT NULL,
  `re_panel_date` datetime DEFAULT NULL,
  `memorandum_id` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `come_from` enum('Entry','Offer Reject','After Retier','OfferCancel','Blacklist','Rest','Direct','Offer','Offer Cancel','Block','Free','Offer Block') DEFAULT NULL,
  `locked` tinyint(1) DEFAULT '0',
  `action_user_id` int(4) NOT NULL DEFAULT '1',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `go_panel_position` int(11) DEFAULT NULL,
  `re_panel_position` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ansar_id_panel` (`ansar_id`),
  KEY `locked` (`locked`)
) ENGINE=InnoDB AUTO_INCREMENT=899888128 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_panel_info_log` */

DROP TABLE IF EXISTS `tbl_panel_info_log`;

CREATE TABLE `tbl_panel_info_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `panel_id_old` varchar(200) NOT NULL,
  `ansar_id` int(4) unsigned NOT NULL,
  `merit_list` int(11) NOT NULL,
  `panel_date` datetime DEFAULT NULL,
  `re_panel_date` datetime DEFAULT NULL,
  `old_memorandum_id` varchar(1000) CHARACTER SET utf8 NOT NULL,
  `movement_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `come_from` enum('Entry','Offer Reject','After Retier','Cancel by DG','Rest') DEFAULT NULL,
  `move_to` enum('Emboded','Blacklist','Offer Reject','Offer Cancel','Rest','Free','Offer','Blocklist') DEFAULT NULL,
  `comment` varchar(1000) DEFAULT NULL,
  `direct_status` int(4) NOT NULL DEFAULT '0',
  `action_user_id` int(10) NOT NULL DEFAULT '1',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `go_panel_position` int(11) DEFAULT '0',
  `re_panel_position` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ansar_id` (`ansar_id`)
) ENGINE=InnoDB AUTO_INCREMENT=481447 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_particular_skill` */

DROP TABLE IF EXISTS `tbl_particular_skill`;

CREATE TABLE `tbl_particular_skill` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `skill_name_eng` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `skill_name_bng` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_promotion_ansar_status_info` */

DROP TABLE IF EXISTS `tbl_promotion_ansar_status_info`;

CREATE TABLE `tbl_promotion_ansar_status_info` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `circular_id` int(10) DEFAULT NULL,
  `ansar_id` int(10) DEFAULT NULL,
  `status` enum('embodied_status','pannel_status','free_status','rest_status','freezing_status','offer_block_status','offer_sms_status','offered_status') COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `tbl_receive_sms_log` */

DROP TABLE IF EXISTS `tbl_receive_sms_log`;

CREATE TABLE `tbl_receive_sms_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_no` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sms_body` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `tbl_request_history` */

DROP TABLE IF EXISTS `tbl_request_history`;

CREATE TABLE `tbl_request_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `request_ip` varchar(50) DEFAULT 'NULL',
  `request_url` varchar(255) DEFAULT NULL,
  `request_data` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `header` text,
  `response_data` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=43720 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_request_log_in_user` */

DROP TABLE IF EXISTS `tbl_request_log_in_user`;

CREATE TABLE `tbl_request_log_in_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `tbl_rest_info` */

DROP TABLE IF EXISTS `tbl_rest_info`;

CREATE TABLE `tbl_rest_info` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `ansar_id` int(10) unsigned NOT NULL,
  `old_embodiment_id` int(4) unsigned NOT NULL,
  `memorandum_id` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `rest_date` date DEFAULT NULL,
  `active_date` date DEFAULT NULL COMMENT 'add 360 days here for 2 reason',
  `disembodiment_reason_id` int(4) NOT NULL,
  `total_service_days` int(4) DEFAULT NULL,
  `rest_form` enum('Regular','Force from Freeze','Panel','Block') DEFAULT NULL,
  `comment` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `action_user_id` int(4) NOT NULL DEFAULT '1',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ansar_id_rest` (`ansar_id`),
  KEY `disembodiment_reason_id_rest` (`disembodiment_reason_id`),
  KEY `search_mem` (`memorandum_id`)
) ENGINE=InnoDB AUTO_INCREMENT=73198 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_rest_info_log` */

DROP TABLE IF EXISTS `tbl_rest_info_log`;

CREATE TABLE `tbl_rest_info_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `old_rest_id` int(4) unsigned NOT NULL,
  `old_embodiment_id` int(4) unsigned NOT NULL,
  `old_memorandum_id` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `ansar_id` int(4) unsigned NOT NULL,
  `rest_date` date DEFAULT NULL,
  `total_service_days` int(4) DEFAULT NULL,
  `rest_type` enum('Regular','Force from Freeze','Panel') DEFAULT NULL,
  `disembodiment_reason_id` int(4) NOT NULL,
  `comment` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `move_to` enum('Panel','Re-Emboded','Parmanenet Retierment','Blacklist','Offer','Blocklist') DEFAULT NULL,
  `move_date` date DEFAULT NULL,
  `direct_status` int(4) NOT NULL DEFAULT '0',
  `action_user_id` int(10) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `ansar_id` (`ansar_id`)
) ENGINE=InnoDB AUTO_INCREMENT=63630 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_service_extension` */

DROP TABLE IF EXISTS `tbl_service_extension`;

CREATE TABLE `tbl_service_extension` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `embodiment_id` int(4) unsigned NOT NULL,
  `ansar_id` int(15) NOT NULL,
  `pre_service_ended_date` date NOT NULL,
  `new_extended_date` date NOT NULL,
  `service_extension_comment` varchar(1000) NOT NULL,
  `action_user_id` int(4) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `embodiment_id_service_extension` (`embodiment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_sesson` */

DROP TABLE IF EXISTS `tbl_sesson`;

CREATE TABLE `tbl_sesson` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `session_year` year(4) DEFAULT NULL,
  `session_start_month` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `session_end_month` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `session_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_sms_offer_info` */

DROP TABLE IF EXISTS `tbl_sms_offer_info`;

CREATE TABLE `tbl_sms_offer_info` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `message_id` varchar(255) NOT NULL,
  `ansar_id` int(10) unsigned NOT NULL,
  `sms_send_datetime` datetime NOT NULL,
  `sms_end_datetime` datetime NOT NULL,
  `district_id` int(4) unsigned DEFAULT NULL COMMENT 'value come from tbl_district against KPI',
  `sms_status` enum('Queue','Send','Delivered','Failed') DEFAULT 'Queue',
  `come_from` varchar(255) DEFAULT NULL,
  `sms_try` int(4) DEFAULT '0',
  `action_user_id` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `err_msg` text,
  `memo_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ansar_id_sms_offer_info` (`ansar_id`),
  KEY `uni_id_sms_offer_info` (`district_id`),
  KEY `sms_status` (`sms_status`)
) ENGINE=InnoDB AUTO_INCREMENT=387861 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_sms_receive_info` */

DROP TABLE IF EXISTS `tbl_sms_receive_info`;

CREATE TABLE `tbl_sms_receive_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ansar_id` int(10) unsigned NOT NULL,
  `sms_received_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sms_send_datetime` datetime NOT NULL,
  `sms_end_datetime` datetime DEFAULT NULL,
  `sms_status` enum('ACCEPTED','REJECTED') NOT NULL,
  `offered_district` int(4) unsigned NOT NULL,
  `embodiment_status` int(4) NOT NULL DEFAULT '0' COMMENT 'Default "0"',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `memo_id` mediumtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ansar_id_sms_receive_info` (`ansar_id`),
  KEY `unit_id_sms_receive_info` (`offered_district`)
) ENGINE=InnoDB AUTO_INCREMENT=82139 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_sms_send_log` */

DROP TABLE IF EXISTS `tbl_sms_send_log`;

CREATE TABLE `tbl_sms_send_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ansar_id` int(11) NOT NULL,
  `sms_offer_id` int(10) NOT NULL COMMENT 'this value come from table tbl_sms_offer_info.',
  `mobile_no` varchar(20) NOT NULL,
  `offer_status` tinyint(1) DEFAULT '0' COMMENT 'this value track, is offer still active or not. initial active value 1 till 24 hours.',
  `offered_date` datetime NOT NULL,
  `offered_district` int(11) NOT NULL,
  `reply_type` enum('No Reply','Yes','No') DEFAULT 'No Reply',
  `action_user_id` int(4) NOT NULL DEFAULT '1',
  `action_date` datetime DEFAULT NULL,
  `sms_info` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `comment` varchar(1000) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `memo_id` mediumtext,
  PRIMARY KEY (`id`),
  KEY `ansar_id` (`ansar_id`),
  KEY `reply_type` (`reply_type`),
  KEY `offer_status` (`offer_status`)
) ENGINE=InnoDB AUTO_INCREMENT=543541 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_sub_training_info` */

DROP TABLE IF EXISTS `tbl_sub_training_info`;

CREATE TABLE `tbl_sub_training_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `main_training_info_id` int(11) DEFAULT NULL,
  `training_name_bng` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `training_name_eng` varchar(255) DEFAULT NULL,
  `action_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_system_setting` */

DROP TABLE IF EXISTS `tbl_system_setting`;

CREATE TABLE `tbl_system_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_name` varchar(255) NOT NULL,
  `setting_slug` varchar(255) NOT NULL,
  `setting_value` text,
  `active` tinyint(1) DEFAULT '1',
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_thana` */

DROP TABLE IF EXISTS `tbl_thana`;

CREATE TABLE `tbl_thana` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `division_id` int(4) unsigned NOT NULL COMMENT 'this value is come from tbl_division',
  `unit_id` int(4) unsigned NOT NULL COMMENT 'this value is come from tbl_unit',
  `unit_code` int(11) NOT NULL,
  `thana_code` varchar(100) NOT NULL,
  `thana_name_eng` varchar(50) DEFAULT NULL,
  `thana_name_bng` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `division_id_thana_info` (`division_id`),
  KEY `unit_id_thana_info` (`unit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=677 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_transfer_ansar` */

DROP TABLE IF EXISTS `tbl_transfer_ansar`;

CREATE TABLE `tbl_transfer_ansar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `embodiment_id` int(11) NOT NULL DEFAULT '0',
  `transfer_memorandum_id` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `present_kpi_id` int(11) NOT NULL DEFAULT '0',
  `present_kpi_join_date` datetime DEFAULT '0000-00-00 00:00:00',
  `transfered_kpi_id` int(11) NOT NULL DEFAULT '0',
  `transfered_kpi_join_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `action_by` int(11) NOT NULL DEFAULT '1',
  `ansar_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `JOIN_INDEX` (`transfer_memorandum_id`,`present_kpi_id`,`transfered_kpi_id`),
  KEY `ansar_id` (`ansar_id`)
) ENGINE=InnoDB AUTO_INCREMENT=344980 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_unions` */

DROP TABLE IF EXISTS `tbl_unions`;

CREATE TABLE `tbl_unions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `division_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `thana_id` int(11) NOT NULL,
  `union_name_eng` varchar(255) DEFAULT NULL,
  `union_name_bng` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `action_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `division` (`division_id`),
  KEY `unit` (`unit_id`),
  KEY `thana` (`thana_id`),
  KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=5838 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_unit_company` */

DROP TABLE IF EXISTS `tbl_unit_company`;

CREATE TABLE `tbl_unit_company` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_id` int(11) DEFAULT NULL,
  `ansar_id` int(11) DEFAULT NULL,
  `action_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `requested_type` enum('add','remove','solved') DEFAULT 'solved',
  `status` enum('requested','approved') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_unit_company_ansar_list` */

DROP TABLE IF EXISTS `tbl_unit_company_ansar_list`;

CREATE TABLE `tbl_unit_company_ansar_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_id` int(11) DEFAULT NULL,
  `ansar_id` int(11) DEFAULT NULL,
  `action_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `requested_type` char(18) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `rejected_by` int(11) DEFAULT NULL,
  `comment` text,
  `request_comment` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_unit_company_log` */

DROP TABLE IF EXISTS `tbl_unit_company_log`;

CREATE TABLE `tbl_unit_company_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_id` int(11) DEFAULT NULL,
  `old_company_id` int(11) DEFAULT NULL,
  `ansar_id` int(11) DEFAULT NULL,
  `remove_date` datetime DEFAULT NULL,
  `remove_reason` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_units` */

DROP TABLE IF EXISTS `tbl_units`;

CREATE TABLE `tbl_units` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `division_id` int(4) unsigned NOT NULL COMMENT 'This field value come from tbl_division',
  `division_code` int(11) NOT NULL,
  `unit_code` varchar(100) NOT NULL COMMENT 'code of particuler district or zone',
  `unit_name_eng` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `unit_name_bng` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `action_user_id` int(10) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `division_id_unit_info` (`division_id`),
  KEY `dc` (`division_code`),
  KEY `uc` (`unit_code`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_user` */

DROP TABLE IF EXISTS `tbl_user`;

CREATE TABLE `tbl_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(255) NOT NULL,
  `type` int(11) NOT NULL,
  `district_id` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `division_id` int(11) DEFAULT NULL,
  `rec_district_id` int(11) DEFAULT NULL,
  `user_parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `visible` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `user_name` (`user_name`),
  KEY `type` (`type`),
  KEY `district_id` (`district_id`),
  KEY `status` (`status`),
  KEY `division_id` (`division_id`)
) ENGINE=InnoDB AUTO_INCREMENT=359 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_user_action_log` */

DROP TABLE IF EXISTS `tbl_user_action_log`;

CREATE TABLE `tbl_user_action_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ansar_id` int(11) NOT NULL,
  `action_type` enum('EMBODIED','PANELED','SEND OFFER','CANCEL OFFER','DISEMBODIMENT','BLOCKED','BLACKED','FREEZE','CANCEL PANEL','ADD ENTRY','EDIT ENTRY','SAVE DRAFT','VERIFIED','REJECT','ADD KPI','WITHDRAW KPI','REDUCE KPI','EDIT KPI','UNBLOCKED','UNBLACKED','TRANSFER','DIRECT OFFER','DIRECT PANEl','DIRECT EMBODIMENT','DIRECT DISEMBODIMENT','DIRECT TRANSFER','DIRECT CANCEL PANEL','BLOCK USER','UNBLOCK USER','CREATE USER','EDIT USER PERMISSION','DISEMBODIMENT DATE CORRECTION','OFFER BLOCK') NOT NULL,
  `from_state` varchar(100) DEFAULT NULL,
  `to_state` varchar(100) DEFAULT NULL,
  `action_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `ansar_id` (`ansar_id`),
  KEY `action_by` (`action_by`),
  KEY `action_type` (`action_type`)
) ENGINE=InnoDB AUTO_INCREMENT=739155 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_user_category` */

DROP TABLE IF EXISTS `tbl_user_category`;

CREATE TABLE `tbl_user_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_user_creation_request` */

DROP TABLE IF EXISTS `tbl_user_creation_request`;

CREATE TABLE `tbl_user_creation_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `last_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mobile_no` varchar(20) DEFAULT NULL,
  `user_type` enum('dataentry','verifier','accountant','office_assistance') DEFAULT NULL,
  `status` enum('pending','approved','canceled') DEFAULT 'pending',
  `user_parent_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_user_details` */

DROP TABLE IF EXISTS `tbl_user_details`;

CREATE TABLE `tbl_user_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `office_phone_no` varchar(20) NOT NULL,
  `mobile_no` varchar(20) NOT NULL,
  `contact_address` varchar(255) NOT NULL,
  `rank` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `bank_account_no` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `branch_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=355 DEFAULT CHARSET=utf8;

/*Table structure for table `tbl_user_info_for_otp_sms` */

DROP TABLE IF EXISTS `tbl_user_info_for_otp_sms`;

CREATE TABLE `tbl_user_info_for_otp_sms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `office_phone_no` varchar(20) NOT NULL,
  `mobile_no` varchar(20) NOT NULL,
  `contact_address` varchar(255) NOT NULL,
  `rank` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `bank_account_no` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `branch_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=349 DEFAULT CHARSET=utf8;

/*Table structure for table `tbl_user_log` */

DROP TABLE IF EXISTS `tbl_user_log`;

CREATE TABLE `tbl_user_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `login_status` tinyint(4) NOT NULL DEFAULT '0',
  `ip_addr` varchar(50) NOT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `user_status` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=415 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_user_login_success_log` */

DROP TABLE IF EXISTS `tbl_user_login_success_log`;

CREATE TABLE `tbl_user_login_success_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `otp_generated_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `tbl_user_otp` */

DROP TABLE IF EXISTS `tbl_user_otp`;

CREATE TABLE `tbl_user_otp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_otp_number` varchar(100) NOT NULL,
  `resend_code_count` int(11) DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `otp_generated_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `tbl_user_otp_log` */

DROP TABLE IF EXISTS `tbl_user_otp_log`;

CREATE TABLE `tbl_user_otp_log` (
  `id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `otp_generated_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `tbl_user_permisson` */

DROP TABLE IF EXISTS `tbl_user_permisson`;

CREATE TABLE `tbl_user_permisson` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `permission_type` tinyint(4) NOT NULL DEFAULT '0',
  `permission_list` longtext,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=355 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_user_type` */

DROP TABLE IF EXISTS `tbl_user_type`;

CREATE TABLE `tbl_user_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_code` int(11) NOT NULL,
  `type_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type_code` (`type_code`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_vdp_ansar_info` */

DROP TABLE IF EXISTS `tbl_vdp_ansar_info`;

CREATE TABLE `tbl_vdp_ansar_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `geo_id` varchar(255) DEFAULT NULL,
  `ansar_name_bng` varchar(255) DEFAULT NULL,
  `vdp_name_eng` varchar(255) DEFAULT NULL,
  `father_name_bng` varchar(255) DEFAULT NULL,
  `mother_name_bng` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `marital_status` enum('Married','Unmarried') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `national_id_no` varchar(100) DEFAULT NULL,
  `smart_card_id` int(10) DEFAULT NULL,
  `division_id` int(4) unsigned NOT NULL COMMENT 'This field value come from tbl_division',
  `unit_id` int(4) unsigned NOT NULL COMMENT 'This field value come from tbl_unit',
  `thana_id` int(4) unsigned NOT NULL COMMENT 'This field value come from tbl_thana',
  `union_id` int(11) DEFAULT NULL,
  `union_word_text` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `own_district_id` int(11) DEFAULT NULL,
  `blood_group_id` int(4) unsigned DEFAULT NULL COMMENT 'This field value come from tbl_blood_group',
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `mobile_no_self` varchar(100) DEFAULT NULL,
  `profile_pic` varchar(250) NOT NULL,
  `sign_pic` varchar(255) DEFAULT NULL,
  `status` enum('new','verified','approved') DEFAULT 'new',
  `action_user_id` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ANSAR_SERIAL` (`id`),
  KEY `FOREIGN_KEY_DIVISION_ID_WITH_ID_FROM_TBL_DIVISION` (`division_id`),
  KEY `FOREIGN_KEY_DESIGNATION_ID_WITH_ID_FROM_TBL_DESIGNATION` (`designation`),
  KEY `FOREIGN_KEY_THANA_ID_WITH_ID_FROM_TBL_THANA` (`thana_id`),
  KEY `FOREIGN_KEY_UNIT_ID_WITH_ID_FROM_TBL_UNIT` (`unit_id`),
  KEY `FOREIGN_KEY_BLOOD_GROUP_ID_WITH_ID_FROM_TBL_BLOOD_GROUP` (`blood_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Table structure for table `tbl_vdp_designation` */

DROP TABLE IF EXISTS `tbl_vdp_designation`;

CREATE TABLE `tbl_vdp_designation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `designation_name_bng` varchar(100) DEFAULT NULL,
  `designation_name_eng` varchar(100) DEFAULT NULL,
  `priority` int(11) DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `card_color` enum('red','green') DEFAULT 'red',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

/*Table structure for table `temp_number` */

DROP TABLE IF EXISTS `temp_number`;

CREATE TABLE `temp_number` (
  `n` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
