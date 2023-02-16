/*
SQLyog Community v13.1.8 (64 bit)
MySQL - 10.1.37-MariaDB : Database - ansar_recruitment
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`ansar_recruitment` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `ansar_recruitment`;

/*Table structure for table `job_accepted_applicant` */

DROP TABLE IF EXISTS `job_accepted_applicant`;

CREATE TABLE `job_accepted_applicant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `applicant_id` varchar(255) NOT NULL,
  `action_user_id` int(11) NOT NULL,
  `message` varchar(255) DEFAULT NULL,
  `message_status` enum('pending','send') DEFAULT 'pending',
  `sms_status` enum('on','off') DEFAULT NULL,
  `comment` text CHARACTER SET utf8,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `app` (`applicant_id`),
  KEY `ms` (`message_status`),
  KEY `ss` (`sms_status`)
) ENGINE=InnoDB AUTO_INCREMENT=25114 DEFAULT CHARSET=latin1;

/*Table structure for table `job_applicant` */

DROP TABLE IF EXISTS `job_applicant`;

CREATE TABLE `job_applicant` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `job_circular_id` int(11) NOT NULL,
  `applicant_id` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `ansar_id` int(11) DEFAULT NULL,
  `applicant_password` varchar(255) DEFAULT NULL,
  `roll_no` varchar(20) DEFAULT NULL,
  `applicant_name_bng` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `applicant_name_eng` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `father_name_bng` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `father_name_eng` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `mother_name_bng` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `mother_name_eng` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `spouse_name_eng` varchar(255) DEFAULT NULL,
  `spouse_name_bng` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `marital_status` enum('Married','Unmarried','Other') DEFAULT NULL,
  `other_marital_status` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `religion` enum('Islam','Traditional','Buddhists','Christians','Other') DEFAULT NULL,
  `nationality` enum('Bangladeshi') DEFAULT 'Bangladeshi',
  `eyesight` varchar(100) DEFAULT 'NULL',
  `quota_details` text CHARACTER SET utf8,
  `present_house_road_number` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `house_road_number` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `present_post_code_number` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `post_code_number` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `present_division_id` int(11) DEFAULT NULL,
  `present_unit_id` int(11) DEFAULT NULL,
  `present_thana_id` int(11) DEFAULT NULL,
  `fb_id` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `email_id` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `national_id_no` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `birth_certificate_no` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `country_id_type` enum('national_id_no','birth_certificate_no') NOT NULL DEFAULT 'national_id_no',
  `division_id` int(4) unsigned DEFAULT NULL COMMENT 'This field value come from tbl_division',
  `unit_id` int(4) unsigned DEFAULT NULL COMMENT 'This field value come from tbl_unit',
  `thana_id` int(4) unsigned DEFAULT NULL COMMENT 'This field value come from tbl_thana',
  `post_office_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `post_office_name_bng` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `village_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `village_name_bng` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `union_name_eng` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `union_name_bng` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `height_feet` int(4) DEFAULT NULL,
  `height_inch` float DEFAULT NULL,
  `chest_normal` double DEFAULT NULL,
  `chest_extended` double DEFAULT NULL,
  `weight` double DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `mobile_no_self` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `training_info` varchar(255) DEFAULT NULL,
  `technical_training` tinyint(2) DEFAULT '0',
  `sports` tinyint(2) DEFAULT '0',
  `connection_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `connection_relation` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `connection_address` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `connection_mobile_no` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `skill_id` int(11) NOT NULL DEFAULT '0',
  `own_particular_skill` varchar(255) DEFAULT NULL,
  `profile_pic` varchar(200) DEFAULT NULL,
  `status` enum('paid','pending','applied','accepted','rejected','selected','initial') NOT NULL DEFAULT 'initial',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `return_url` varchar(255) DEFAULT NULL,
  `can_use_smart_phone` tinyint(1) NOT NULL DEFAULT '0',
  `have_own_smart_phone` tinyint(1) NOT NULL DEFAULT '0',
  `special_performance` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `extra_activities` text CHARACTER SET utf8,
  `signature_pic` varchar(255) DEFAULT NULL,
  `circular_applicant_quota_id` int(11) NOT NULL DEFAULT '0',
  `experience` text CHARACTER SET utf8,
  `computer_knowledge` text CHARACTER SET utf8,
  `divisional_candidate` enum('yes','no','empty') DEFAULT 'empty',
  `present_post_office_name_bng` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `present_village_name_bng` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `present_union_name_bng` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `vdp_leader` tinyint(1) DEFAULT '0',
  `govt_job_status` tinyint(1) DEFAULT '0',
  `govt_job_post` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `govt_job_institute` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`,`circular_applicant_quota_id`),
  UNIQUE KEY `ANSAR_SERIAL` (`id`,`applicant_id`),
  UNIQUE KEY `unique_validation` (`job_circular_id`,`mobile_no_self`),
  UNIQUE KEY `circular_unique_roll` (`job_circular_id`,`roll_no`) COMMENT 'duplicate roll number in circular resricted',
  KEY `FOREIGN_KEY_DIVISION_ID_WITH_ID_FROM_TBL_DIVISION` (`division_id`) USING BTREE,
  KEY `FOREIGN_KEY_DESIGNATION_ID_WITH_ID_FROM_TBL_DESIGNATION` (`designation`(1)) USING BTREE,
  KEY `FOREIGN_KEY_THANA_ID_WITH_ID_FROM_TBL_THANA` (`thana_id`) USING BTREE,
  KEY `FOREIGN_KEY_UNIT_ID_WITH_ID_FROM_TBL_UNIT` (`unit_id`) USING BTREE,
  KEY `FOREIGN_KEY_SKILL_ID_WITH_ID_FROM_TBL_PARTICULAR_SKILL` (`skill_id`) USING BTREE,
  KEY `job_circular_id` (`job_circular_id`),
  KEY `statussss` (`status`),
  KEY `height_feet` (`height_feet`),
  KEY `height_inch` (`height_inch`),
  KEY `gender` (`gender`),
  KEY `dob` (`date_of_birth`),
  KEY `applicant_id` (`applicant_id`),
  KEY `nid` (`national_id_no`),
  KEY `mobile` (`mobile_no_self`),
  KEY `mobile_status_job` (`job_circular_id`,`mobile_no_self`,`status`),
  CONSTRAINT `job_applicant_ibfk_1` FOREIGN KEY (`job_circular_id`) REFERENCES `job_circular` (`id`) ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=894957 DEFAULT CHARSET=latin1;

/*Table structure for table `job_applicant_edit_history` */

DROP TABLE IF EXISTS `job_applicant_edit_history`;

CREATE TABLE `job_applicant_edit_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `new_data` text CHARACTER SET utf8,
  `previous_data` text CHARACTER SET utf8,
  `action_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `applicant_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `applicant_id` (`applicant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19216 DEFAULT CHARSET=latin1;

/*Table structure for table `job_applicant_exam_center` */

DROP TABLE IF EXISTS `job_applicant_exam_center`;

CREATE TABLE `job_applicant_exam_center` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_circular_id` int(11) DEFAULT NULL,
  `selection_date` date DEFAULT NULL,
  `selection_place` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `written_date` date DEFAULT NULL,
  `written_viva_place` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `units` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `action_user_id` int(11) DEFAULT NULL,
  `written_time` varchar(55) DEFAULT NULL,
  `selection_time` varchar(55) DEFAULT NULL,
  `viva_date` date DEFAULT NULL,
  `viva_time` varchar(55) DEFAULT NULL,
  `viva_present_time` varchar(55) DEFAULT NULL,
  `written_present_time` varchar(55) DEFAULT NULL,
  `selection_present_time` varchar(55) DEFAULT NULL,
  `exam_place_roll_wise` text CHARACTER SET utf8,
  PRIMARY KEY (`id`),
  KEY `job_circular_id` (`job_circular_id`)
) ENGINE=InnoDB AUTO_INCREMENT=276 DEFAULT CHARSET=latin1;

/*Table structure for table `job_applicant_exam_center_units` */

DROP TABLE IF EXISTS `job_applicant_exam_center_units`;

CREATE TABLE `job_applicant_exam_center_units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_center_id` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `exam_center_id` (`exam_center_id`),
  KEY `unit_id` (`unit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6010 DEFAULT CHARSET=latin1;

/*Table structure for table `job_applicant_hrm_details` */

DROP TABLE IF EXISTS `job_applicant_hrm_details`;

CREATE TABLE `job_applicant_hrm_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_id` varchar(255) NOT NULL,
  `job_circular_id` int(11) DEFAULT NULL,
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
  `skin_color_bng` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `identification_mark_bng` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `criminal_case_bng` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `applicant_nominee_info` text CHARACTER SET utf8,
  `applicant_training_info` text CHARACTER SET utf8,
  `appliciant_education_info` text CHARACTER SET utf8,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `ansar_id` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ANSAR_SERIAL` (`id`),
  UNIQUE KEY `INSERT_UNIQUE` (`applicant_id`,`job_circular_id`),
  KEY `FOREIGN_KEY_DIVISION_ID_WITH_ID_FROM_TBL_DIVISION` (`division_id`),
  KEY `FOREIGN_KEY_DESIGNATION_ID_WITH_ID_FROM_TBL_DESIGNATION` (`designation_id`),
  KEY `FOREIGN_KEY_THANA_ID_WITH_ID_FROM_TBL_THANA` (`thana_id`),
  KEY `FOREIGN_KEY_UNIT_ID_WITH_ID_FROM_TBL_UNIT` (`unit_id`),
  KEY `FOREIGN_KEY_BLOOD_GROUP_ID_WITH_ID_FROM_TBL_BLOOD_GROUP` (`blood_group_id`),
  KEY `FOREIGN_KEY_DISEASE_ID_WITH_ID_FROM_TBL_LONGTIME_DISEASE` (`disease_id`),
  KEY `FOREIGN_KEY_SKILL_ID_WITH_ID_FROM_TBL_PARTICULAR_SKILL` (`skill_id`),
  KEY `job_circular_id` (`job_circular_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20691 DEFAULT CHARSET=latin1;

/*Table structure for table `job_applicant_marks` */

DROP TABLE IF EXISTS `job_applicant_marks`;

CREATE TABLE `job_applicant_marks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `applicant_id` varchar(255) DEFAULT NULL,
  `written` float DEFAULT NULL,
  `edu_training` float DEFAULT NULL,
  `edu_experience` float DEFAULT NULL,
  `physical_age` float DEFAULT NULL,
  `viva` float DEFAULT NULL,
  `physical` double DEFAULT NULL,
  `is_bn_candidate` tinyint(1) DEFAULT '0',
  `specialized` tinyint(1) DEFAULT '0',
  `action_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `additional_marks` text,
  `total_aditional_marks` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuuuu` (`applicant_id`),
  KEY `written` (`written`),
  KEY `edu_training` (`edu_training`),
  KEY `edu_exprience` (`edu_experience`),
  KEY `physical_age` (`physical_age`),
  KEY `viva` (`viva`),
  KEY `physical` (`physical`),
  KEY `special` (`is_bn_candidate`,`specialized`),
  KEY `tam` (`total_aditional_marks`)
) ENGINE=InnoDB AUTO_INCREMENT=38271 DEFAULT CHARSET=latin1;

/*Table structure for table `job_applicant_nid_request` */

DROP TABLE IF EXISTS `job_applicant_nid_request`;

CREATE TABLE `job_applicant_nid_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `applicant_id` int(11) DEFAULT NULL,
  `nid_data` varchar(100) DEFAULT NULL,
  `dob` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `job_applicant_points` */

DROP TABLE IF EXISTS `job_applicant_points`;

CREATE TABLE `job_applicant_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_circular_id` int(11) DEFAULT NULL,
  `point_for` enum('physical','edu_training','edu_experience','physical_age') DEFAULT NULL,
  `rule_name` enum('height','education','training','experience','age') DEFAULT NULL,
  `rules` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `action_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_KEY` (`job_circular_id`),
  KEY `pf` (`point_for`),
  KEY `rn` (`rule_name`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

/*Table structure for table `job_applicant_quota` */

DROP TABLE IF EXISTS `job_applicant_quota`;

CREATE TABLE `job_applicant_quota` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_circular_quota_id` int(11) DEFAULT NULL,
  `range_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `male` int(11) DEFAULT '0',
  `female` int(11) DEFAULT '0',
  `waiting_male` int(11) DEFAULT NULL,
  `waiting_female` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quota_id` (`job_circular_quota_id`),
  KEY `range` (`range_id`),
  KEY `district` (`district_id`),
  KEY `male` (`male`),
  KEY `female` (`female`),
  KEY `wm` (`waiting_male`),
  KEY `wf` (`waiting_female`)
) ENGINE=InnoDB AUTO_INCREMENT=208 DEFAULT CHARSET=latin1;

/*Table structure for table `job_applicant_training_date` */

DROP TABLE IF EXISTS `job_applicant_training_date`;

CREATE TABLE `job_applicant_training_date` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_circular_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `action_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_circular_id` (`job_circular_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Table structure for table `job_application_instruction` */

DROP TABLE IF EXISTS `job_application_instruction`;

CREATE TABLE `job_application_instruction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instruction` longtext CHARACTER SET utf8,
  `type` enum('welcome_message','instruction_message') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Table structure for table `job_appliciant_education_info` */

DROP TABLE IF EXISTS `job_appliciant_education_info`;

CREATE TABLE `job_appliciant_education_info` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `job_education_id` int(11) NOT NULL DEFAULT '0',
  `job_applicant_id` int(11) unsigned NOT NULL COMMENT 'This ID come from tbl_ansar_personal_info',
  `name_of_degree` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `name_of_degree_eng` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `institute_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `institute_name_eng` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `passing_year` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `passing_year_eng` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `gade_divission` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `gade_divission_eng` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `board_university` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `subject` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FOREIGN_KEY_EDUCATION_ANSAR_ID_WITH_PERSONAL_INFO_ANSAR_ID` (`job_applicant_id`) USING BTREE,
  KEY `education_id` (`job_education_id`),
  CONSTRAINT `job_appliciant_education_info_ibfk_1` FOREIGN KEY (`job_education_id`) REFERENCES `job_education_info` (`id`) ON UPDATE NO ACTION,
  CONSTRAINT `job_appliciant_education_info_ibfk_2` FOREIGN KEY (`job_applicant_id`) REFERENCES `job_applicant` (`id`) ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1385697 DEFAULT CHARSET=latin1;

/*Table structure for table `job_appliciant_payment_history` */

DROP TABLE IF EXISTS `job_appliciant_payment_history`;

CREATE TABLE `job_appliciant_payment_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_appliciant_id` varchar(50) NOT NULL,
  `txID` varchar(255) DEFAULT NULL,
  `returntxID` varchar(255) DEFAULT NULL,
  `bankTxStatus` varchar(255) DEFAULT NULL,
  `bankTxID` varchar(255) DEFAULT NULL,
  `txnAmount` varchar(255) DEFAULT NULL,
  `spCode` varchar(255) DEFAULT NULL,
  `paymentOption` varchar(255) DEFAULT NULL,
  `spCodeDes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `pay_amount` float DEFAULT NULL,
  `spOrderID` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `search` (`job_appliciant_id`,`txID`,`bankTxStatus`),
  KEY `bankTxStatus` (`bankTxStatus`),
  KEY `job_applicant_id` (`job_appliciant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9544790 DEFAULT CHARSET=latin1;

/*Table structure for table `job_category` */

DROP TABLE IF EXISTS `job_category`;

CREATE TABLE `job_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name_eng` varchar(255) NOT NULL,
  `category_name_bng` varchar(255) DEFAULT NULL,
  `category_description` varchar(255) DEFAULT NULL,
  `category_rank` varchar(255) DEFAULT NULL,
  `category_type` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `action_user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `category_header` varchar(255) DEFAULT NULL,
  `category_conditions` text,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `type` (`category_type`),
  KEY `rank` (`category_rank`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

/*Table structure for table `job_circular` */

DROP TABLE IF EXISTS `job_circular`;

CREATE TABLE `job_circular` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_category_id` int(11) NOT NULL,
  `circular_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `circular_code` varchar(25) CHARACTER SET utf8 DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8 NOT NULL DEFAULT 'inactive',
  `auto_terminate` tinyint(1) DEFAULT '0',
  `applicatn_range` text,
  `applicatn_units` text,
  `pay_amount` float DEFAULT NULL,
  `payment_status` enum('on','off') DEFAULT 'on',
  `application_status` enum('on','off') DEFAULT 'on',
  `circular_status` enum('running','shutdown') DEFAULT 'running',
  `login_status` enum('on','off') DEFAULT 'on',
  `check_unit_before_payment` enum('on','off') CHARACTER SET utf8 DEFAULT 'off',
  `admit_card_print_status` enum('on','off') DEFAULT 'on',
  `submit_problem_status` enum('on','off') DEFAULT 'off',
  `quota_district_division` enum('on','off') NOT NULL DEFAULT 'off',
  `terms_and_conditions` text CHARACTER SET utf8 NOT NULL,
  `admit_card_message` text CHARACTER SET utf8,
  `action_user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `memorandum_no` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `circular_publish_date` date DEFAULT NULL,
  `demo_status` enum('on','off') CHARACTER SET utf8 DEFAULT 'off',
  PRIMARY KEY (`id`),
  KEY `job_category_id` (`job_category_id`) USING BTREE,
  KEY `status` (`status`),
  KEY `cs` (`circular_status`),
  KEY `payment` (`payment_status`),
  KEY `application_status` (`application_status`),
  KEY `login_status` (`login_status`),
  KEY `admit_status` (`admit_card_print_status`),
  CONSTRAINT `job_circular_ibfk_1` FOREIGN KEY (`job_category_id`) REFERENCES `job_category` (`id`) ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=latin1;

/*Table structure for table `job_circular_applicant_quota` */

DROP TABLE IF EXISTS `job_circular_applicant_quota`;

CREATE TABLE `job_circular_applicant_quota` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quota_name_eng` varchar(255) NOT NULL,
  `quota_name_bng` varchar(255) CHARACTER SET utf8 NOT NULL,
  `has_own_form` tinyint(1) DEFAULT '0',
  `form_details` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

/*Table structure for table `job_circular_applicant_quota_relation` */

DROP TABLE IF EXISTS `job_circular_applicant_quota_relation`;

CREATE TABLE `job_circular_applicant_quota_relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_circular_id` int(11) NOT NULL,
  `job_circular_applicant_quota_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `job_circular_id` (`job_circular_id`),
  KEY `job_circular_applicant_quota_id` (`job_circular_applicant_quota_id`),
  CONSTRAINT `job_circular_applicant_quota_relation_ibfk_1` FOREIGN KEY (`job_circular_id`) REFERENCES `job_circular` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `job_circular_applicant_quota_relation_ibfk_2` FOREIGN KEY (`job_circular_applicant_quota_id`) REFERENCES `job_circular_applicant_quota` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5508 DEFAULT CHARSET=latin1;

/*Table structure for table `job_circular_constraint` */

DROP TABLE IF EXISTS `job_circular_constraint`;

CREATE TABLE `job_circular_constraint` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_circular_id` int(11) NOT NULL,
  `constraint` text,
  `action_user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_circular_id` (`job_circular_id`) USING BTREE,
  CONSTRAINT `job_circular_constraint_ibfk_1` FOREIGN KEY (`job_circular_id`) REFERENCES `job_circular` (`id`) ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=latin1;

/*Table structure for table `job_circular_mark_distribution` */

DROP TABLE IF EXISTS `job_circular_mark_distribution`;

CREATE TABLE `job_circular_mark_distribution` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_circular_id` int(11) DEFAULT NULL,
  `physical` float DEFAULT NULL,
  `edu_training` float DEFAULT NULL,
  `edu_experience` float DEFAULT NULL,
  `physical_age` float DEFAULT NULL,
  `written` float DEFAULT NULL,
  `convert_written_mark` float DEFAULT NULL,
  `viva` float DEFAULT NULL,
  `written_pass_mark` float DEFAULT NULL,
  `viva_pass_mark` float DEFAULT NULL,
  `additional_marks` text,
  `action_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `FK_JC` (`job_circular_id`),
  KEY `physical` (`physical`),
  KEY `edu_training` (`edu_training`),
  KEY `edu_exprience` (`edu_experience`),
  KEY `physical_age` (`physical_age`),
  KEY `written` (`written`),
  KEY `cwm` (`convert_written_mark`),
  KEY `viva` (`viva`),
  KEY `wpm` (`written_pass_mark`),
  KEY `vpm` (`viva_pass_mark`),
  CONSTRAINT `FK_JC` FOREIGN KEY (`job_circular_id`) REFERENCES `job_circular` (`id`) ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

/*Table structure for table `job_circular_quota` */

DROP TABLE IF EXISTS `job_circular_quota`;

CREATE TABLE `job_circular_quota` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_circular_id` int(11) DEFAULT NULL,
  `type` char(15) DEFAULT NULL,
  `action_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `circular` (`job_circular_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;

/*Table structure for table `job_education_info` */

DROP TABLE IF EXISTS `job_education_info`;

CREATE TABLE `job_education_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `education_deg_bng` varchar(255) CHARACTER SET utf8 NOT NULL,
  `education_deg_eng` varchar(255) CHARACTER SET utf8 NOT NULL,
  `priority` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `priority` (`priority`),
  KEY `ebng` (`education_deg_bng`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

/*Table structure for table `job_feedback` */

DROP TABLE IF EXISTS `job_feedback`;

CREATE TABLE `job_feedback` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `problem_type` varchar(255) DEFAULT NULL,
  `mobile_no_self` varchar(20) DEFAULT NULL,
  `payment_option` varchar(255) DEFAULT NULL,
  `txid` varchar(100) DEFAULT NULL,
  `sender_no` varchar(20) DEFAULT NULL,
  `national_id_no` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `status` enum('reject','verify','pending') DEFAULT 'pending',
  `feed_circular_id` int(11) DEFAULT NULL,
  `comment` text CHARACTER SET utf8,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63625 DEFAULT CHARSET=latin1;

/*Table structure for table `job_hrm_ansar_training_info` */

DROP TABLE IF EXISTS `job_hrm_ansar_training_info`;

CREATE TABLE `job_hrm_ansar_training_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `job_applicant_id` int(10) unsigned NOT NULL,
  `training_designation` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `training_institute_name` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  `training_start_date` date DEFAULT NULL,
  `training_end_date` date DEFAULT NULL,
  `trining_certificate_no` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `job_applicant_id` (`job_applicant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5907 DEFAULT CHARSET=latin1;

/*Table structure for table `job_payment_history` */

DROP TABLE IF EXISTS `job_payment_history`;

CREATE TABLE `job_payment_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_applicant_payment_id` int(50) NOT NULL,
  `txID` varchar(255) DEFAULT NULL,
  `bankTxStatus` varchar(255) DEFAULT NULL,
  `bankTxID` varchar(255) DEFAULT NULL,
  `txnAmount` varchar(255) DEFAULT NULL,
  `spCode` varchar(255) DEFAULT NULL,
  `paymentOption` varchar(255) DEFAULT NULL,
  `spCodeDes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `spOrderID` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `search` (`job_applicant_payment_id`,`txID`,`bankTxStatus`),
  KEY `bankTxStatus` (`bankTxStatus`),
  KEY `job_applicant_payment_id` (`job_applicant_payment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1799034 DEFAULT CHARSET=latin1;

/*Table structure for table `job_quota` */

DROP TABLE IF EXISTS `job_quota`;

CREATE TABLE `job_quota` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `quota_type` enum('son_of_freedom_fighter','grandson_of_freedom_fighter','member_of_ansar_or_vdp','orphan','physically_disabled','tribe') DEFAULT NULL,
  `freedom_fighter_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `freedom_fighter_relation` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `freedom_fighter_address` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `job_applicant_id` int(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `quota_type` (`quota_type`)
) ENGINE=InnoDB AUTO_INCREMENT=12645 DEFAULT CHARSET=latin1;

/*Table structure for table `job_rejected_applicant` */

DROP TABLE IF EXISTS `job_rejected_applicant`;

CREATE TABLE `job_rejected_applicant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `applicant_id` varchar(255) NOT NULL,
  `remark` text CHARACTER SET utf8 NOT NULL,
  `action_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQUE` (`applicant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=157 DEFAULT CHARSET=latin1;

/*Table structure for table `job_selected_applicant` */

DROP TABLE IF EXISTS `job_selected_applicant`;

CREATE TABLE `job_selected_applicant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `applicant_id` varchar(255) DEFAULT NULL,
  `action_user_id` int(11) DEFAULT NULL,
  `message` text,
  `message_status` enum('send','pending') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `sms_status` enum('on','off') DEFAULT 'off',
  PRIMARY KEY (`id`),
  KEY `sms_index` (`applicant_id`,`sms_status`,`message_status`),
  KEY `ss` (`sms_status`),
  KEY `ms` (`message_status`),
  KEY `applicant` (`applicant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=257239 DEFAULT CHARSET=latin1;

/*Table structure for table `job_settings` */

DROP TABLE IF EXISTS `job_settings`;

CREATE TABLE `job_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_type` varchar(255) DEFAULT NULL,
  `field_value` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Table structure for table `jobs` */

DROP TABLE IF EXISTS `jobs`;

CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_reserved_reserved_at_index` (`queue`,`reserved`,`reserved_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Table structure for table `range_relation` */

DROP TABLE IF EXISTS `range_relation`;

CREATE TABLE `range_relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `range_id` int(11) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

/*Table structure for table `sessions` */

DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `session_id` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `expires` int(11) unsigned NOT NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=116112 DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=4231 DEFAULT CHARSET=latin1;

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
  PRIMARY KEY (`id`),
  KEY `FOREIGN_KEY_EDUCATION_ANSAR_ID_WITH_PERSONAL_INFO_ANSAR_ID` (`ansar_id`) USING BTREE,
  KEY `education` (`education_id`)
) ENGINE=InnoDB AUTO_INCREMENT=97664 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_ansar_id_card` */

DROP TABLE IF EXISTS `tbl_ansar_id_card`;

CREATE TABLE `tbl_ansar_id_card` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `ansar_id` int(10) unsigned NOT NULL,
  `type` enum('ENG','BNG') NOT NULL DEFAULT 'ENG',
  `issue_date` date NOT NULL,
  `expire_date` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `rank` varchar(6) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `status` (`status`),
  KEY `issue_date` (`issue_date`),
  KEY `expire_date` (`expire_date`),
  KEY `ansar_id` (`ansar_id`)
) ENGINE=InnoDB AUTO_INCREMENT=100451 DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=83808 DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=83817 DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=97574 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_blacklist_info` */

DROP TABLE IF EXISTS `tbl_blacklist_info`;

CREATE TABLE `tbl_blacklist_info` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `ansar_id` int(10) unsigned NOT NULL,
  `black_list_from` enum('Not Verified','Free','Panel','Offer','Embodied','Rest','Block','Freeze') DEFAULT NULL,
  `from_id` int(4) DEFAULT NULL,
  `black_listed_date` date DEFAULT NULL,
  `black_list_comment` text CHARACTER SET utf8,
  `direct_status` int(4) NOT NULL DEFAULT '0',
  `action_user_id` int(10) NOT NULL DEFAULT '1',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ansar_id_blacklist_info` (`ansar_id`)
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_blacklist_info_log` */

DROP TABLE IF EXISTS `tbl_blacklist_info_log`;

CREATE TABLE `tbl_blacklist_info_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `old_blacklist_id` int(4) unsigned NOT NULL,
  `ansar_id` int(4) unsigned NOT NULL,
  `black_list_from` enum('Entry','Free','Panel','Offer','Embodiment','Rest','Blocklist','Freeze') DEFAULT NULL,
  `from_id` int(4) DEFAULT NULL,
  `black_listed_date` date DEFAULT NULL,
  `black_list_comment` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `unblacklist_date` date NOT NULL,
  `unblacklist_comment` varchar(1000) NOT NULL,
  `move_to` enum('Free','Retierment') DEFAULT NULL,
  `move_date` date DEFAULT NULL,
  `direct_status` int(4) NOT NULL DEFAULT '0',
  `action_user_id` int(10) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `black` (`black_list_from`),
  KEY `move` (`move_to`),
  KEY `ansar_id` (`ansar_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_blocklist_info` */

DROP TABLE IF EXISTS `tbl_blocklist_info`;

CREATE TABLE `tbl_blocklist_info` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `ansar_id` int(10) unsigned NOT NULL,
  `block_list_from` enum('Panel','Embodiment','Rest','Offer','Other') NOT NULL,
  `from_id` int(10) NOT NULL,
  `date_for_block` date DEFAULT NULL,
  `date_for_unblock` date DEFAULT NULL,
  `comment_for_block` text CHARACTER SET utf8,
  `comment_for_unblock` text CHARACTER SET utf8,
  `direct_status` int(4) NOT NULL DEFAULT '0',
  `action_user_id` int(10) NOT NULL DEFAULT '1',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_periodic` tinyint(1) DEFAULT NULL,
  `assigned_unblock_date` date DEFAULT NULL,
  `assigned_unblock_stutus` enum('Not Verified','Panel','Rest','Free') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ansar_id_blocklist_info` (`ansar_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7691 DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=2290 DEFAULT CHARSET=latin1;

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

/*Table structure for table `tbl_dg_action` */

DROP TABLE IF EXISTS `tbl_dg_action`;

CREATE TABLE `tbl_dg_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ansar_id` int(11) NOT NULL,
  `action` enum('FREE','EMBODIED','PANELED','SEND OFFER','CANCEL OFFER','DISEMBODIMENT','BLOCKED','BLACKED','FREEZE','CANCEL PANEL','TRANSFER','REST','UNBLACKED','UNBLOCKED') NOT NULL,
  `from_state` varchar(100) NOT NULL,
  `to_state` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `ansar_id` (`ansar_id`),
  KEY `action` (`action`),
  KEY `from` (`from_state`),
  KEY `to` (`to_state`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `ansar_id` (`ansar_id`),
  KEY `kpi_id_embodiment` (`kpi_id`),
  KEY `mem_search` (`memorandum_id`)
) ENGINE=InnoDB AUTO_INCREMENT=91623 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_embodiment_log` */

DROP TABLE IF EXISTS `tbl_embodiment_log`;

CREATE TABLE `tbl_embodiment_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `old_embodiment_id` int(10) unsigned NOT NULL DEFAULT '0',
  `old_memorandum_id` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `ansar_id` int(4) unsigned NOT NULL,
  `kpi_id` int(4) unsigned NOT NULL DEFAULT '0',
  `reporting_date` date DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `transfered_date` date DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `disembodiment_reason_id` int(10) NOT NULL,
  `move_to` enum('Panel','Freeze','Rest','Blacklist') DEFAULT NULL,
  `service_extension_status` int(10) NOT NULL DEFAULT '0',
  `comment` varchar(1000) NOT NULL DEFAULT 'N/A',
  `direct_status` int(4) NOT NULL DEFAULT '0',
  `action_user_id` int(4) NOT NULL DEFAULT '1',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `search` (`old_memorandum_id`,`kpi_id`),
  KEY `ansar_id` (`ansar_id`),
  KEY `disembo_reason` (`disembodiment_reason_id`)
) ENGINE=InnoDB AUTO_INCREMENT=92210 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_forget_password_request` */

DROP TABLE IF EXISTS `tbl_forget_password_request`;

CREATE TABLE `tbl_forget_password_request` (
  `id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_freezed_ansar_embodiment_details` */

DROP TABLE IF EXISTS `tbl_freezed_ansar_embodiment_details`;

CREATE TABLE `tbl_freezed_ansar_embodiment_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ansar_id` int(11) DEFAULT NULL,
  `freezed_id` int(11) DEFAULT NULL,
  `freezed_kpi_id` int(11) DEFAULT NULL,
  `embodiment_id` int(11) DEFAULT NULL,
  `em_mem_id` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `embodied_date` date DEFAULT NULL,
  `transfer_date` date DEFAULT NULL,
  `reporting_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `service_ended_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ansar_id` (`ansar_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1926 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_freezing_info` */

DROP TABLE IF EXISTS `tbl_freezing_info`;

CREATE TABLE `tbl_freezing_info` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `ansar_id` int(10) unsigned NOT NULL,
  `freez_reason` enum('Auto Withdraw','Guard Withdraw','Guard Reduce','Disciplinary Actions','Pre deployment','Leave without pay') DEFAULT NULL,
  `freez_date` date DEFAULT NULL,
  `comment_on_freez` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `memorandum_id` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `kpi_id` int(4) unsigned NOT NULL DEFAULT '0',
  `ansar_embodiment_id` int(4) DEFAULT NULL,
  `action_user_id` int(11) NOT NULL DEFAULT '1',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ansar_id_freeze` (`ansar_id`),
  KEY `kpi_id_freezing_info` (`kpi_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3284 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_freezing_info_log` */

DROP TABLE IF EXISTS `tbl_freezing_info_log`;

CREATE TABLE `tbl_freezing_info_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `old_freez_id` int(4) unsigned NOT NULL,
  `ansar_id` int(4) unsigned NOT NULL,
  `ansar_embodiment_id` int(11) NOT NULL,
  `freez_reason` enum('Guard Withdraw','Guard Reduce','Disciplinary Actions') DEFAULT NULL,
  `freez_date` date DEFAULT NULL,
  `comment_on_freez` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `move_frm_freez_date` date DEFAULT NULL,
  `move_to` enum('Panel','Emodiment','Blacklist','Blocklist','Retierment','Rest') DEFAULT NULL,
  `comment_on_move` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `direct_status` int(4) NOT NULL DEFAULT '0',
  `action_user_id` int(10) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `ansar_id` (`ansar_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1834 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_global_parameter` */

DROP TABLE IF EXISTS `tbl_global_parameter`;

CREATE TABLE `tbl_global_parameter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `param_name` varchar(255) NOT NULL,
  `param_value` int(11) NOT NULL,
  `param_unit` enum('Day','Month','Year') NOT NULL,
  `param_description` varchar(255) NOT NULL,
  `param_piority` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_ipn_call_log` */

DROP TABLE IF EXISTS `tbl_ipn_call_log`;

CREATE TABLE `tbl_ipn_call_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sp_order_id` tinytext NOT NULL,
  `ip` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `tbl_kpi_detail_info` */

DROP TABLE IF EXISTS `tbl_kpi_detail_info`;

CREATE TABLE `tbl_kpi_detail_info` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `kpi_id` int(4) unsigned NOT NULL,
  `total_ansar_request` int(4) DEFAULT NULL,
  `total_ansar_given` int(4) DEFAULT NULL,
  `with_weapon` tinyint(1) DEFAULT '0',
  `weapon_count` int(4) DEFAULT '0',
  `weapon_description` varchar(1000) DEFAULT NULL,
  `bullet_no` int(10) NOT NULL DEFAULT '0',
  `activation_date` date DEFAULT NULL,
  `withdraw_date` date DEFAULT NULL,
  `kpi_withdraw_date` date DEFAULT NULL,
  `kpi_withdraw_mem_id` varchar(255) DEFAULT NULL,
  `kpi_withdraw_cancel_mem_id` varchar(255) DEFAULT NULL,
  `kpi_withdraw_date_update_mem_id` varchar(255) DEFAULT NULL,
  `no_of_ansar` int(4) DEFAULT '0',
  `no_of_apc` int(4) DEFAULT '0',
  `no_of_pc` int(4) DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_special_kpi` tinyint(4) NOT NULL DEFAULT '0',
  `special_amount` float NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kpi_id_details` (`kpi_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7756 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_kpi_info` */

DROP TABLE IF EXISTS `tbl_kpi_info`;

CREATE TABLE `tbl_kpi_info` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `kpi_name_eng` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `kpi_name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `division_id` int(4) unsigned NOT NULL COMMENT 'this valu come from tbl_division',
  `unit_id` int(4) unsigned NOT NULL COMMENT 'this valu come from tbl_unit',
  `thana_id` int(4) unsigned NOT NULL COMMENT 'this valu come from tbl_thana',
  `kpi_address` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `kpi_contact_no` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `status_of_kpi` tinyint(1) DEFAULT '0',
  `withdraw_status` int(10) NOT NULL DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `division_id_kpi_info` (`division_id`),
  KEY `thana_id_kpi_info` (`thana_id`),
  KEY `unit_id_kpi_info` (`unit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7766 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_kpi_log` */

DROP TABLE IF EXISTS `tbl_kpi_log`;

CREATE TABLE `tbl_kpi_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `kpi_name_eng` varchar(100) NOT NULL,
  `kpi_id` int(10) NOT NULL,
  `ansar_id` int(4) NOT NULL,
  `reason_for_freeze` enum('Withdraw','Reduce') NOT NULL,
  `comment_on_freeze` varchar(1000) NOT NULL,
  `date_of_freeze` date NOT NULL,
  `reporting_date` date NOT NULL,
  `joining_date` date NOT NULL,
  `action_user_id` int(4) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=736 DEFAULT CHARSET=latin1;

/*Table structure for table `tbl_logged_in_user` */

DROP TABLE IF EXISTS `tbl_logged_in_user`;

CREATE TABLE `tbl_logged_in_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
