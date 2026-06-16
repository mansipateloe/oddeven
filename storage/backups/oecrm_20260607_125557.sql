SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `access_accounts`;
CREATE TABLE `access_accounts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `service_type` enum('gmail','github','gitlab','aws','digitalocean','figma','chatgpt','claude','hosting','domain','vpn','other') NOT NULL,
  `service_name` varchar(150) NOT NULL,
  `login_url` varchar(500) DEFAULT NULL,
  `account_username` varchar(180) DEFAULT NULL,
  `owner_email` varchar(180) DEFAULT NULL,
  `status` enum('active','suspended','closed') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_access_company` (`company_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `access_assignments`;
CREATE TABLE `access_assignments` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `account_id` bigint(20) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `access_level` enum('viewer','member','admin','owner') NOT NULL DEFAULT 'member',
  `assigned_on` date NOT NULL,
  `expires_on` date DEFAULT NULL,
  `status` enum('active','revoked') NOT NULL DEFAULT 'active',
  `revoked_on` date DEFAULT NULL,
  `assigned_by` int(11) NOT NULL DEFAULT 0,
  `revoked_by` int(11) DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_access_employee` (`employee_id`,`status`),
  KEY `idx_access_account` (`account_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `access_history`;
CREATE TABLE `access_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `account_id` bigint(20) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `event_type` enum('created','updated','assigned','revoked') NOT NULL,
  `details` text DEFAULT NULL,
  `performed_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_access_history` (`account_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `account`;
CREATE TABLE `account` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_name` varchar(255) NOT NULL,
  `balance` varchar(255) NOT NULL,
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
INSERT INTO `account` (`account_id`,`account_name`,`balance`) VALUES('1','Oddeven Infotech Private Limited - ICICI','333459');
INSERT INTO `account` (`account_id`,`account_name`,`balance`) VALUES('4','Nidhi Navadiya - ICICI','130000');
INSERT INTO `account` (`account_id`,`account_name`,`balance`) VALUES('5','Tejpal Navadiya - Gpay','7500');
INSERT INTO `account` (`account_id`,`account_name`,`balance`) VALUES('7','Oddeven Infotech LLC - Santander Bank (USA)','8740');

DROP TABLE IF EXISTS `admin_menu`;
CREATE TABLE `admin_menu` (
  `mid` int(11) NOT NULL AUTO_INCREMENT,
  `mname` varchar(100) NOT NULL,
  `mtitle` varchar(100) NOT NULL,
  `pmenu` int(11) NOT NULL DEFAULT 0,
  `is_deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`mid`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('1','dashboard','Dashboard','0','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('2','employee','Employee','0','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('4','project','Project','0','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('5','role','Role','0','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('6','lead','Lead','0','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('7','invoice','Invoice','0','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('8','task','Task','0','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('9','domain_hosting','Domain Hosting','0','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('10','salary','Salary','0','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('11','leave','Leave','0','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('12','bank_account','Bank Account','0','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('13','deposit','Deposit','0','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('14','expense','Expense','0','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('15','project_expense','Project Expense','0','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('16','settings','Settings','0','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('17','add_employee','Add Employee','2','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('18','update_employee','Update Employee','2','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('19','view_employee','View Employee','2','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('20','delete_employee','Delete Employee','2','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('21','add_project','Add Project','4','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('22','update_project','Update Project','4','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('23','view_project','View Project','4','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('24','delete_project','Delete Project','4','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('25','add_role','Add Role','5','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('26','update_role','Update Role','5','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('27','view_role','View Role','5','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('28','delete_role','Delete Role','5','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('29','add_lead','Add Lead','6','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('30','update_lead','Update Lead','6','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('31','view_lead','View Lead','6','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('32','delete_lead','Delete Lead','6','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('33','add_invoice','Add Invoice','7','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('34','update_invoice','Update Invoice','7','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('35','view_invoice','View Invoice','7','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('36','delete_invoice','Delete Invoice','7','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('37','add_task','Add Task','8','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('38','update_task','Update Task','8','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('39','view_task','View Task','8','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('40','delete_task','Delete Task','8','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('41','add_domain_hosting','Add Domain Hosting','9','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('42','update_domain_hosting','Update Domain Hosting','9','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('43','view_domain_hosting','View Domain Hosting','9','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('44','delete_domain_hosting','Delete Domain Hosting','9','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('45','add_salary','Add Salary','10','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('46','update_salary','Update Salary','10','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('47','view_salary','View Salary','10','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('48','delete_salary','Delete Salary','10','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('49','add_leave','Add Leave','11','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('50','update_leave','Update Leave','11','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('51','view_leave','View Leave','11','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('52','delete_leave','Delete Leave','11','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('53','add_bank_account','Add Bank Account','12','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('54','update_bank_account','Update Bank Account','12','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('55','view_bank_account','View Bank Account','12','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('56','delete_bank_account','Delete Bank Account','12','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('57','add_deposit','Add Deposit','13','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('58','update_deposit','Update Deposit','13','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('59','view_deposit','View Deposit','13','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('60','delete_deposit','Delete Deposit','13','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('61','add_expense','Add Expense','14','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('62','update_expense','Update Expense','14','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('63','view_expense','View Expense','14','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('64','delete_expense','Delete Expense','14','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('65','add_project_expense','Add Project Expense','15','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('66','update_project_expense','Update Project Expense','15','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('67','view_project_expense','View Project Expense','15','0');
INSERT INTO `admin_menu` (`mid`,`mname`,`mtitle`,`pmenu`,`is_deleted`) VALUES('68','delete_project_expense','Delete Project Expense','15','0');

DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL DEFAULT 0,
  `uname` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
INSERT INTO `admins` (`id`,`company_id`,`uname`,`password`) VALUES('1','1','oddeven','$2y$10$1o0VwJTxuz7B5OfoDPOwPuJTcck0lZTWIYIFppGngHVX9sOgRtHlu');

DROP TABLE IF EXISTS `app_settings`;
CREATE TABLE `app_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL DEFAULT 0,
  `setting_key` varchar(120) NOT NULL,
  `setting_value` longtext DEFAULT NULL,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_setting_company_key` (`company_id`,`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `asset_allocations`;
CREATE TABLE `asset_allocations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `asset_id` bigint(20) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `allocated_on` date NOT NULL,
  `expected_return_date` date DEFAULT NULL,
  `returned_on` date DEFAULT NULL,
  `issue_condition` enum('new','good','fair','damaged') NOT NULL DEFAULT 'good',
  `return_condition` enum('new','good','fair','damaged') DEFAULT NULL,
  `status` enum('allocated','returned') NOT NULL DEFAULT 'allocated',
  `remarks` text DEFAULT NULL,
  `allocated_by` int(11) NOT NULL DEFAULT 0,
  `returned_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_allocation_asset` (`asset_id`,`status`),
  KEY `idx_allocation_employee` (`employee_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `asset_history`;
CREATE TABLE `asset_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `asset_id` bigint(20) NOT NULL,
  `event_type` enum('created','updated','allocated','returned','repair','retired','lost') NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `performed_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_asset_history` (`asset_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `assets`;
CREATE TABLE `assets` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `asset_code` varchar(40) NOT NULL,
  `asset_type` enum('laptop','desktop','monitor','mouse','keyboard','headphones','mobile','other') NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(120) DEFAULT NULL,
  `serial_number` varchar(150) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_cost` decimal(14,2) NOT NULL DEFAULT 0.00,
  `warranty_until` date DEFAULT NULL,
  `condition_status` enum('new','good','fair','damaged') NOT NULL DEFAULT 'good',
  `lifecycle_status` enum('available','allocated','repair','retired','lost') NOT NULL DEFAULT 'available',
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_asset_code` (`company_id`,`asset_code`),
  KEY `idx_asset_status` (`company_id`,`lifecycle_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `attendance_corrections`;
CREATE TABLE `attendance_corrections` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `session_id` bigint(20) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `old_actual_in` datetime DEFAULT NULL,
  `new_actual_in` datetime DEFAULT NULL,
  `old_actual_out` datetime DEFAULT NULL,
  `new_actual_out` datetime DEFAULT NULL,
  `old_break_minutes` int(11) NOT NULL DEFAULT 0,
  `new_break_minutes` int(11) NOT NULL DEFAULT 0,
  `reason` varchar(500) NOT NULL,
  `requested_by` int(11) NOT NULL DEFAULT 0,
  `approved_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_correction_session` (`session_id`),
  KEY `idx_correction_employee` (`employee_id`,`created_at`),
  CONSTRAINT `fk_correction_employee` FOREIGN KEY (`employee_id`) REFERENCES `employeestbl` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_correction_session` FOREIGN KEY (`session_id`) REFERENCES `attendance_sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `attendance_events`;
CREATE TABLE `attendance_events` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `session_id` bigint(20) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `event_type` enum('sign_in','break_in','break_out','sign_out','manual_adjustment') NOT NULL,
  `event_time` datetime NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_attendance_events_session` (`session_id`,`event_time`),
  KEY `fk_attendance_event_employee` (`employee_id`),
  CONSTRAINT `fk_attendance_event_employee` FOREIGN KEY (`employee_id`) REFERENCES `employeestbl` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_attendance_event_session` FOREIGN KEY (`session_id`) REFERENCES `attendance_sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `attendance_period_locks`;
CREATE TABLE `attendance_period_locks` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `period_year` smallint(6) NOT NULL,
  `period_month` tinyint(4) NOT NULL,
  `locked_at` datetime NOT NULL DEFAULT current_timestamp(),
  `locked_by` int(11) NOT NULL DEFAULT 0,
  `lock_reason` varchar(255) DEFAULT NULL,
  `unlocked_at` datetime DEFAULT NULL,
  `unlocked_by` int(11) NOT NULL DEFAULT 0,
  `unlock_reason` varchar(255) DEFAULT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_attendance_period` (`company_id`,`period_year`,`period_month`),
  KEY `idx_period_lock` (`company_id`,`is_locked`),
  CONSTRAINT `fk_attendance_lock_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `attendance_sessions`;
CREATE TABLE `attendance_sessions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `scheduled_start` datetime NOT NULL,
  `scheduled_end` datetime NOT NULL,
  `actual_in` datetime DEFAULT NULL,
  `actual_out` datetime DEFAULT NULL,
  `total_minutes` int(11) NOT NULL DEFAULT 0,
  `break_minutes` int(11) NOT NULL DEFAULT 0,
  `effective_minutes` int(11) NOT NULL DEFAULT 0,
  `late_minutes` int(11) NOT NULL DEFAULT 0,
  `early_exit_minutes` int(11) NOT NULL DEFAULT 0,
  `overtime_minutes` int(11) NOT NULL DEFAULT 0,
  `attendance_status` enum('not_started','present','late','half_day','absent','weekly_off','holiday','incomplete') NOT NULL DEFAULT 'not_started',
  `review_status` enum('pending','approved','rejected','corrected') NOT NULL DEFAULT 'pending',
  `reviewed_by` int(11) NOT NULL DEFAULT 0,
  `reviewed_at` datetime DEFAULT NULL,
  `review_notes` varchar(500) DEFAULT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `late_reason` varchar(500) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_attendance_employee_date` (`employee_id`,`attendance_date`),
  KEY `idx_attendance_shift_date` (`shift_id`,`attendance_date`),
  CONSTRAINT `fk_attendance_employee` FOREIGN KEY (`employee_id`) REFERENCES `employeestbl` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_attendance_shift` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL DEFAULT 0,
  `actor_type` enum('admin','employee','system') NOT NULL DEFAULT 'system',
  `actor_id` int(11) NOT NULL DEFAULT 0,
  `employee_id` int(11) DEFAULT NULL,
  `session_key` varchar(128) DEFAULT NULL,
  `action` varchar(60) NOT NULL,
  `module_key` varchar(80) NOT NULL,
  `entity_type` varchar(100) DEFAULT NULL,
  `entity_id` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `old_values` longtext DEFAULT NULL,
  `new_values` longtext DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `browser_name` varchar(80) DEFAULT NULL,
  `device_type` varchar(40) DEFAULT NULL,
  `platform_name` varchar(80) DEFAULT NULL,
  `risk_level` enum('normal','review','suspicious') NOT NULL DEFAULT 'normal',
  `risk_reasons` varchar(500) DEFAULT NULL,
  `request_method` varchar(10) DEFAULT NULL,
  `request_uri` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_audit_company_created` (`company_id`,`created_at`),
  KEY `idx_audit_actor` (`actor_type`,`actor_id`),
  KEY `idx_audit_entity` (`entity_type`,`entity_id`),
  KEY `idx_audit_module_action` (`module_key`,`action`),
  KEY `idx_audit_employee` (`employee_id`,`created_at`),
  KEY `idx_audit_risk` (`company_id`,`risk_level`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `audit_logs` (`id`,`company_id`,`actor_type`,`actor_id`,`employee_id`,`session_key`,`action`,`module_key`,`entity_type`,`entity_id`,`description`,`old_values`,`new_values`,`ip_address`,`user_agent`,`browser_name`,`device_type`,`platform_name`,`risk_level`,`risk_reasons`,`request_method`,`request_uri`,`created_at`) VALUES('4','0','system','0',NULL,'cqp00qk4vajfq6fknt3o197tc9','failed_login','authentication','system','0','Employee portal login failed',NULL,'{\"username\":\"oddeven\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0','Firefox','desktop','Windows','normal','','POST','/oecrm/employee/','2026-06-06 22:46:54');
INSERT INTO `audit_logs` (`id`,`company_id`,`actor_type`,`actor_id`,`employee_id`,`session_key`,`action`,`module_key`,`entity_type`,`entity_id`,`description`,`old_values`,`new_values`,`ip_address`,`user_agent`,`browser_name`,`device_type`,`platform_name`,`risk_level`,`risk_reasons`,`request_method`,`request_uri`,`created_at`) VALUES('5','0','system','0',NULL,'cqp00qk4vajfq6fknt3o197tc9','failed_login','authentication','system','0','Employee portal login failed',NULL,'{\"username\":\"oddeven\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0','Firefox','desktop','Windows','normal','','POST','/oecrm/employee/','2026-06-06 22:47:04');
INSERT INTO `audit_logs` (`id`,`company_id`,`actor_type`,`actor_id`,`employee_id`,`session_key`,`action`,`module_key`,`entity_type`,`entity_id`,`description`,`old_values`,`new_values`,`ip_address`,`user_agent`,`browser_name`,`device_type`,`platform_name`,`risk_level`,`risk_reasons`,`request_method`,`request_uri`,`created_at`) VALUES('6','0','system','0',NULL,'cqp00qk4vajfq6fknt3o197tc9','failed_login','authentication','system','0','Employee portal login failed',NULL,'{\"username\":\"oddeven\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0','Firefox','desktop','Windows','normal','','POST','/oecrm/employee/','2026-06-06 22:48:23');
INSERT INTO `audit_logs` (`id`,`company_id`,`actor_type`,`actor_id`,`employee_id`,`session_key`,`action`,`module_key`,`entity_type`,`entity_id`,`description`,`old_values`,`new_values`,`ip_address`,`user_agent`,`browser_name`,`device_type`,`platform_name`,`risk_level`,`risk_reasons`,`request_method`,`request_uri`,`created_at`) VALUES('7','1','admin','1',NULL,'7eji332qbfppb5ne4pmb4mrsqc','login','authentication','admin','1','Admin login successful',NULL,'{\"username\":\"oddeven\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0','Firefox','desktop','Windows','normal','','POST','/oecrm/admin/index.php','2026-06-06 22:48:50');
INSERT INTO `audit_logs` (`id`,`company_id`,`actor_type`,`actor_id`,`employee_id`,`session_key`,`action`,`module_key`,`entity_type`,`entity_id`,`description`,`old_values`,`new_values`,`ip_address`,`user_agent`,`browser_name`,`device_type`,`platform_name`,`risk_level`,`risk_reasons`,`request_method`,`request_uri`,`created_at`) VALUES('8','1','admin','1',NULL,'lbn8bod1c8tl0c4hu933s9jr9u','login','authentication','admin','1','Admin login successful',NULL,'{\"username\":\"oddeven\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','Chrome','desktop','Windows','review','New IP address','POST','/oecrm/admin/index.php','2026-06-07 13:41:28');
INSERT INTO `audit_logs` (`id`,`company_id`,`actor_type`,`actor_id`,`employee_id`,`session_key`,`action`,`module_key`,`entity_type`,`entity_id`,`description`,`old_values`,`new_values`,`ip_address`,`user_agent`,`browser_name`,`device_type`,`platform_name`,`risk_level`,`risk_reasons`,`request_method`,`request_uri`,`created_at`) VALUES('9','1','admin','1',NULL,'uuakurlbrqsggk0q2h6q81e0pf','login','authentication','admin','1','Admin login successful',NULL,'{\"username\":\"oddeven\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','Chrome','desktop','Windows','normal','','POST','/oecrm/admin/index.php','2026-06-07 13:46:24');
INSERT INTO `audit_logs` (`id`,`company_id`,`actor_type`,`actor_id`,`employee_id`,`session_key`,`action`,`module_key`,`entity_type`,`entity_id`,`description`,`old_values`,`new_values`,`ip_address`,`user_agent`,`browser_name`,`device_type`,`platform_name`,`risk_level`,`risk_reasons`,`request_method`,`request_uri`,`created_at`) VALUES('10','1','admin','1',NULL,'5q963kfe8vtrsoj6tm3e55nt1a','login','authentication','admin','1','Admin login successful',NULL,'{\"username\":\"oddeven\"}','::1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-IN) WindowsPowerShell/5.1.26100.8457','Other','desktop','Windows','normal','','POST','/oecrm/admin/index.php','2026-06-07 16:25:56');

DROP TABLE IF EXISTS `audit_retention_settings`;
CREATE TABLE `audit_retention_settings` (
  `company_id` int(11) NOT NULL,
  `retention_days` int(11) NOT NULL DEFAULT 730,
  `preserve_security_events` tinyint(1) NOT NULL DEFAULT 1,
  `last_purged_at` datetime DEFAULT NULL,
  `updated_by` int(11) NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`company_id`),
  CONSTRAINT `fk_audit_retention_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `audit_retention_settings` (`company_id`,`retention_days`,`preserve_security_events`,`last_purged_at`,`updated_by`,`updated_at`) VALUES('1','730','1',NULL,'0','2026-06-06 22:38:27');

DROP TABLE IF EXISTS `bank_details`;
CREATE TABLE `bank_details` (
  `bank_id` int(11) NOT NULL AUTO_INCREMENT,
  `bank_name` varchar(255) NOT NULL,
  PRIMARY KEY (`bank_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `bank_details` (`bank_id`,`bank_name`) VALUES('7','ICICI Gandhinagar');
INSERT INTO `bank_details` (`bank_id`,`bank_name`) VALUES('8','ICICI Nidhi');
INSERT INTO `bank_details` (`bank_id`,`bank_name`) VALUES('9','Gpay - Tejpal');

DROP TABLE IF EXISTS `bank_reconciliation`;
CREATE TABLE `bank_reconciliation` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `statement_date` date NOT NULL,
  `statement_balance` decimal(14,2) NOT NULL,
  `book_balance` decimal(14,2) NOT NULL,
  `difference_amount` decimal(14,2) NOT NULL,
  `status` enum('draft','reconciled') NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `reconciled_by` int(11) DEFAULT NULL,
  `reconciled_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_reconciliation` (`company_id`,`statement_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `client_communications`;
CREATE TABLE `client_communications` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) NOT NULL,
  `contact_id` bigint(20) DEFAULT NULL,
  `communication_type` enum('call','email','meeting','note','message') NOT NULL DEFAULT 'note',
  `subject` varchar(180) NOT NULL,
  `details` text DEFAULT NULL,
  `communication_at` datetime NOT NULL,
  `next_followup_at` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_client_communication` (`client_id`,`communication_at`),
  KEY `fk_client_communication_contact` (`contact_id`),
  CONSTRAINT `fk_client_communication` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_client_communication_contact` FOREIGN KEY (`contact_id`) REFERENCES `client_contacts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `client_contacts`;
CREATE TABLE `client_contacts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `designation` varchar(120) DEFAULT NULL,
  `email` varchar(180) DEFAULT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_client_contact` (`client_id`,`status`),
  CONSTRAINT `fk_client_contact` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `client_contracts`;
CREATE TABLE `client_contracts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) NOT NULL,
  `contract_type` enum('service','retainer','dedicated_resource','nda','other') NOT NULL DEFAULT 'service',
  `title` varchar(180) NOT NULL,
  `reference_no` varchar(80) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `value_amount` decimal(16,2) NOT NULL DEFAULT 0.00,
  `currency_code` char(3) NOT NULL DEFAULT 'INR',
  `billing_cycle` enum('one_time','weekly','monthly','quarterly','yearly','hourly') NOT NULL DEFAULT 'one_time',
  `status` enum('draft','active','expired','terminated') NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_client_contract` (`client_id`,`status`),
  CONSTRAINT `fk_client_contract` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `client_documents`;
CREATE TABLE `client_documents` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) NOT NULL,
  `document_type` enum('contract','nda','tax','proposal','other') NOT NULL DEFAULT 'other',
  `title` varchar(180) NOT NULL,
  `stored_name` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `mime_type` varchar(120) DEFAULT NULL,
  `file_size` int(11) NOT NULL DEFAULT 0,
  `expiry_date` date DEFAULT NULL,
  `uploaded_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_client_document` (`client_id`,`document_type`),
  CONSTRAINT `fk_client_document` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `client_code` varchar(30) NOT NULL,
  `legal_name` varchar(180) NOT NULL,
  `display_name` varchar(180) NOT NULL,
  `client_type` enum('company','individual') NOT NULL DEFAULT 'company',
  `status` enum('prospect','active','inactive','on_hold','closed') NOT NULL DEFAULT 'active',
  `industry` varchar(120) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `email` varchar(180) DEFAULT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `billing_address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `postal_code` varchar(30) DEFAULT NULL,
  `tax_id` varchar(80) DEFAULT NULL,
  `currency_code` char(3) NOT NULL DEFAULT 'INR',
  `payment_terms_days` int(11) NOT NULL DEFAULT 0,
  `source_lead_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_client_code` (`company_id`,`client_code`),
  KEY `idx_client_company_status` (`company_id`,`status`),
  CONSTRAINT `fk_client_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `comp_off_ledger`;
CREATE TABLE `comp_off_ledger` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `source_type` enum('holiday_work','weekly_off_work','manual','adjustment','leave_usage') NOT NULL,
  `source_date` date DEFAULT NULL,
  `credit_days` decimal(6,2) NOT NULL DEFAULT 0.00,
  `debit_days` decimal(6,2) NOT NULL DEFAULT 0.00,
  `expiry_date` date DEFAULT NULL,
  `reference_type` varchar(60) DEFAULT NULL,
  `reference_id` bigint(20) DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `status` enum('available','used','expired','cancelled') NOT NULL DEFAULT 'available',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_comp_off_employee` (`employee_id`,`status`,`expiry_date`),
  CONSTRAINT `fk_comp_off_employee` FOREIGN KEY (`employee_id`) REFERENCES `employeestbl` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `companies`;
CREATE TABLE `companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL,
  `legal_name` varchar(180) NOT NULL,
  `display_name` varchar(180) NOT NULL,
  `company_type` enum('india_gst','india_non_gst','usa','other') NOT NULL DEFAULT 'other',
  `country_code` char(2) NOT NULL DEFAULT 'IN',
  `currency_code` char(3) NOT NULL DEFAULT 'INR',
  `gstin` varchar(30) DEFAULT NULL,
  `tax_id` varchar(50) DEFAULT NULL,
  `email` varchar(180) DEFAULT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_companies_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `companies` (`id`,`code`,`legal_name`,`display_name`,`company_type`,`country_code`,`currency_code`,`gstin`,`tax_id`,`email`,`phone`,`address`,`logo_path`,`status`,`created_at`,`updated_at`) VALUES('1','OE-IN-GST','Oddeven Infotech Private Limited','Oddeven Infotech','india_gst','IN','INR',NULL,NULL,NULL,NULL,NULL,NULL,'1','2026-06-06 20:53:44','2026-06-06 20:53:44');

DROP TABLE IF EXISTS `country`;
CREATE TABLE `country` (
  `country_id` int(11) NOT NULL AUTO_INCREMENT,
  `country_code` varchar(255) NOT NULL,
  `country_symbols` varchar(255) NOT NULL,
  PRIMARY KEY (`country_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `country` (`country_id`,`country_code`,`country_symbols`) VALUES('1','INR','₹');
INSERT INTO `country` (`country_id`,`country_code`,`country_symbols`) VALUES('2','USD','$');

DROP TABLE IF EXISTS `currency_master`;
CREATE TABLE `currency_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `symbol` varchar(20) NOT NULL,
  `rate` decimal(18,6) NOT NULL DEFAULT 1.000000,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_currency_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `currency_master` (`id`,`name`,`symbol`,`rate`,`created_at`,`updated_at`) VALUES('1','INR','₹','1.000000','2026-06-07 13:50:25','2026-06-07 13:50:25');
INSERT INTO `currency_master` (`id`,`name`,`symbol`,`rate`,`created_at`,`updated_at`) VALUES('2','USD','$','1.000000','2026-06-07 13:50:25','2026-06-07 13:50:25');

DROP TABLE IF EXISTS `database_backups`;
CREATE TABLE `database_backups` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` bigint(20) NOT NULL DEFAULT 0,
  `status` enum('completed','failed') NOT NULL DEFAULT 'completed',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `code` varchar(30) DEFAULT NULL,
  `manager_employee_id` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_department_company_name` (`company_id`,`name`),
  KEY `idx_departments_company` (`company_id`),
  CONSTRAINT `fk_departments_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `departments` (`id`,`company_id`,`name`,`code`,`manager_employee_id`,`status`,`created_at`,`updated_at`) VALUES('1','1','General','GEN',NULL,'1','2026-06-06 21:24:18','2026-06-06 21:24:18');

DROP TABLE IF EXISTS `deposit`;
CREATE TABLE `deposit` (
  `deposit_id` int(11) NOT NULL AUTO_INCREMENT,
  `deposit_date` date NOT NULL,
  `account_Id` int(11) NOT NULL,
  `project_Id` int(11) NOT NULL,
  `invoice_id` bigint(20) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `amount` varchar(255) NOT NULL,
  `tax_amount` varchar(255) NOT NULL DEFAULT '0',
  `approx_amount` varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`deposit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

DROP TABLE IF EXISTS `designation`;
CREATE TABLE `designation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `designation` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
INSERT INTO `designation` (`id`,`designation`) VALUES('11','Php Developer');
INSERT INTO `designation` (`id`,`designation`) VALUES('12','Net Developer');
INSERT INTO `designation` (`id`,`designation`) VALUES('13','Android Developer');
INSERT INTO `designation` (`id`,`designation`) VALUES('14','Business Development Executive');
INSERT INTO `designation` (`id`,`designation`) VALUES('15','Web Designer');
INSERT INTO `designation` (`id`,`designation`) VALUES('17','Seo Developer');
INSERT INTO `designation` (`id`,`designation`) VALUES('18','Business Analyst');
INSERT INTO `designation` (`id`,`designation`) VALUES('19','QA');
INSERT INTO `designation` (`id`,`designation`) VALUES('20','Salesforce Developer');

DROP TABLE IF EXISTS `digital_subscriptions`;
CREATE TABLE `digital_subscriptions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `asset_type` enum('domain','hosting','ssl','software','ai_tool','cloud','other') NOT NULL,
  `name` varchar(180) NOT NULL,
  `provider` varchar(150) DEFAULT NULL,
  `login_url` varchar(500) DEFAULT NULL,
  `username` varchar(180) DEFAULT NULL,
  `secret_encrypted` text DEFAULT NULL,
  `recovery_email` varchar(180) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `renewal_date` date DEFAULT NULL,
  `billing_cycle` enum('monthly','quarterly','half_yearly','yearly','other') NOT NULL DEFAULT 'yearly',
  `cost` decimal(14,2) NOT NULL DEFAULT 0.00,
  `currency_code` char(3) NOT NULL DEFAULT 'INR',
  `status` enum('active','expired','cancelled') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_subscription_renewal` (`company_id`,`renewal_date`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `domainhostingtbl`;
CREATE TABLE `domainhostingtbl` (
  `domain_id` int(11) NOT NULL AUTO_INCREMENT,
  `clientname` varchar(255) NOT NULL,
  `service` varchar(255) NOT NULL,
  `domainname` varchar(255) NOT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  `amount` varchar(255) NOT NULL,
  PRIMARY KEY (`domain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

DROP TABLE IF EXISTS `employee_contracts`;
CREATE TABLE `employee_contracts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `contract_type` enum('employment','nda','consulting','internship','other') NOT NULL DEFAULT 'employment',
  `title` varchar(180) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('draft','active','expired','terminated') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_employee_contracts_employee` (`employee_id`),
  CONSTRAINT `fk_employee_contracts_employee` FOREIGN KEY (`employee_id`) REFERENCES `employeestbl` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `employee_documents`;
CREATE TABLE `employee_documents` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `document_type` enum('resume','aadhaar','pan','passport','driving_licence','address_proof','photograph','experience_letter','relieving_letter','salary_slip','contract','other') NOT NULL,
  `title` varchar(180) NOT NULL,
  `stored_name` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `mime_type` varchar(120) DEFAULT NULL,
  `file_size` bigint(20) NOT NULL DEFAULT 0,
  `document_number` varchar(100) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `uploaded_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_employee_documents_employee_type` (`employee_id`,`document_type`),
  CONSTRAINT `fk_employee_documents_employee` FOREIGN KEY (`employee_id`) REFERENCES `employeestbl` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `employee_exits`;
CREATE TABLE `employee_exits` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `resignation_date` date DEFAULT NULL,
  `last_working_date` date NOT NULL,
  `exit_type` enum('resignation','termination','retirement','other') NOT NULL DEFAULT 'resignation',
  `status` enum('initiated','clearance','settlement','completed','cancelled') NOT NULL DEFAULT 'initiated',
  `pending_salary` decimal(14,2) NOT NULL DEFAULT 0.00,
  `retention_release` decimal(14,2) NOT NULL DEFAULT 0.00,
  `deductions` decimal(14,2) NOT NULL DEFAULT 0.00,
  `final_settlement` decimal(14,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_exit` (`company_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `employee_increments`;
CREATE TABLE `employee_increments` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `effective_date` date NOT NULL,
  `previous_salary` decimal(14,2) NOT NULL DEFAULT 0.00,
  `revised_salary` decimal(14,2) NOT NULL DEFAULT 0.00,
  `increment_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `increment_percent` decimal(8,2) NOT NULL DEFAULT 0.00,
  `reason` varchar(255) DEFAULT NULL,
  `approved_by` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_employee_increments_employee_date` (`employee_id`,`effective_date`),
  CONSTRAINT `fk_employee_increments_employee` FOREIGN KEY (`employee_id`) REFERENCES `employeestbl` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `employee_leave_balances`;
CREATE TABLE `employee_leave_balances` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `balance_year` smallint(6) NOT NULL,
  `opening_balance` decimal(7,2) NOT NULL DEFAULT 0.00,
  `credited` decimal(7,2) NOT NULL DEFAULT 0.00,
  `used` decimal(7,2) NOT NULL DEFAULT 0.00,
  `pending` decimal(7,2) NOT NULL DEFAULT 0.00,
  `adjusted` decimal(7,2) NOT NULL DEFAULT 0.00,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_employee_leave_balance` (`employee_id`,`leave_type_id`,`balance_year`),
  KEY `fk_leave_balance_type` (`leave_type_id`),
  CONSTRAINT `fk_leave_balance_employee` FOREIGN KEY (`employee_id`) REFERENCES `employeestbl` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_leave_balance_type` FOREIGN KEY (`leave_type_id`) REFERENCES `leavetypetbl` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=257 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('1','1','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('2','1','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('3','1','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('4','1','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('5','1','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('6','2','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('7','2','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('8','2','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('9','2','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('10','2','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('11','3','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('12','3','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('13','3','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('14','3','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('15','3','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('16','4','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('17','4','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('18','4','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('19','4','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('20','4','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('21','6','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('22','6','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('23','6','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('24','6','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('25','6','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('26','7','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('27','7','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('28','7','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('29','7','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('30','7','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('31','8','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('32','8','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('33','8','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('34','8','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('35','8','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('36','9','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('37','9','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('38','9','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('39','9','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('40','9','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('41','10','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('42','10','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('43','10','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('44','10','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('45','10','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('46','11','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('47','11','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('48','11','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('49','11','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('50','11','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('51','12','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('52','12','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('53','12','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('54','12','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('55','12','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('56','13','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('57','13','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('58','13','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('59','13','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('60','13','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('61','14','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('62','14','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('63','14','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('64','14','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('65','14','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('66','15','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('67','15','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('68','15','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('69','15','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('70','15','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('71','17','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('72','17','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('73','17','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('74','17','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('75','17','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('76','18','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('77','18','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('78','18','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('79','18','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('80','18','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('81','19','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('82','19','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('83','19','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('84','19','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('85','19','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('86','20','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('87','20','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('88','20','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('89','20','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('90','20','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('91','21','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('92','21','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('93','21','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('94','21','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('95','21','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('96','22','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('97','22','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('98','22','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('99','22','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('100','22','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('101','23','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('102','23','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('103','23','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('104','23','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('105','23','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('106','24','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('107','24','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('108','24','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('109','24','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('110','24','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('111','25','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('112','25','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('113','25','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('114','25','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('115','25','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('116','26','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('117','26','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('118','26','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('119','26','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('120','26','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('121','27','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('122','27','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('123','27','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('124','27','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('125','27','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('126','28','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('127','28','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('128','28','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('129','28','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('130','28','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('131','30','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('132','30','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('133','30','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('134','30','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('135','30','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('136','31','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('137','31','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('138','31','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('139','31','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('140','31','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('141','32','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('142','32','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('143','32','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('144','32','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('145','32','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('146','33','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('147','33','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('148','33','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('149','33','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('150','33','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('151','34','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('152','34','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('153','34','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('154','34','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('155','34','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('156','35','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('157','35','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('158','35','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('159','35','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('160','35','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('161','36','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('162','36','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('163','36','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('164','36','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('165','36','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('166','37','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('167','37','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('168','37','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('169','37','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('170','37','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('171','38','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('172','38','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('173','38','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('174','38','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('175','38','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('176','39','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('177','39','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('178','39','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('179','39','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('180','39','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('181','40','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('182','40','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('183','40','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('184','40','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('185','40','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('186','41','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('187','41','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('188','41','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('189','41','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('190','41','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('191','42','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('192','42','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('193','42','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('194','42','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('195','42','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('196','44','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('197','44','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('198','44','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('199','44','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('200','44','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('201','45','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('202','45','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('203','45','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('204','45','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('205','45','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('206','46','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('207','46','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('208','46','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('209','46','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('210','46','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('211','47','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('212','47','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('213','47','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('214','47','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('215','47','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('216','48','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('217','48','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('218','48','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('219','48','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('220','48','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('221','49','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('222','49','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('223','49','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('224','49','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('225','49','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('226','50','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('227','50','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('228','50','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('229','50','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('230','50','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('231','51','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('232','51','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('233','51','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('234','51','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('235','51','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('236','52','1','2026','0.00','10.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('237','52','2','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('238','52','3','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('239','52','4','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');
INSERT INTO `employee_leave_balances` (`id`,`employee_id`,`leave_type_id`,`balance_year`,`opening_balance`,`credited`,`used`,`pending`,`adjusted`,`updated_at`) VALUES('240','52','5','2026','0.00','0.00','0.00','0.00','0.00','2026-06-06 21:47:53');

DROP TABLE IF EXISTS `employee_profiles`;
CREATE TABLE `employee_profiles` (
  `employee_id` int(11) NOT NULL,
  `employment_type` enum('permanent','probation','contract','intern','consultant') NOT NULL DEFAULT 'permanent',
  `employment_status` enum('active','inactive','notice_period','resigned','terminated','retired') NOT NULL DEFAULT 'active',
  `confirmation_date` date DEFAULT NULL,
  `notice_period_days` int(11) NOT NULL DEFAULT 0,
  `exit_date` date DEFAULT NULL,
  `exit_reason` varchar(255) DEFAULT NULL,
  `emergency_contact_name` varchar(150) DEFAULT NULL,
  `emergency_contact_phone` varchar(40) DEFAULT NULL,
  `blood_group` varchar(10) DEFAULT NULL,
  `marital_status` varchar(30) DEFAULT NULL,
  `current_address` text DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`employee_id`),
  CONSTRAINT `fk_employee_profiles_employee` FOREIGN KEY (`employee_id`) REFERENCES `employeestbl` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('1','permanent','active',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'Ugati 122','Ugati 122','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('2','permanent','active',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'Bapunagar','Bapunagar','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('3','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'[Present Address]\r\nB/93 Tejendra Prakash Socity Part-2,\r\nBehind Tejendra Vihar, Khudiyar Nagar, Bapunagar Ahmedabad -\r\nAhmedabd, Gujrat - 380024\r\n[Permanent Address]\r\nAt & Po - Jamahar\r\nTa - Gwalior\r\nDist - Gwalior - 474005','[Present Address]\r\nB/93 Tejendra Prakash Socity Part-2,\r\nBehind Tejendra Vihar, Khudiyar Nagar, Bapunagar Ahmedabad -\r\nAhmedabd, Gujrat - 380024\r\n[Permanent Address]\r\nAt & Po - Jamahar\r\nTa - Gwalior\r\nDist - Gwalior - 474005','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('4','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'At & Po - Raigadh\r\nTa - Himatnagar\r\nDist - Sabarkantha - 383276','At & Po - Raigadh\r\nTa - Himatnagar\r\nDist - Sabarkantha - 383276','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('6','permanent','active',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'Ahmedabad','Ahmedabad','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('7','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'7, Ujjval Homes, B/H CNG pump,\r\nNana Chiloda, Ahemdabad - 382330','7, Ujjval Homes, B/H CNG pump,\r\nNana Chiloda, Ahemdabad - 382330','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('8','permanent','active',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'D-102,Shubh Candid Society, Kudasan. Taluka-District:Gandhinagar ','D-102,Shubh Candid Society, Kudasan. Taluka-District:Gandhinagar ','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('9','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'28,Ghansyamnagar, Nr Ashopalav Sco,\r\nL.B Shashtri Road, Saijpur,\r\nA\'bd-382345','28,Ghansyamnagar, Nr Ashopalav Sco,\r\nL.B Shashtri Road, Saijpur,\r\nA\'bd-382345','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('10','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('11','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('12','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'404 - Pavan Appartment , Near Mahaprabhuji bethak , Naroda , Ahmedabad 382330','404 - Pavan Appartment , Near Mahaprabhuji bethak , Naroda , Ahmedabad 382330','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('13','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'105, Jay Ambe Soc., Thakkarbapanagar Road, Bapunagar, Ahmedabad 382350','105, Jay Ambe Soc., Thakkarbapanagar Road, Bapunagar, Ahmedabad 382350','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('14','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('15','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'B-26 Umiya Krupa Soc. \r\nUrjnanagar 1 Kudasan Gandhinagar','B-26 Umiya Krupa Soc. \r\nUrjnanagar 1 Kudasan Gandhinagar','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('17','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'A-601 sahyog greens, opp preksha bharti, koba, gandhinagar\r\nC503 Bansari Greencity I Gandhinagar\r\n','A-601 sahyog greens, opp preksha bharti, koba, gandhinagar\r\nC503 Bansari Greencity I Gandhinagar\r\n','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('18','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('19','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'Saundarya 444, Kh- Road, Sargasan Cross, Gandhinagar','Saundarya 444, Kh- Road, Sargasan Cross, Gandhinagar','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('20','permanent','active',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('21','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('22','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('23','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'25/357\r\nNear union bank saman branch','25/357\r\nNear union bank saman branch','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('24','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('25','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'Gandhinagar','Gandhinagar','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('26','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('27','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('28','permanent','active',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('30','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('31','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('32','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'Maharashtara','Maharashtara','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('33','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'Rajasthan ','Rajasthan ','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('34','permanent','active',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'93/741 Near Jaimangal BRTS, Nirmal Apartment. Ahmedabad','93/741 Near Jaimangal BRTS, Nirmal Apartment. Ahmedabad','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('35','permanent','active',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'A/46 Prerna Bunglows, Bapasita Ram Chowk, New Naroda, Ahmedabad','A/46 Prerna Bunglows, Bapasita Ram Chowk, New Naroda, Ahmedabad','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('36','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('37','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'D - 504 Shreeji Icon Kudasan Gandhiangar','D - 504 Shreeji Icon Kudasan Gandhiangar','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('38','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('39','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('40','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('41','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('42','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'kadi','kadi','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('44','permanent','active',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('45','permanent','active',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('46','permanent','active',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('47','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('48','permanent','active',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('49','permanent','active',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('50','permanent','inactive',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('51','permanent','active',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'F-201 Elegance, Hansol, Sardarnagar, Ahmedabad. Gujarat.','F-201 Elegance, Hansol, Sardarnagar, Ahmedabad. Gujarat.','2026-06-06 21:04:50','2026-06-06 21:04:50');
INSERT INTO `employee_profiles` (`employee_id`,`employment_type`,`employment_status`,`confirmation_date`,`notice_period_days`,`exit_date`,`exit_reason`,`emergency_contact_name`,`emergency_contact_phone`,`blood_group`,`marital_status`,`current_address`,`permanent_address`,`created_at`,`updated_at`) VALUES('52','permanent','active',NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,'','','2026-06-06 21:04:50','2026-06-06 21:04:50');

DROP TABLE IF EXISTS `employee_shift_assignments`;
CREATE TABLE `employee_shift_assignments` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `notes` varchar(255) DEFAULT NULL,
  `assigned_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_assignment_employee_dates` (`employee_id`,`effective_from`,`effective_to`),
  KEY `idx_assignment_shift` (`shift_id`),
  CONSTRAINT `fk_shift_assignment_employee` FOREIGN KEY (`employee_id`) REFERENCES `employeestbl` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_shift_assignment_shift` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('1','1','1','2000-01-01',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('2','2','1','2019-09-01',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('3','3','1','2000-01-01',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('4','4','1','2018-05-31',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('5','6','1','2018-02-08',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('6','7','1','2018-07-09',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('7','8','1','2019-05-01',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('8','9','1','2018-12-03',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('9','10','1','2019-01-04',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('10','11','1','2019-03-18',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('11','12','1','2019-03-22',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('12','13','1','2019-04-01',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('13','14','1','2019-05-21',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('14','15','1','2019-09-02',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('15','17','1','2022-06-01',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('16','18','1','2000-01-01',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('17','19','1','2021-08-02',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('18','20','1','2021-11-16',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('19','21','1','2022-02-23',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('20','22','1','2022-09-21',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('21','23','1','2022-09-26',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('22','24','1','2022-09-26',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('23','25','1','2022-10-10',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('24','26','1','2018-03-01',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('25','27','1','2022-12-01',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('26','28','1','2023-01-05',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('27','30','1','2023-01-09',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('28','31','1','2000-01-01',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('29','32','1','2023-07-04',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('30','33','1','2000-01-01',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('31','34','1','2023-08-28',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('32','35','1','2023-10-16',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('33','36','1','2023-11-20',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('34','37','1','2023-12-26',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('35','38','1','2024-06-10',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('36','39','1','2024-06-10',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('37','40','1','2024-06-10',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('38','41','1','2024-06-20',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('39','42','1','2024-10-01',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('40','44','1','2025-03-17',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('41','45','1','2024-10-14',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('42','46','1','2024-10-14',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('43','47','1','2000-01-01',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('44','48','1','2025-05-01',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('45','49','1','2025-06-09',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('46','50','1','2000-01-01',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('47','51','1','2026-03-09',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');
INSERT INTO `employee_shift_assignments` (`id`,`employee_id`,`shift_id`,`effective_from`,`effective_to`,`status`,`notes`,`assigned_by`,`created_at`) VALUES('48','52','1','2000-01-01',NULL,'1','Initial migration assignment','0','2026-06-06 21:27:06');

DROP TABLE IF EXISTS `employee_shift_history`;
CREATE TABLE `employee_shift_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `old_shift_id` int(11) DEFAULT NULL,
  `new_shift_id` int(11) NOT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `changed_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_shift_history_employee` (`employee_id`,`effective_from`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `employee_status_history`;
CREATE TABLE `employee_status_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `old_status` varchar(40) DEFAULT NULL,
  `new_status` varchar(40) NOT NULL,
  `effective_date` date NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `changed_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_employee_status_history_employee` (`employee_id`,`effective_date`),
  CONSTRAINT `fk_employee_status_history_employee` FOREIGN KEY (`employee_id`) REFERENCES `employeestbl` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `employeestbl`;
CREATE TABLE `employeestbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL DEFAULT 0,
  `department_id` int(11) NOT NULL DEFAULT 0,
  `is_admin_access` int(11) NOT NULL DEFAULT 0,
  `access_role` int(11) NOT NULL DEFAULT 0,
  `employeeCode` varchar(255) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `birthdate` varchar(25) NOT NULL,
  `employeeUname` varchar(255) NOT NULL,
  `employeeUpass` varchar(255) NOT NULL,
  `companyEmail` varchar(255) NOT NULL,
  `personalEmail` varchar(255) NOT NULL,
  `mobile1` varchar(255) NOT NULL,
  `mobile2` varchar(255) NOT NULL,
  `skypeUname` varchar(255) NOT NULL,
  `joiningDate` varchar(255) NOT NULL,
  `salary` varchar(255) NOT NULL,
  `bankName` varchar(255) NOT NULL,
  `bankIFSCno` varchar(255) NOT NULL,
  `bankAcHolderName` varchar(255) NOT NULL,
  `bankAcNo` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('1','1','1','0','0','E003','Php Developer','Tejpal Navadiya','','tejpal','0192023a7bbd73250516f069df18b500','meet2teju@oddeveninfotech.com','meet2teju@gmail.com','8460090389','9016840417','tejpal143','','35000','','','','','Ugati 122','0');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('2','1','1','0','0','E002','Net Developer','Bhavesh Vaghani','','bhavesh','0192023a7bbd73250516f069df18b500','bhavesh@oddeveninfotech.com','bhavesh.vaghani1989@gmail.com','9427558676','9099965811','','2019-09-01','35000','','','','','Bapunagar','0');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('3','1','1','0','0','E004','Web Designer','Vinod Kushwah','','vinod','e149c203f50a48b48113bfbbe6bb2fa6','vinodkushwah590@gmail.com','vinodkushwan22@gmail.com','8511933704','9106297436','vinod.kushwah12','','31000','Kotak Mahindra Bank','KKBK0000812','Vinod Kumar','0611379625','[Present Address]\r\nB/93 Tejendra Prakash Socity Part-2,\r\nBehind Tejendra Vihar, Khudiyar Nagar, Bapunagar Ahmedabad -\r\nAhmedabd, Gujrat - 380024\r\n[Permanent Address]\r\nAt & Po - Jamahar\r\nTa - Gwalior\r\nDist - Gwalior - 474005','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('4','1','1','0','0','E005','Php Developer','Nirav Bhoi','','nirav','0192023a7bbd73250516f069df18b500','niravoddevenphp@gmail.com','niravbhoi007@gmail.com','9408373007','9409351494','niravoddevenphp@gmail.com','2018-05-31','24000','ICICI Bank','ICIC0001183','Niravkumar Jagdishlal Bhoi','118301522396','At & Po - Raigadh\r\nTa - Himatnagar\r\nDist - Sabarkantha - 383276','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('6','1','1','0','0','E001','Net Developer','Rahul Patel','','rahuljodhani','e857f9b3fa03593ff7787a6ba9ecd5c1','rj@oddeveninfotech.com','rahul.jodhani@gmail.com','9545808844','','rahul.jodhani@gmail.com','2018-02-08','0','','','','','Ahmedabad','0');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('7','1','1','0','0','E006','Web Designer','Payal Jha','','payalj','0192023a7bbd73250516f069df18b500','oddevendesigner@gmail.com','payaljha2012@gmail.com','9725221469','9898454192','','2018-07-09','14000','','','','','7, Ujjval Homes, B/H CNG pump,\r\nNana Chiloda, Ahemdabad - 382330','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('8','1','1','0','0','E007','Php Developer','Anil Jhala','','aniljhala','0192023a7bbd73250516f069df18b500','aniljhala123@gmail.com','','9726949866','','','2019-05-01','10000','','','','','D-102,Shubh Candid Society, Kudasan. Taluka-District:Gandhinagar ','0');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('9','1','1','0','0','E008','Business Development Executive','Mansi R Patel','','mansipatel','e857f9b3fa03593ff7787a6ba9ecd5c1','xxxx@oddeveninfotech.com','mansi2691patel@gmail.com','9426078950','9558787633','sales@oddeveninfotech.com','2018-12-03','18000','PNB','PUNB0213800','Mansi Rameshbhai Patel','2138000100079023','28,Ghansyamnagar, Nr Ashopalav Sco,\r\nL.B Shashtri Road, Saijpur,\r\nA\'bd-382345','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('10','1','1','0','0','E009','Android Developer','Dipak Prajapati','','dipake009','e857f9b3fa03593ff7787a6ba9ecd5c1','dp8140131843@gmail.com','dp8140131843@gmail.com','8140131843','','oddevenandriod@gmail.com','2019-01-04','8500','','','','','','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('11','1','1','0','0','E010','Net Developer','Darshan Jadav','','darshanj','e5cb7975b455e6e5bf108c20ad908c66','jadav.darshan66@gmail.com','jadav.darshan66@gmail.com','9601408966','9601408966','','2019-03-18','10000','','','','','','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('12','1','1','0','0','E011','Android Developer','Maulik Dadhaniya','','maulikd','dde70492521e5648fc406a441a1ba3e2','oddevenandriod@gmail.com','maulik.dadhaniya1995@gmail.com','8866490355','8866490355','oddevenandriod','2019-03-22','20000','','','','','404 - Pavan Appartment , Near Mahaprabhuji bethak , Naroda , Ahmedabad 382330','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('13','1','1','0','0','E012','Seo Developer','Satish Jogani','','satishj','d02888c993a1e9660042cbc083455c3e','seooddeven@gmail.com','satishjogani200942@gmail.com','8866983849','','adminseooddeven@gmail.com','2019-04-01','17500','','','','','105, Jay Ambe Soc., Thakkarbapanagar Road, Bapunagar, Ahmedabad 382350','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('14','1','1','0','0','E013','Php Developer','Varsha Darji','','varsha','ff2f87e3b76f13788e41d6feae7c5dbb','varshaphp@gmail.com','','9904890183','9327459011','varshaphp@gmail.com','2019-05-21','2500','','','','','','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('15','1','1','0','0','E014','Business Development Executive','Lipsa Bhut','','lipsa','dbbe41467b686edd95a69c5eda12357d','sales1@oddeveninfotech.com','lipsacbhut@gmail.com','9033412217','9773235715','hello','2019-09-02','25000','','','','','B-26 Umiya Krupa Soc. \r\nUrjnanagar 1 Kudasan Gandhinagar','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('17','1','1','0','0','E015','Seo Developer','Krupa patel','','krupapatel','b9d8d53f07c526edfd425226ba302b18','oddevenseo1@gmail.com','patelkrupa096@gmail.com','7984110894','9723021204','oddevenseo1','2022-06-01','12000','','','','','A-601 sahyog greens, opp preksha bharti, koba, gandhinagar\r\nC503 Bansari Greencity I Gandhinagar\r\n','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('18','1','1','0','0','E016','Seo Developer','Praful Gohil','','praful','58de109b11ab24b855ad09864648c34a','prafulg@gmail.com','','9033374338','','','','','','','','','','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('19','1','1','0','0','E016','Seo Developer','Mittal Ashvinbhai Patel','','mittal','0192023a7bbd73250516f069df18b500','seooddeven1@gmail.com','me.mitttalpatel@gmail.com','8140960825','9979098669','seooddeven1@gmail.com','2021-08-02','8000','','','','','Saundarya 444, Kh- Road, Sargasan Cross, Gandhinagar','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('20','1','1','0','0','E017','Php Developer','Yogesh Nathva','','yogeshn','e857f9b3fa03593ff7787a6ba9ecd5c1','yogeshnphp@gmail.com','yogeshnakhva@gmail.com','9601089399','9327133788','yogeshnphp@gmail.com','2021-11-16','27000','','','','','','0');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('21','1','1','0','0','E017','Seo Developer','Nirzaree Patel','','nirzaree','e857f9b3fa03593ff7787a6ba9ecd5c1','NirzareePatel@gmail.com','','8490811651','','','2022-02-23','','','','','','','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('22','1','1','0','0','E018','Business Development Executive','Palak P Dave','','palakdave','f14e9dc8ed9b59c34abe0ba3a254ebc8','sales@oddeveninfotech.com','palak144dave@gmail.com','9408502770','','hello@oddeveninfotech.com','2022-09-21','10000','Central bank of India','CBIN0284806','Palak Pankaj kumar Dave','3760270913','','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('23','1','1','0','0','E019','Business Development Executive','Prashansa Bajpai','','prashansab','64191eabd037d277c3be3b843590c750','prashansab@oddeveninfotech.com','bajpaiprashansa97@gmail.com','08223075171','08223075171','prashansab@oddeveninfotech.com','2022-09-26','30000','','','','','25/357\r\nNear union bank saman branch','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('24','1','1','0','0','E020','Php Developer','Harsh Vaghela','','harshv','5c1a08fa6d516f56281dae315182b595','harshmvaghela99@gmail.com','','9510151546','','','2022-09-26','','','','','','','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('25','1','1','0','0','E021','Business Development Executive','Uttam Banugaria','','uttam','174d842b3acca61f893538477ffd3c8b','uttamb@oddeveninfotech.com','uttam.banugaria@gmail.com','9998533447','','','2022-10-10','45000','State bank of India ','SBIN0010994','Uttam Vajubhai Banugaria','33828639588','Gandhinagar','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('26','1','1','0','0','E022','Business Analyst','Sumit Jain','','sumit.jain','e6e061838856bf47e1de730719fb2609','jainsumit989@gmail.com','jainsumit989@gmail.com','9958463093','','oddeven','2018-03-01','25000','','','','','','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('27','1','1','0','0','E023','Php Developer','Najma Sodha','','najmas','e857f9b3fa03593ff7787a6ba9ecd5c1','najma.oddeven@gmail.com','sodhanajma@gmail.com','9510867174','','','2022-12-01','8000','STATE BANK OF INDIA','SBIN0060044','SODHA NAJAMA TAIYABBHAI','30595001058','','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('28','1','1','0','0','E024','Web Designer','Brijkishor Sapovadiya','','brijkishor','e857f9b3fa03593ff7787a6ba9ecd5c1','brijoddeven@gmail.com','brijsapovadiya9241@gmail.com','6352345485','9662184621','','2023-01-05','','SBI','SBIN0060064','Mr. BRIJ DHIRUBHAI SAPOVADIYA','36826292964','','0');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('30','1','1','0','0','E025','QA','Priyanka Patel','','priyanka.patel','e857f9b3fa03593ff7787a6ba9ecd5c1','qa.priyankapatel@gmail.com','pihu17342000@gmail.com','9870071648','','qa.priyankapatel@gmail.com','2023-01-09','12000','','','','','','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('31','1','1','0','0','E026','Business Analyst','Priti Palivar','','priti_palivar','e857f9b3fa03593ff7787a6ba9ecd5c1','priti@oddeveninfotech.com','','','','','','33500','AXIS BANK','UTIB0000809','PRITIBA PALIVAR','922010065136539','','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('32','1','1','0','0','E027','Salesforce Developer','Prasad Patil','','prasadpatil','6350852d63423637f02fb4768f7b6c9d','patilprasad1904@gmail.com','prasadpatil751998@gmail.com','','','','2023-07-04','','HDFC BANK ','HDFC0009201','PRASAD WALMIK PATIL','50100501338056','Maharashtara','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('33','1','1','0','0','E028','Salesforce Developer','Vishal Panchal','','vishal.panchal','92de7181ce13521712ba5042ab46e793','vishalp@pierconsultinginc.com','panchalvishal543@gmail.com','','','rj@oddeveninfotech.com','','75000','','','','','Rajasthan ','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('34','1','1','0','0','E024','Salesforce Developer','Nikhil Devidasrao Khadse','','nikhil-khadse','02fe34c8f1c48e6a7d822be40a198373','temp@oddeveninfotech.com','dknikhilo@gmail.com','8788727713','8378848224','','2023-08-28','20000','Punjab National Bank','PUNB0547300','Nikhil Devidasrao Khadse','5473000100040776','93/741 Near Jaimangal BRTS, Nirmal Apartment. Ahmedabad','0');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('35','1','1','0','0','E029','Seo Developer','Ayana Yakin Patel','','ayanapatel','6a4e7d8ce89ddb6938e840673c2e6bf2','seooddeven1+1@gmail.com','ayana.patel1993@gmail.com','9925412564','7096431570','','2023-10-16','','State Bank of India','SBIN0017900','AYANA BHARATBHAI PATEL','35220644377','A/46 Prerna Bunglows, Bapasita Ram Chowk, New Naroda, Ahmedabad','0');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('36','1','1','0','0','E030','Php Developer','Nikhil Inani','','Nikhil-PHP','dd907d50200fc472d277fcea0291182d','nikhiloddeven@gmail.com','nikhilmaheshwari279@gmail.com','9737670994','','nikhiloddeven@gmail.com','2023-11-20','','','','','','','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('37','1','1','0','0','E031','Php Developer','Daxaben Maheshbhai  Makwana','','daxachudasma','d329e95ee6369312a3296a787053d57f','daxachudasmaoe@gmail.com','dxchudasma@gmail.com','9574345706','9712045706','daxachudasmaoe@gmail.com','2023-12-26','','','','','','D - 504 Shreeji Icon Kudasan Gandhiangar','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('38','1','1','0','0','E032','Php Developer','Khushboo Tailor','','khushboo','f996cfbd1438f281ff0e39a8522547e0','khushboopatel12021@gmail.com','khushbootailor111@gmail.com','9099686960','7984341326','','2024-06-10','','','','','','','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('39','1','1','0','0','E032','Php Developer','Khushboo Tailor','','khushboo','f996cfbd1438f281ff0e39a8522547e0','khushboopatel12021@gmail.com','khushbootailor111@gmail.com','9099686960','7984341326','','2024-06-10','','','','','','','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('40','1','1','0','0','E032','Php Developer','Khushboo Tailor','','khushboo','f996cfbd1438f281ff0e39a8522547e0','khushboopatel12021@gmail.com','khushbootailor111@gmail.com','9099686960','7984341326','','2024-06-10','46000','','','','','','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('41','1','1','0','0','E033','Business Development Executive','Heema Shah','','heemashah','e857f9b3fa03593ff7787a6ba9ecd5c1','sales+1@oddeveninfotech.com','','','','oddeven','2024-06-20','','','','','','','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('42','1','1','0','0','E034','Php Developer','Raval Ravi','2000-06-19','raval-ravi','e58cc3fe4b3387c893c8fc9dd43a829a','bhaveshpatelodd@gmail.com','ravi52@gmail.com','8780717389','9720179308','','2024-10-01','10000','','','','','kadi','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('44','1','1','0','0','E035','Php Developer','Kritika Kumari','2002-05-07','kritika_kumari','5897b58489425abae723334a8d322123','kritikaoddeveninfotech@gmail.com','','','','','2025-03-17','22000','','','','','','0');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('45','1','1','0','0','E036','Web Designer','Aarti Borad','','aarti_borad','23e04174e9d8f6769c3e3ada413a81a1','adborad122a@gmail.com','adborad122a@gmail.com','8980664404','8980664404','oddeven','2024-10-14','3000','','','','','','0');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('46','1','1','0','0','E037','Php Developer','Maniya Mansi','','maniya_mansi','90431b1aac9b97544da34ba727527c3f','maniyamansioe@gmail.com','','8849488045','8849488045','oddeven','2024-10-14','3000','','','','','','0');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('47','1','1','0','0','E038','QA','SMIT JOSHI','','smitjoshi','0192ed4260a43c07e82a619189e1903b','smitjoshiqa@gmail.com','','9624948345','','','','11000','','','','','','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('48','1','1','0','0','E039','Salesforce Developer','Rohit Kumar Chandan','2000-12-01','rohit_chandan','74270fa03fe4875a10e0940d3951452f','r@gmail.com','i.rohitsharma2023@gmail.com','7717364689','','','2025-05-01','65000','','','','','','0');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('49','1','1','0','0','E040','Php Developer','Darshan Malani','','darshan_malani','393c8a4c014b556f7e0bd86c78b86be5','malaniderrick@gmail.com','malaniderrick@gmail.com','8866735283','9898805021','oddeven','2025-06-09','45000','','','','','','0');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('50','1','1','0','0','E041','Seo Developer','Senjaliya Hardik Rameshbhai','','hardik_senjaliya','9159d2114c81c7b6c7a346863485eb49','hardikoddeven@gmail.com','senjaliyahardik7373@gmail.com','7874987578','9879367833','oddeven','','','','','','','','1');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('51','1','1','0','0','E42','Salesforce Developer','Rahul Vaswani','','rahul_vaswani','7b7f71bff78951c020e9c647a32bb839','rahul.oddeven123@gmail.com','','9586666555','','','2026-03-09','','','','','','F-201 Elegance, Hansol, Sardarnagar, Ahmedabad. Gujarat.','0');
INSERT INTO `employeestbl` (`id`,`company_id`,`department_id`,`is_admin_access`,`access_role`,`employeeCode`,`designation`,`name`,`birthdate`,`employeeUname`,`employeeUpass`,`companyEmail`,`personalEmail`,`mobile1`,`mobile2`,`skypeUname`,`joiningDate`,`salary`,`bankName`,`bankIFSCno`,`bankAcHolderName`,`bankAcNo`,`address`,`status`) VALUES('52','1','1','0','0','E43','Salesforce Developer','Pratyush Maheshbhai Baldha','','pratyush','2fd514688810e8d5df92afb1bccfa6f7','pratyush@oddeveninfotech.com','','8200590500','','','','10000','','','','','','0');

DROP TABLE IF EXISTS `exit_checklist`;
CREATE TABLE `exit_checklist` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `exit_id` bigint(20) NOT NULL,
  `item_type` enum('asset','access','project','document','other') NOT NULL,
  `item_label` varchar(180) NOT NULL,
  `status` enum('pending','cleared','not_applicable') NOT NULL DEFAULT 'pending',
  `cleared_by` int(11) DEFAULT NULL,
  `cleared_at` datetime DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_exit_item` (`exit_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `expencecategory`;
CREATE TABLE `expencecategory` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `expenseCategory` varchar(255) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
INSERT INTO `expencecategory` (`category_id`,`expenseCategory`) VALUES('1','Domain');
INSERT INTO `expencecategory` (`category_id`,`expenseCategory`) VALUES('5','Electricity');
INSERT INTO `expencecategory` (`category_id`,`expenseCategory`) VALUES('7','Hosting');
INSERT INTO `expencecategory` (`category_id`,`expenseCategory`) VALUES('9','SSL');
INSERT INTO `expencecategory` (`category_id`,`expenseCategory`) VALUES('10','Office expenses');
INSERT INTO `expencecategory` (`category_id`,`expenseCategory`) VALUES('11','Salary');
INSERT INTO `expencecategory` (`category_id`,`expenseCategory`) VALUES('12','Freelancer Payment');
INSERT INTO `expencecategory` (`category_id`,`expenseCategory`) VALUES('13','Other');
INSERT INTO `expencecategory` (`category_id`,`expenseCategory`) VALUES('14','Office Rent');

DROP TABLE IF EXISTS `expense`;
CREATE TABLE `expense` (
  `expense_id` int(11) NOT NULL AUTO_INCREMENT,
  `expenseCategory` varchar(255) NOT NULL,
  `expensedate` date NOT NULL,
  `amount` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`expense_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

DROP TABLE IF EXISTS `finance_expenses`;
CREATE TABLE `finance_expenses` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `expense_date` date NOT NULL,
  `vendor` varchar(180) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  `tax_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `currency_code` char(3) NOT NULL DEFAULT 'INR',
  `bank_account_id` int(11) DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_fin_expense` (`company_id`,`expense_date`,`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `finance_invoice_items`;
CREATE TABLE `finance_invoice_items` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint(20) NOT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT 1.00,
  `rate` decimal(14,2) NOT NULL DEFAULT 0.00,
  `tax_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_invoice_item` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `finance_invoices`;
CREATE TABLE `finance_invoices` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `client_id` bigint(20) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `invoice_number` varchar(60) NOT NULL,
  `invoice_type` enum('gst','non_gst','international') NOT NULL DEFAULT 'gst',
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `currency_code` char(3) NOT NULL DEFAULT 'INR',
  `subtotal` decimal(14,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','sent','viewed','partially_paid','paid','overdue','cancelled') NOT NULL DEFAULT 'draft',
  `recurring_frequency` enum('none','monthly','quarterly','yearly') NOT NULL DEFAULT 'none',
  `next_invoice_date` date DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `viewed_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_invoice` (`company_id`,`invoice_number`),
  KEY `idx_invoice_status` (`company_id`,`status`,`due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `finance_payments`;
CREATE TABLE `finance_payments` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `invoice_id` bigint(20) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  `transaction_reference` varchar(150) DEFAULT NULL,
  `gateway` enum('bank','paypal','stripe','wise','cash','other') NOT NULL DEFAULT 'bank',
  `gateway_fee` decimal(14,2) NOT NULL DEFAULT 0.00,
  `bank_charge` decimal(14,2) NOT NULL DEFAULT 0.00,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_payment_invoice` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `followup_type_tbl`;
CREATE TABLE `followup_type_tbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `followup_type_tbl` (`id`,`name`) VALUES('6','Call');
INSERT INTO `followup_type_tbl` (`id`,`name`) VALUES('7','Whatsapp');
INSERT INTO `followup_type_tbl` (`id`,`name`) VALUES('9','Rj Email ');
INSERT INTO `followup_type_tbl` (`id`,`name`) VALUES('10','Hello Email');

DROP TABLE IF EXISTS `holidaytbl`;
CREATE TABLE `holidaytbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `holidayDate` date NOT NULL,
  `holidayTitle` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
INSERT INTO `holidaytbl` (`id`,`holidayDate`,`holidayTitle`) VALUES('1','2026-01-14','Utrayan');
INSERT INTO `holidaytbl` (`id`,`holidayDate`,`holidayTitle`) VALUES('2','2026-01-15','Next day to Utrayan');
INSERT INTO `holidaytbl` (`id`,`holidayDate`,`holidayTitle`) VALUES('4','2026-08-28','Rakshabandhan');
INSERT INTO `holidaytbl` (`id`,`holidayDate`,`holidayTitle`) VALUES('5','2026-09-04','Janmasthami');
INSERT INTO `holidaytbl` (`id`,`holidayDate`,`holidayTitle`) VALUES('7','2026-11-08','Diwali');
INSERT INTO `holidaytbl` (`id`,`holidayDate`,`holidayTitle`) VALUES('9','2026-08-15','Independence Day');
INSERT INTO `holidaytbl` (`id`,`holidayDate`,`holidayTitle`) VALUES('10','2026-03-04','Dhuleti');
INSERT INTO `holidaytbl` (`id`,`holidayDate`,`holidayTitle`) VALUES('12','2026-01-26','Republic Day');
INSERT INTO `holidaytbl` (`id`,`holidayDate`,`holidayTitle`) VALUES('14','2026-11-10','New Year & Bhai Dooj');
INSERT INTO `holidaytbl` (`id`,`holidayDate`,`holidayTitle`) VALUES('15','2026-10-20','Dussera');

DROP TABLE IF EXISTS `hr_letter_delivery_logs`;
CREATE TABLE `hr_letter_delivery_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `letter_id` bigint(20) NOT NULL,
  `recipient_email` varchar(180) NOT NULL,
  `delivery_status` enum('sent','failed') NOT NULL,
  `error_message` varchar(500) DEFAULT NULL,
  `sent_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_letter_delivery` (`letter_id`,`created_at`),
  CONSTRAINT `fk_letter_delivery` FOREIGN KEY (`letter_id`) REFERENCES `hr_letters` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `hr_letter_templates`;
CREATE TABLE `hr_letter_templates` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `letter_type` varchar(40) NOT NULL,
  `name` varchar(150) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body_html` mediumtext NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_letter_template_company` (`company_id`,`letter_type`,`status`),
  CONSTRAINT `fk_letter_template_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `hr_letter_templates` (`id`,`company_id`,`letter_type`,`name`,`subject`,`body_html`,`is_default`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('1','1','offer','Standard Offer Letter','Offer of Employment - {{designation}}','<p>Dear {{employee_name}},</p><p>We are pleased to offer you the position of <strong>{{designation}}</strong> at {{company_name}}.</p><p>Your proposed joining date is {{joining_date}}. The detailed employment terms will be shared separately.</p><p>Sincerely,<br>Human Resources<br>{{company_name}}</p>','1','1','0','2026-06-06 22:22:15','2026-06-06 22:22:15');
INSERT INTO `hr_letter_templates` (`id`,`company_id`,`letter_type`,`name`,`subject`,`body_html`,`is_default`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('2','1','appointment','Standard Appointment Letter','Appointment as {{designation}}','<p>Dear {{employee_name}},</p><p>We are pleased to appoint you as <strong>{{designation}}</strong> with effect from {{joining_date}}.</p><p>Your employment will be governed by company policies and the agreed terms.</p><p>Sincerely,<br>Human Resources<br>{{company_name}}</p>','1','1','0','2026-06-06 22:22:15','2026-06-06 22:22:15');
INSERT INTO `hr_letter_templates` (`id`,`company_id`,`letter_type`,`name`,`subject`,`body_html`,`is_default`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('3','1','confirmation','Employment Confirmation','Confirmation of Employment','<p>Dear {{employee_name}},</p><p>We are pleased to confirm your employment as <strong>{{designation}}</strong> effective {{effective_date}}.</p><p>We appreciate your contribution and look forward to your continued association.</p><p>Sincerely,<br>Human Resources<br>{{company_name}}</p>','1','1','0','2026-06-06 22:22:15','2026-06-06 22:22:15');
INSERT INTO `hr_letter_templates` (`id`,`company_id`,`letter_type`,`name`,`subject`,`body_html`,`is_default`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('4','1','promotion','Promotion Letter','Promotion to {{new_designation}}','<p>Dear {{employee_name}},</p><p>We are pleased to promote you from {{designation}} to <strong>{{new_designation}}</strong> effective {{effective_date}}.</p><p>Congratulations on this well-deserved progression.</p><p>Sincerely,<br>Human Resources<br>{{company_name}}</p>','1','1','0','2026-06-06 22:22:15','2026-06-06 22:22:15');
INSERT INTO `hr_letter_templates` (`id`,`company_id`,`letter_type`,`name`,`subject`,`body_html`,`is_default`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('5','1','salary_revision','Salary Revision Letter','Salary Revision Effective {{effective_date}}','<p>Dear {{employee_name}},</p><p>Your compensation has been revised to <strong>{{revised_salary}}</strong> effective {{effective_date}}.</p><p>All other employment terms remain unchanged.</p><p>Sincerely,<br>Human Resources<br>{{company_name}}</p>','1','1','0','2026-06-06 22:22:15','2026-06-06 22:22:15');
INSERT INTO `hr_letter_templates` (`id`,`company_id`,`letter_type`,`name`,`subject`,`body_html`,`is_default`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('6','1','warning','Employee Warning Letter','Formal Warning','<p>Dear {{employee_name}},</p><p>This letter serves as a formal warning regarding: <strong>{{reason}}</strong>.</p><p>You are expected to take immediate corrective action and comply with company policy.</p><p>Sincerely,<br>Human Resources<br>{{company_name}}</p>','1','1','0','2026-06-06 22:22:15','2026-06-06 22:22:15');
INSERT INTO `hr_letter_templates` (`id`,`company_id`,`letter_type`,`name`,`subject`,`body_html`,`is_default`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('7','1','experience','Experience Letter','Experience Certificate','<p>To Whom It May Concern,</p><p>This is to certify that <strong>{{employee_name}}</strong> ({{employee_code}}) worked with {{company_name}} as {{designation}} from {{joining_date}} until {{last_working_date}}.</p><p>We wish them success in future endeavors.</p><p>Sincerely,<br>Human Resources<br>{{company_name}}</p>','1','1','0','2026-06-06 22:22:15','2026-06-06 22:22:15');
INSERT INTO `hr_letter_templates` (`id`,`company_id`,`letter_type`,`name`,`subject`,`body_html`,`is_default`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('8','1','relieving','Relieving Letter','Relieving Confirmation','<p>Dear {{employee_name}},</p><p>This confirms that you have been relieved from your duties as {{designation}} effective {{last_working_date}}.</p><p>We thank you for your service and wish you well.</p><p>Sincerely,<br>Human Resources<br>{{company_name}}</p>','1','1','0','2026-06-06 22:22:15','2026-06-06 22:22:15');
INSERT INTO `hr_letter_templates` (`id`,`company_id`,`letter_type`,`name`,`subject`,`body_html`,`is_default`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('9','1','termination','Termination Letter','Termination of Employment','<p>Dear {{employee_name}},</p><p>Your employment with {{company_name}} is terminated effective {{last_working_date}}.</p><p>Reason: {{reason}}</p><p>Please complete the required exit and clearance process.</p><p>Sincerely,<br>Human Resources<br>{{company_name}}</p>','1','1','0','2026-06-06 22:22:15','2026-06-06 22:22:15');

DROP TABLE IF EXISTS `hr_letters`;
CREATE TABLE `hr_letters` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `template_id` bigint(20) DEFAULT NULL,
  `letter_type` varchar(40) NOT NULL,
  `reference_no` varchar(60) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body_html` mediumtext NOT NULL,
  `issue_date` date NOT NULL,
  `status` enum('draft','issued','emailed','cancelled') NOT NULL DEFAULT 'draft',
  `recipient_email` varchar(180) DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `issued_by` int(11) NOT NULL DEFAULT 0,
  `issued_at` datetime DEFAULT NULL,
  `emailed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_letter_reference` (`company_id`,`reference_no`),
  KEY `idx_letter_employee` (`employee_id`,`issue_date`),
  KEY `fk_hr_letter_template` (`template_id`),
  CONSTRAINT `fk_hr_letter_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `fk_hr_letter_employee` FOREIGN KEY (`employee_id`) REFERENCES `employeestbl` (`id`),
  CONSTRAINT `fk_hr_letter_template` FOREIGN KEY (`template_id`) REFERENCES `hr_letter_templates` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `invoice_details`;
CREATE TABLE `invoice_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint(20) NOT NULL,
  `perticular` varchar(255) NOT NULL,
  `amount` float NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `country_id` int(11) NOT NULL,
  `bank_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

DROP TABLE IF EXISTS `invoice_reminders`;
CREATE TABLE `invoice_reminders` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `invoice_id` bigint(20) NOT NULL,
  `reminder_type` enum('manual','due_soon','overdue') NOT NULL DEFAULT 'manual',
  `recipient_email` varchar(180) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('queued','sent','failed') NOT NULL DEFAULT 'queued',
  `sent_at` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_reminder` (`invoice_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `invoicetbl`;
CREATE TABLE `invoicetbl` (
  `invoice_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(50) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `project_Id` bigint(20) NOT NULL,
  `person_name` varchar(255) NOT NULL,
  `total_amount` float NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '1->active,2->inactive',
  PRIMARY KEY (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

DROP TABLE IF EXISTS `lateworkhourstbl`;
CREATE TABLE `lateworkhourstbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employeeId` int(11) NOT NULL,
  `workDate` varchar(255) NOT NULL,
  `month` varchar(255) NOT NULL,
  `year` varchar(255) NOT NULL,
  `signinTime` varchar(255) NOT NULL,
  `reason` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

DROP TABLE IF EXISTS `lead_followup`;
CREATE TABLE `lead_followup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lead_type` varchar(100) NOT NULL,
  `followup_type` varchar(150) NOT NULL,
  `remarks` text DEFAULT NULL,
  `next_followup_date` date DEFAULT NULL,
  `next_followup_time` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `lead_id` int(11) NOT NULL,
  `status` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `lead_source_tbl`;
CREATE TABLE `lead_source_tbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `lead_source_tbl` (`id`,`name`) VALUES('1','Contact Us Page');
INSERT INTO `lead_source_tbl` (`id`,`name`) VALUES('2','EMail');
INSERT INTO `lead_source_tbl` (`id`,`name`) VALUES('3','Facebook');
INSERT INTO `lead_source_tbl` (`id`,`name`) VALUES('4','Instagram');
INSERT INTO `lead_source_tbl` (`id`,`name`) VALUES('5','Linkedin');
INSERT INTO `lead_source_tbl` (`id`,`name`) VALUES('6','Call');
INSERT INTO `lead_source_tbl` (`id`,`name`) VALUES('7','Whatsapp');
INSERT INTO `lead_source_tbl` (`id`,`name`) VALUES('8','Upwork');
INSERT INTO `lead_source_tbl` (`id`,`name`) VALUES('9','Fiverr');
INSERT INTO `lead_source_tbl` (`id`,`name`) VALUES('10','Reference');

DROP TABLE IF EXISTS `leads`;
CREATE TABLE `leads` (
  `lead_id` int(11) NOT NULL AUTO_INCREMENT,
  `lead_date` date NOT NULL,
  `executive_name` varchar(255) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `mobile_no1` varchar(15) DEFAULT NULL,
  `mobile_no2` varchar(15) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `personal_email` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` varchar(25) DEFAULT NULL,
  `nick_name` varchar(255) DEFAULT NULL,
  `lead_source` int(11) DEFAULT 0,
  `assign_to` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`lead_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `leave_master`;
CREATE TABLE `leave_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL,
  `subject` text NOT NULL,
  `description` text NOT NULL,
  `type` varchar(100) NOT NULL,
  `start_date` varchar(12) NOT NULL,
  `end_date` varchar(12) NOT NULL,
  `is_approved` int(11) DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `leave_policies`;
CREATE TABLE `leave_policies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `annual_entitlement` decimal(6,2) NOT NULL DEFAULT 0.00,
  `carry_forward` tinyint(1) NOT NULL DEFAULT 0,
  `max_carry_forward` decimal(6,2) NOT NULL DEFAULT 0.00,
  `is_paid` tinyint(1) NOT NULL DEFAULT 1,
  `sandwich_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `allow_half_day` tinyint(1) NOT NULL DEFAULT 0,
  `requires_document_after_days` decimal(6,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_leave_policy` (`company_id`,`leave_type_id`),
  KEY `fk_leave_policy_type` (`leave_type_id`),
  CONSTRAINT `fk_leave_policy_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `fk_leave_policy_type` FOREIGN KEY (`leave_type_id`) REFERENCES `leavetypetbl` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `leave_policies` (`id`,`company_id`,`leave_type_id`,`annual_entitlement`,`carry_forward`,`max_carry_forward`,`is_paid`,`sandwich_enabled`,`allow_half_day`,`requires_document_after_days`,`status`,`created_at`,`updated_at`) VALUES('1','1','1','10.00','0','0.00','1','1','0','0.00','1','2026-06-06 21:47:53','2026-06-06 21:47:53');
INSERT INTO `leave_policies` (`id`,`company_id`,`leave_type_id`,`annual_entitlement`,`carry_forward`,`max_carry_forward`,`is_paid`,`sandwich_enabled`,`allow_half_day`,`requires_document_after_days`,`status`,`created_at`,`updated_at`) VALUES('2','1','2','0.00','0','0.00','0','0','0','0.00','1','2026-06-06 21:47:53','2026-06-06 21:47:53');
INSERT INTO `leave_policies` (`id`,`company_id`,`leave_type_id`,`annual_entitlement`,`carry_forward`,`max_carry_forward`,`is_paid`,`sandwich_enabled`,`allow_half_day`,`requires_document_after_days`,`status`,`created_at`,`updated_at`) VALUES('3','1','3','0.00','0','0.00','0','0','0','0.00','1','2026-06-06 21:47:53','2026-06-06 21:47:53');
INSERT INTO `leave_policies` (`id`,`company_id`,`leave_type_id`,`annual_entitlement`,`carry_forward`,`max_carry_forward`,`is_paid`,`sandwich_enabled`,`allow_half_day`,`requires_document_after_days`,`status`,`created_at`,`updated_at`) VALUES('4','1','4','0.00','0','0.00','0','0','0','0.00','1','2026-06-06 21:47:53','2026-06-06 21:47:53');
INSERT INTO `leave_policies` (`id`,`company_id`,`leave_type_id`,`annual_entitlement`,`carry_forward`,`max_carry_forward`,`is_paid`,`sandwich_enabled`,`allow_half_day`,`requires_document_after_days`,`status`,`created_at`,`updated_at`) VALUES('5','1','5','0.00','0','0.00','0','0','1','0.00','1','2026-06-06 21:47:53','2026-06-06 21:47:53');

DROP TABLE IF EXISTS `leave_requests`;
CREATE TABLE `leave_requests` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `legacy_leave_id` int(11) DEFAULT NULL,
  `employee_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `subject` varchar(180) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `requested_days` decimal(7,2) NOT NULL DEFAULT 0.00,
  `sandwich_days` decimal(7,2) NOT NULL DEFAULT 0.00,
  `payable_days` decimal(7,2) NOT NULL DEFAULT 0.00,
  `unpaid_days` decimal(7,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `employee_note` varchar(500) DEFAULT NULL,
  `approver_note` varchar(500) DEFAULT NULL,
  `approved_by` int(11) NOT NULL DEFAULT 0,
  `approved_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_leave_legacy` (`legacy_leave_id`),
  KEY `idx_leave_request_employee` (`employee_id`,`start_date`),
  KEY `idx_leave_request_company_status` (`company_id`,`status`),
  KEY `fk_leave_request_type` (`leave_type_id`),
  CONSTRAINT `fk_leave_request_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `fk_leave_request_employee` FOREIGN KEY (`employee_id`) REFERENCES `employeestbl` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_leave_request_type` FOREIGN KEY (`leave_type_id`) REFERENCES `leavetypetbl` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `leave_settings`;
CREATE TABLE `leave_settings` (
  `company_id` int(11) NOT NULL,
  `paid_leave_per_year` decimal(6,2) NOT NULL DEFAULT 10.00,
  `comp_off_expiry_days` int(11) NOT NULL DEFAULT 90,
  `minimum_comp_off_minutes` int(11) NOT NULL DEFAULT 240,
  `sandwich_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`company_id`),
  CONSTRAINT `fk_leave_settings_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `leave_settings` (`company_id`,`paid_leave_per_year`,`comp_off_expiry_days`,`minimum_comp_off_minutes`,`sandwich_enabled`,`updated_at`) VALUES('1','10.00','90','240','1','2026-06-06 21:47:53');

DROP TABLE IF EXISTS `leavetypetbl`;
CREATE TABLE `leavetypetbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `leavetypetbl` (`id`,`name`) VALUES('1','Paid Leave');
INSERT INTO `leavetypetbl` (`id`,`name`) VALUES('2','Casual Leave');
INSERT INTO `leavetypetbl` (`id`,`name`) VALUES('3','Medical Leave');
INSERT INTO `leavetypetbl` (`id`,`name`) VALUES('4','Emergency Leave');
INSERT INTO `leavetypetbl` (`id`,`name`) VALUES('5','Half Day Leave');

DROP TABLE IF EXISTS `login_details`;
CREATE TABLE `login_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(40) NOT NULL,
  `login_datetime` datetime NOT NULL,
  `browser_details` text NOT NULL,
  `browser_type` varchar(255) DEFAULT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `mobile_browser` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `notice_events`;
CREATE TABLE `notice_events` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `notice_id` bigint(20) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `actor_type` enum('admin','employee','system') NOT NULL,
  `actor_id` int(11) NOT NULL DEFAULT 0,
  `event_type` varchar(40) NOT NULL,
  `details` varchar(500) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_notice_events` (`notice_id`,`created_at`),
  CONSTRAINT `fk_notice_event_notice` FOREIGN KEY (`notice_id`) REFERENCES `notices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `notice_receipts`;
CREATE TABLE `notice_receipts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `notice_id` bigint(20) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `first_seen_at` datetime DEFAULT NULL,
  `read_at` datetime DEFAULT NULL,
  `acknowledged_at` datetime DEFAULT NULL,
  `acknowledgement_ip` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_notice_receipt` (`notice_id`,`employee_id`),
  KEY `idx_employee_receipts` (`employee_id`,`read_at`),
  CONSTRAINT `fk_notice_receipt_employee` FOREIGN KEY (`employee_id`) REFERENCES `employeestbl` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_notice_receipt_notice` FOREIGN KEY (`notice_id`) REFERENCES `notices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `notice_targets`;
CREATE TABLE `notice_targets` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `notice_id` bigint(20) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_notice_department` (`notice_id`,`department_id`),
  UNIQUE KEY `uq_notice_employee` (`notice_id`,`employee_id`),
  KEY `fk_notice_target_department` (`department_id`),
  KEY `fk_notice_target_employee` (`employee_id`),
  CONSTRAINT `fk_notice_target_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_notice_target_employee` FOREIGN KEY (`employee_id`) REFERENCES `employeestbl` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_notice_target_notice` FOREIGN KEY (`notice_id`) REFERENCES `notices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `notices`;
CREATE TABLE `notices` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `notice_type` enum('company','hr','policy','holiday') NOT NULL DEFAULT 'company',
  `title` varchar(180) NOT NULL,
  `body_html` mediumtext NOT NULL,
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `audience_type` enum('all','department','employee') NOT NULL DEFAULT 'all',
  `publish_at` datetime NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `show_popup` tinyint(1) NOT NULL DEFAULT 0,
  `acknowledgement_required` tinyint(1) NOT NULL DEFAULT 0,
  `attachment_stored_name` varchar(255) DEFAULT NULL,
  `attachment_original_name` varchar(255) DEFAULT NULL,
  `attachment_mime` varchar(120) DEFAULT NULL,
  `attachment_size` int(11) NOT NULL DEFAULT 0,
  `status` enum('draft','scheduled','published','expired','cancelled') NOT NULL DEFAULT 'draft',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_notice_active` (`company_id`,`status`,`publish_at`,`expires_at`),
  CONSTRAINT `fk_notice_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `notices` (`id`,`company_id`,`notice_type`,`title`,`body_html`,`priority`,`audience_type`,`publish_at`,`expires_at`,`show_popup`,`acknowledgement_required`,`attachment_stored_name`,`attachment_original_name`,`attachment_mime`,`attachment_size`,`status`,`created_by`,`published_at`,`created_at`,`updated_at`) VALUES('1','1','company','Hello Oddeven Team. Don\'t use a cellphone inside an office and please make it silent and if you have any urgent call then you need to go outside the office for a talk in cellphone ','Hello Oddeven Team. <b>Don\'t use a cellphone inside an office</b> and please make it silent and if you have any urgent call then you need to go outside the office for a talk in cellphone but not longer than 5 minutes.','normal','all','2023-07-12 00:00:00','2023-12-31 00:00:00','0','0',NULL,NULL,NULL,'0','expired','0','2023-07-12 00:00:00','2026-06-06 22:34:30','2026-06-06 22:34:30');
INSERT INTO `notices` (`id`,`company_id`,`notice_type`,`title`,`body_html`,`priority`,`audience_type`,`publish_at`,`expires_at`,`show_popup`,`acknowledgement_required`,`attachment_stored_name`,`attachment_original_name`,`attachment_mime`,`attachment_size`,`status`,`created_by`,`published_at`,`created_at`,`updated_at`) VALUES('2','1','company','Also, all member must be coming between 09:30 AM - 10:30 AM please in morning.','Also, all member must be coming between <b>09:30 AM - 10:30 AM </b>please in morning.','normal','all','2018-08-01 00:00:00','2022-12-31 00:00:00','0','0',NULL,NULL,NULL,'0','expired','0','2018-08-01 00:00:00','2026-06-06 22:34:30','2026-06-06 22:34:30');
INSERT INTO `notices` (`id`,`company_id`,`notice_type`,`title`,`body_html`,`priority`,`audience_type`,`publish_at`,`expires_at`,`show_popup`,`acknowledgement_required`,`attachment_stored_name`,`attachment_original_name`,`attachment_mime`,`attachment_size`,`status`,`created_by`,`published_at`,`created_at`,`updated_at`) VALUES('3','1','company','If anyone wants to leave then need 2 days ago mentioned us with reason. Please follow all the above rules. ','If anyone wants to <b>leave then need 2 days ago</b> mentioned us with reason. Please follow all the above rules. ','normal','all','2018-08-01 00:00:00','2022-12-31 00:00:00','0','0',NULL,NULL,NULL,'0','expired','0','2018-08-01 00:00:00','2026-06-06 22:34:30','2026-06-06 22:34:30');
INSERT INTO `notices` (`id`,`company_id`,`notice_type`,`title`,`body_html`,`priority`,`audience_type`,`publish_at`,`expires_at`,`show_popup`,`acknowledgement_required`,`attachment_stored_name`,`attachment_original_name`,`attachment_mime`,`attachment_size`,`status`,`created_by`,`published_at`,`created_at`,`updated_at`) VALUES('4','1','company','If any employee found with lunch out or break out in attendance system even if they outside office then company will count half day for that day as well as any missing entry in por','<b>If any employee found with lunch out or break out in attendance system even if they outside office then company will count half day for that day as well as any missing entry in portal for attendance will be counting as a half day . If any one have issue then please let me know. <b>','normal','all','2023-07-12 00:00:00','2023-12-31 00:00:00','0','0',NULL,NULL,NULL,'0','expired','0','2023-07-12 00:00:00','2026-06-06 22:34:30','2026-06-06 22:34:30');

DROP TABLE IF EXISTS `noticetbl`;
CREATE TABLE `noticetbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notice` text NOT NULL,
  `startingDate` varchar(10) NOT NULL,
  `endingDate` varchar(10) NOT NULL,
  `noticeStatus` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
INSERT INTO `noticetbl` (`id`,`notice`,`startingDate`,`endingDate`,`noticeStatus`) VALUES('6','Hello Oddeven Team. <b>Don\'t use a cellphone inside an office</b> and please make it silent and if you have any urgent call then you need to go outside the office for a talk in cellphone but not longer than 5 minutes.','12-07-2023','31-12-2023','1');
INSERT INTO `noticetbl` (`id`,`notice`,`startingDate`,`endingDate`,`noticeStatus`) VALUES('8','Also, all member must be coming between <b>09:30 AM - 10:30 AM </b>please in morning.','01-08-2018','31-12-2022','1');
INSERT INTO `noticetbl` (`id`,`notice`,`startingDate`,`endingDate`,`noticeStatus`) VALUES('9','If anyone wants to <b>leave then need 2 days ago</b> mentioned us with reason. Please follow all the above rules. ','01-08-2018','31-12-2022','1');
INSERT INTO `noticetbl` (`id`,`notice`,`startingDate`,`endingDate`,`noticeStatus`) VALUES('12','<b>If any employee found with lunch out or break out in attendance system even if they outside office then company will count half day for that day as well as any missing entry in portal for attendance will be counting as a half day . If any one have issue then please let me know. <b>','12-07-2023','31-12-2023','1');

DROP TABLE IF EXISTS `payroll_adjustments`;
CREATE TABLE `payroll_adjustments` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `payroll_item_id` bigint(20) NOT NULL,
  `adjustment_type` enum('earning','deduction','retention','reimbursement') NOT NULL,
  `title` varchar(150) NOT NULL,
  `amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `notes` varchar(500) DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_payroll_adjustment_item` (`payroll_item_id`),
  CONSTRAINT `fk_payroll_adjustment_item` FOREIGN KEY (`payroll_item_id`) REFERENCES `payroll_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `payroll_items`;
CREATE TABLE `payroll_items` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `payroll_run_id` bigint(20) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `salary_structure_id` bigint(20) NOT NULL,
  `calendar_days` int(11) NOT NULL DEFAULT 0,
  `payable_days` decimal(7,2) NOT NULL DEFAULT 0.00,
  `present_days` decimal(7,2) NOT NULL DEFAULT 0.00,
  `paid_leave_days` decimal(7,2) NOT NULL DEFAULT 0.00,
  `unpaid_leave_days` decimal(7,2) NOT NULL DEFAULT 0.00,
  `weekly_off_days` decimal(7,2) NOT NULL DEFAULT 0.00,
  `holiday_days` decimal(7,2) NOT NULL DEFAULT 0.00,
  `required_minutes` int(11) NOT NULL DEFAULT 0,
  `effective_minutes` int(11) NOT NULL DEFAULT 0,
  `overtime_minutes` int(11) NOT NULL DEFAULT 0,
  `gross_salary` decimal(14,2) NOT NULL DEFAULT 0.00,
  `attendance_deduction` decimal(14,2) NOT NULL DEFAULT 0.00,
  `leave_deduction` decimal(14,2) NOT NULL DEFAULT 0.00,
  `professional_tax` decimal(14,2) NOT NULL DEFAULT 0.00,
  `custom_deduction` decimal(14,2) NOT NULL DEFAULT 0.00,
  `retention_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `overtime_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `extra_day_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_deduction` decimal(14,2) NOT NULL DEFAULT 0.00,
  `net_salary` decimal(14,2) NOT NULL DEFAULT 0.00,
  `status` enum('calculated','reviewed','approved','held') NOT NULL DEFAULT 'calculated',
  `notes` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_payroll_item_employee` (`payroll_run_id`,`employee_id`),
  KEY `idx_payroll_item_employee` (`employee_id`),
  KEY `fk_payroll_item_structure` (`salary_structure_id`),
  CONSTRAINT `fk_payroll_item_employee` FOREIGN KEY (`employee_id`) REFERENCES `employeestbl` (`id`),
  CONSTRAINT `fk_payroll_item_run` FOREIGN KEY (`payroll_run_id`) REFERENCES `payroll_runs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_payroll_item_structure` FOREIGN KEY (`salary_structure_id`) REFERENCES `salary_structures` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `payroll_runs`;
CREATE TABLE `payroll_runs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `period_year` smallint(6) NOT NULL,
  `period_month` tinyint(4) NOT NULL,
  `status` enum('draft','generated','pending_approval','approved','locked','cancelled') NOT NULL DEFAULT 'draft',
  `attendance_locked` tinyint(1) NOT NULL DEFAULT 0,
  `employee_count` int(11) NOT NULL DEFAULT 0,
  `gross_total` decimal(16,2) NOT NULL DEFAULT 0.00,
  `deduction_total` decimal(16,2) NOT NULL DEFAULT 0.00,
  `retention_total` decimal(16,2) NOT NULL DEFAULT 0.00,
  `net_total` decimal(16,2) NOT NULL DEFAULT 0.00,
  `generated_by` int(11) NOT NULL DEFAULT 0,
  `generated_at` datetime DEFAULT NULL,
  `approved_by` int(11) NOT NULL DEFAULT 0,
  `approved_at` datetime DEFAULT NULL,
  `locked_by` int(11) NOT NULL DEFAULT 0,
  `locked_at` datetime DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_payroll_company_period` (`company_id`,`period_year`,`period_month`),
  CONSTRAINT `fk_payroll_run_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_key` varchar(80) NOT NULL,
  `action_key` varchar(40) NOT NULL,
  `label` varchar(150) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_permission_module_action` (`module_key`,`action_key`)
) ENGINE=InnoDB AUTO_INCREMENT=226 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('1','companies','view','View companies',NULL,'2026-06-06 20:53:44');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('2','companies','create','Create companies',NULL,'2026-06-06 20:53:44');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('3','companies','edit','Edit companies',NULL,'2026-06-06 20:53:44');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('4','companies','delete','Deactivate companies',NULL,'2026-06-06 20:53:44');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('5','departments','view','View departments',NULL,'2026-06-06 20:53:44');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('6','departments','create','Create departments',NULL,'2026-06-06 20:53:44');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('7','departments','edit','Edit departments',NULL,'2026-06-06 20:53:44');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('8','departments','delete','Deactivate departments',NULL,'2026-06-06 20:53:44');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('9','employees','view','View employees',NULL,'2026-06-06 20:53:44');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('10','employees','create','Create employees',NULL,'2026-06-06 20:53:44');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('11','employees','edit','Edit employees',NULL,'2026-06-06 20:53:44');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('12','employees','delete','Delete employees',NULL,'2026-06-06 20:53:44');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('13','employees','export','Export employees',NULL,'2026-06-06 20:53:44');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('14','roles','view','View roles',NULL,'2026-06-06 20:53:44');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('15','roles','create','Create roles',NULL,'2026-06-06 20:53:44');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('16','roles','edit','Edit roles',NULL,'2026-06-06 20:53:44');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('17','roles','delete','Delete roles',NULL,'2026-06-06 20:53:44');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('18','roles','manage_permissions','Manage role permissions',NULL,'2026-06-06 20:53:44');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('19','audit','view','View audit logs',NULL,'2026-06-06 20:53:44');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('20','audit','export','Export audit logs',NULL,'2026-06-06 20:53:44');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('21','settings','view','View settings',NULL,'2026-06-06 20:53:44');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('22','settings','edit','Edit settings',NULL,'2026-06-06 20:53:44');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('45','employee_profiles','view','View employee profiles',NULL,'2026-06-06 21:04:50');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('46','employee_profiles','edit','Edit employee profiles',NULL,'2026-06-06 21:04:50');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('47','employee_contracts','view','View employee contracts',NULL,'2026-06-06 21:04:50');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('48','employee_contracts','create','Create employee contracts',NULL,'2026-06-06 21:04:50');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('49','employee_contracts','edit','Edit employee contracts',NULL,'2026-06-06 21:04:50');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('50','employee_contracts','delete','Delete employee contracts',NULL,'2026-06-06 21:04:50');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('51','employee_increments','view','View employee increments',NULL,'2026-06-06 21:04:50');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('52','employee_increments','create','Create employee increments',NULL,'2026-06-06 21:04:50');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('53','employee_documents','view','View employee documents',NULL,'2026-06-06 21:04:50');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('54','employee_documents','upload','Upload employee documents',NULL,'2026-06-06 21:04:50');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('55','employee_documents','download','Download employee documents',NULL,'2026-06-06 21:04:50');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('56','employee_documents','delete','Delete employee documents',NULL,'2026-06-06 21:04:50');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('69','shifts','view','View shifts',NULL,'2026-06-06 21:27:06');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('70','shifts','create','Create shifts',NULL,'2026-06-06 21:27:06');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('71','shifts','edit','Edit shifts',NULL,'2026-06-06 21:27:06');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('72','shifts','delete','Deactivate shifts',NULL,'2026-06-06 21:27:06');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('73','shift_assignments','view','View shift assignments',NULL,'2026-06-06 21:27:06');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('74','shift_assignments','assign','Assign employee shifts',NULL,'2026-06-06 21:27:06');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('75','shift_assignments','history','View shift history',NULL,'2026-06-06 21:27:06');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('83','attendance','view','View attendance',NULL,'2026-06-06 21:33:39');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('84','attendance','clock','Use attendance clock',NULL,'2026-06-06 21:33:39');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('85','attendance','correct','Correct attendance',NULL,'2026-06-06 21:33:39');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('86','attendance','lock','Lock attendance',NULL,'2026-06-06 21:33:39');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('87','attendance','approve','Approve attendance',NULL,'2026-06-06 21:33:39');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('93','attendance_review','view','View attendance review',NULL,'2026-06-06 21:41:06');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('94','attendance_review','correct','Correct attendance',NULL,'2026-06-06 21:41:06');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('95','attendance_review','approve','Approve attendance',NULL,'2026-06-06 21:41:06');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('96','attendance_review','reject','Reject attendance',NULL,'2026-06-06 21:41:06');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('97','attendance_review','lock','Lock attendance period',NULL,'2026-06-06 21:41:06');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('98','attendance_review','unlock','Unlock attendance period',NULL,'2026-06-06 21:41:06');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('99','attendance_review','export','Export attendance exceptions',NULL,'2026-06-06 21:41:06');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('107','leave_policies','view','View leave policies',NULL,'2026-06-06 21:47:53');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('108','leave_policies','edit','Edit leave policies',NULL,'2026-06-06 21:47:53');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('109','leave_requests','view','View leave requests',NULL,'2026-06-06 21:47:53');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('110','leave_requests','apply','Apply leave',NULL,'2026-06-06 21:47:53');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('111','leave_requests','approve','Approve leave',NULL,'2026-06-06 21:47:53');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('112','leave_requests','reject','Reject leave',NULL,'2026-06-06 21:47:53');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('113','leave_balances','view','View leave balances',NULL,'2026-06-06 21:47:53');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('114','leave_balances','adjust','Adjust leave balances',NULL,'2026-06-06 21:47:53');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('115','comp_off','view','View comp-off',NULL,'2026-06-06 21:47:53');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('116','comp_off','credit','Credit comp-off',NULL,'2026-06-06 21:47:53');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('117','comp_off','adjust','Adjust comp-off',NULL,'2026-06-06 21:47:53');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('129','salary_structures','view','View salary structures',NULL,'2026-06-06 21:58:51');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('130','salary_structures','edit','Edit salary structures',NULL,'2026-06-06 21:58:51');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('131','payroll','view','View payroll',NULL,'2026-06-06 21:58:51');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('132','payroll','generate','Generate payroll',NULL,'2026-06-06 21:58:51');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('133','payroll','recalculate','Recalculate payroll',NULL,'2026-06-06 21:58:51');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('134','payroll','approve','Approve payroll',NULL,'2026-06-06 21:58:51');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('135','payroll','lock','Lock payroll',NULL,'2026-06-06 21:58:51');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('136','payroll','adjust','Adjust payroll',NULL,'2026-06-06 21:58:51');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('137','payroll','export','Export payroll',NULL,'2026-06-06 21:58:51');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('147','hr_letters','view','View HR letters',NULL,'2026-06-06 22:18:42');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('148','hr_letters','create','Create HR letters',NULL,'2026-06-06 22:18:42');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('149','hr_letters','issue','Issue HR letters',NULL,'2026-06-06 22:18:42');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('150','hr_letters','email','Email HR letters',NULL,'2026-06-06 22:18:42');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('151','hr_letters','cancel','Cancel HR letters',NULL,'2026-06-06 22:18:42');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('152','hr_letter_templates','view','View HR letter templates',NULL,'2026-06-06 22:18:42');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('153','hr_letter_templates','create','Create HR letter templates',NULL,'2026-06-06 22:18:42');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('154','hr_letter_templates','edit','Edit HR letter templates',NULL,'2026-06-06 22:18:42');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('155','notices','view','View notices',NULL,'2026-06-06 22:28:45');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('156','notices','create','Create notices',NULL,'2026-06-06 22:28:45');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('157','notices','edit','Edit notices',NULL,'2026-06-06 22:28:45');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('158','notices','publish','Publish notices',NULL,'2026-06-06 22:28:45');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('159','notices','cancel','Cancel notices',NULL,'2026-06-06 22:28:45');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('160','notices','reports','View notice delivery reports',NULL,'2026-06-06 22:28:45');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('161','activity_audit','view','View activity audit dashboard',NULL,'2026-06-06 22:38:27');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('162','activity_audit','export','Export activity audit logs',NULL,'2026-06-06 22:38:27');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('163','activity_audit','retention','Manage audit retention',NULL,'2026-06-06 22:38:27');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('164','activity_audit','purge','Purge expired audit logs',NULL,'2026-06-06 22:38:27');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('165','clients','view','View clients',NULL,'2026-06-07 14:03:16');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('166','clients','create','Create clients',NULL,'2026-06-07 14:03:16');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('167','clients','edit','Edit clients',NULL,'2026-06-07 14:03:16');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('168','clients','delete','Delete clients',NULL,'2026-06-07 14:03:16');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('169','clients','export','Export clients',NULL,'2026-06-07 14:03:16');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('170','client_contacts','create','Create client contacts',NULL,'2026-06-07 14:03:16');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('171','client_contacts','edit','Edit client contacts',NULL,'2026-06-07 14:03:16');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('172','client_documents','upload','Upload client documents',NULL,'2026-06-07 14:03:16');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('173','client_documents','download','Download client documents',NULL,'2026-06-07 14:03:16');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('174','client_contracts','create','Create client contracts',NULL,'2026-06-07 14:03:16');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('175','client_contracts','edit','Edit client contracts',NULL,'2026-06-07 14:03:16');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('176','client_communications','create','Record client communications',NULL,'2026-06-07 14:03:16');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('177','resources','view','View resource allocations',NULL,'2026-06-07 14:22:50');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('178','resources','create','Create resource allocations',NULL,'2026-06-07 14:22:50');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('179','resources','edit','Edit resource allocations',NULL,'2026-06-07 14:22:50');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('180','resources','delete','Delete resource allocations',NULL,'2026-06-07 14:22:50');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('181','resources','export','Export resource reports',NULL,'2026-06-07 14:22:50');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('182','projects','view','View projects',NULL,'2026-06-07 14:29:48');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('183','projects','create','Create projects',NULL,'2026-06-07 14:29:48');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('184','projects','edit','Edit projects',NULL,'2026-06-07 14:29:48');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('185','projects','delete','Delete projects',NULL,'2026-06-07 14:29:48');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('186','project_tasks','view','View project tasks',NULL,'2026-06-07 14:29:48');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('187','project_tasks','create','Create project tasks',NULL,'2026-06-07 14:29:48');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('188','project_tasks','edit','Edit project tasks',NULL,'2026-06-07 14:29:48');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('189','project_tasks','comment','Comment on tasks',NULL,'2026-06-07 14:29:48');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('190','timesheets','view','View timesheets',NULL,'2026-06-07 14:39:13');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('191','timesheets','review','Review timesheets',NULL,'2026-06-07 14:39:13');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('192','timesheets','export','Export timesheets',NULL,'2026-06-07 14:39:13');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('193','timesheets','report','View timesheet reports',NULL,'2026-06-07 14:39:13');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('194','assets','view','View assets',NULL,'2026-06-07 14:54:02');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('195','assets','create','Create assets',NULL,'2026-06-07 14:54:02');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('196','assets','edit','Edit assets',NULL,'2026-06-07 14:54:02');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('197','assets','allocate','Allocate assets',NULL,'2026-06-07 14:54:02');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('198','assets','return','Return assets',NULL,'2026-06-07 14:54:02');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('199','assets','export','Export asset reports',NULL,'2026-06-07 14:54:02');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('200','access_management','view','View access register',NULL,'2026-06-07 15:15:07');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('201','access_management','create','Create access accounts',NULL,'2026-06-07 15:15:07');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('202','access_management','edit','Edit access accounts',NULL,'2026-06-07 15:15:07');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('203','access_management','assign','Assign access',NULL,'2026-06-07 15:15:07');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('204','access_management','revoke','Revoke access',NULL,'2026-06-07 15:15:07');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('205','access_management','export','Export access report',NULL,'2026-06-07 15:15:07');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('206','subscriptions','view','View subscriptions',NULL,'2026-06-07 15:32:34');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('207','subscriptions','create','Create subscriptions',NULL,'2026-06-07 15:32:34');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('208','subscriptions','edit','Edit subscriptions',NULL,'2026-06-07 15:32:34');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('209','subscriptions','reveal','Reveal encrypted credentials',NULL,'2026-06-07 15:32:34');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('210','finance','view','View finance',NULL,'2026-06-07 15:32:34');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('211','finance','create','Create finance records',NULL,'2026-06-07 15:32:34');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('212','finance','edit','Edit finance records',NULL,'2026-06-07 15:32:34');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('213','finance','reports','View financial reports',NULL,'2026-06-07 15:32:34');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('214','gst','view','View GST reports',NULL,'2026-06-07 15:32:34');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('215','dashboards','view','View analytics dashboards',NULL,'2026-06-07 15:32:34');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('216','employee_exit','view','View employee exits',NULL,'2026-06-07 15:32:34');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('217','employee_exit','create','Initiate employee exit',NULL,'2026-06-07 15:32:34');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('218','employee_exit','edit','Manage exit clearance',NULL,'2026-06-07 15:32:34');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('219','employee_exit','settle','Complete final settlement',NULL,'2026-06-07 15:32:34');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('220','backups','view','View database backups',NULL,'2026-06-07 15:52:43');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('221','backups','create','Create database backups',NULL,'2026-06-07 15:52:43');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('222','bank_reconciliation','view','View reconciliation',NULL,'2026-06-07 15:52:43');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('223','bank_reconciliation','create','Create reconciliation',NULL,'2026-06-07 15:52:43');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('224','project_sprints','view','View sprints',NULL,'2026-06-07 15:52:43');
INSERT INTO `permissions` (`id`,`module_key`,`action_key`,`label`,`description`,`created_at`) VALUES('225','project_sprints','manage','Manage sprints',NULL,'2026-06-07 15:52:43');

DROP TABLE IF EXISTS `professionaltaxtbl`;
CREATE TABLE `professionaltaxtbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `startingAmount` int(11) NOT NULL,
  `endingAmount` int(11) NOT NULL,
  `professionalTax` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
INSERT INTO `professionaltaxtbl` (`id`,`startingAmount`,`endingAmount`,`professionalTax`) VALUES('1','0','5999','0');
INSERT INTO `professionaltaxtbl` (`id`,`startingAmount`,`endingAmount`,`professionalTax`) VALUES('2','6000','8999','80');
INSERT INTO `professionaltaxtbl` (`id`,`startingAmount`,`endingAmount`,`professionalTax`) VALUES('3','9000','11999','150');
INSERT INTO `professionaltaxtbl` (`id`,`startingAmount`,`endingAmount`,`professionalTax`) VALUES('10','12000','15000','200');
INSERT INTO `professionaltaxtbl` (`id`,`startingAmount`,`endingAmount`,`professionalTax`) VALUES('11','15000','50000','200');

DROP TABLE IF EXISTS `project_expenses`;
CREATE TABLE `project_expenses` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `expensedate` date NOT NULL,
  `project_Id` int(11) NOT NULL,
  `expense_title` varchar(255) NOT NULL,
  `amount` varchar(255) NOT NULL,
  `account_Id` bigint(20) NOT NULL DEFAULT 0,
  `remark` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

DROP TABLE IF EXISTS `project_milestones`;
CREATE TABLE `project_milestones` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `title` varchar(180) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_milestone_project` (`project_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `project_sprints`;
CREATE TABLE `project_sprints` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `goal` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('planned','active','completed','cancelled') NOT NULL DEFAULT 'planned',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_sprint` (`project_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `project_team_members`;
CREATE TABLE `project_team_members` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `role_name` varchar(100) DEFAULT NULL,
  `allocation_percent` decimal(5,2) NOT NULL DEFAULT 100.00,
  `joined_at` date NOT NULL,
  `left_at` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_project_employee` (`project_id`,`employee_id`),
  KEY `idx_team_employee` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `projectplatform`;
CREATE TABLE `projectplatform` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `projectplatform` (`id`,`name`) VALUES('1','Upwork');
INSERT INTO `projectplatform` (`id`,`name`) VALUES('2','Fiverr');
INSERT INTO `projectplatform` (`id`,`name`) VALUES('3','References');
INSERT INTO `projectplatform` (`id`,`name`) VALUES('4','Direct');

DROP TABLE IF EXISTS `projectstbl`;
CREATE TABLE `projectstbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL DEFAULT 0,
  `client_id` bigint(20) DEFAULT NULL,
  `projectName` varchar(255) NOT NULL,
  `developerId` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `priority` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `progress_percent` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `attachment` varchar(255) NOT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  `amount` varchar(255) NOT NULL,
  `budget_hours` decimal(10,2) NOT NULL DEFAULT 0.00,
  `expence` varchar(255) NOT NULL,
  `customerName` varchar(255) NOT NULL,
  `nickName` varchar(215) NOT NULL,
  `platform` varchar(250) NOT NULL,
  `projectType` varchar(250) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `country` varchar(50) NOT NULL,
  `reference` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

DROP TABLE IF EXISTS `projecttype`;
CREATE TABLE `projecttype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `projecttype` (`id`,`name`) VALUES('1','Ui UX Development');
INSERT INTO `projecttype` (`id`,`name`) VALUES('2','CRM Development');
INSERT INTO `projecttype` (`id`,`name`) VALUES('3','Laravel Development');
INSERT INTO `projecttype` (`id`,`name`) VALUES('4','Codeigniter Development');
INSERT INTO `projecttype` (`id`,`name`) VALUES('5','Mobile App Development');
INSERT INTO `projecttype` (`id`,`name`) VALUES('6','Wordpress Development');
INSERT INTO `projecttype` (`id`,`name`) VALUES('7','Magento Development');
INSERT INTO `projecttype` (`id`,`name`) VALUES('8','Shopify Development');
INSERT INTO `projecttype` (`id`,`name`) VALUES('9','SEO Development');
INSERT INTO `projecttype` (`id`,`name`) VALUES('10','Webflow Development');
INSERT INTO `projecttype` (`id`,`name`) VALUES('11','React Node Development');
INSERT INTO `projecttype` (`id`,`name`) VALUES('12','Salesforce Development');
INSERT INTO `projecttype` (`id`,`name`) VALUES('13','QA Development');

DROP TABLE IF EXISTS `resource_allocations`;
CREATE TABLE `resource_allocations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `client_id` bigint(20) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `allocation_type` enum('full_time','part_time','shared','hourly') NOT NULL DEFAULT 'full_time',
  `allocation_percent` decimal(5,2) NOT NULL DEFAULT 100.00,
  `billing_rate` decimal(14,2) NOT NULL DEFAULT 0.00,
  `billing_cycle` enum('monthly','hourly','fixed') NOT NULL DEFAULT 'monthly',
  `salary_cost` decimal(14,2) NOT NULL DEFAULT 0.00,
  `currency_code` char(3) NOT NULL DEFAULT 'INR',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('planned','active','completed','cancelled') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_resource_company_status` (`company_id`,`status`),
  KEY `idx_resource_employee` (`employee_id`,`start_date`,`end_date`),
  KEY `idx_resource_client` (`client_id`),
  KEY `idx_resource_project` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `retention_ledger`;
CREATE TABLE `retention_ledger` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `payroll_item_id` bigint(20) DEFAULT NULL,
  `transaction_type` enum('hold','release','adjustment') NOT NULL,
  `amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `balance_after` decimal(14,2) NOT NULL DEFAULT 0.00,
  `notes` varchar(500) DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_retention_employee` (`employee_id`,`created_at`),
  KEY `fk_retention_payroll_item` (`payroll_item_id`),
  CONSTRAINT `fk_retention_employee` FOREIGN KEY (`employee_id`) REFERENCES `employeestbl` (`id`),
  CONSTRAINT `fk_retention_payroll_item` FOREIGN KEY (`payroll_item_id`) REFERENCES `payroll_items` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 0,
  `allowed` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_role_permission_company` (`role_id`,`permission_id`,`company_id`),
  KEY `idx_role_permissions_role` (`role_id`),
  KEY `idx_role_permissions_permission` (`permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `role_permissions` (`id`,`role_id`,`permission_id`,`company_id`,`allowed`,`created_at`,`updated_at`) VALUES('1','2','176','0','1','2026-06-07 14:15:04','2026-06-07 14:15:04');
INSERT INTO `role_permissions` (`id`,`role_id`,`permission_id`,`company_id`,`allowed`,`created_at`,`updated_at`) VALUES('2','2','170','0','1','2026-06-07 14:15:04','2026-06-07 14:15:04');
INSERT INTO `role_permissions` (`id`,`role_id`,`permission_id`,`company_id`,`allowed`,`created_at`,`updated_at`) VALUES('3','2','171','0','1','2026-06-07 14:15:04','2026-06-07 14:15:04');
INSERT INTO `role_permissions` (`id`,`role_id`,`permission_id`,`company_id`,`allowed`,`created_at`,`updated_at`) VALUES('4','2','166','0','1','2026-06-07 14:15:04','2026-06-07 14:15:04');
INSERT INTO `role_permissions` (`id`,`role_id`,`permission_id`,`company_id`,`allowed`,`created_at`,`updated_at`) VALUES('5','2','167','0','1','2026-06-07 14:15:04','2026-06-07 14:15:04');
INSERT INTO `role_permissions` (`id`,`role_id`,`permission_id`,`company_id`,`allowed`,`created_at`,`updated_at`) VALUES('6','2','169','0','1','2026-06-07 14:15:04','2026-06-07 14:15:04');
INSERT INTO `role_permissions` (`id`,`role_id`,`permission_id`,`company_id`,`allowed`,`created_at`,`updated_at`) VALUES('7','2','165','0','1','2026-06-07 14:15:04','2026-06-07 14:15:04');
INSERT INTO `role_permissions` (`id`,`role_id`,`permission_id`,`company_id`,`allowed`,`created_at`,`updated_at`) VALUES('8','2','178','0','1','2026-06-07 14:22:50','2026-06-07 14:22:50');
INSERT INTO `role_permissions` (`id`,`role_id`,`permission_id`,`company_id`,`allowed`,`created_at`,`updated_at`) VALUES('9','2','179','0','1','2026-06-07 14:22:50','2026-06-07 14:22:50');
INSERT INTO `role_permissions` (`id`,`role_id`,`permission_id`,`company_id`,`allowed`,`created_at`,`updated_at`) VALUES('10','2','181','0','1','2026-06-07 14:22:50','2026-06-07 14:22:50');
INSERT INTO `role_permissions` (`id`,`role_id`,`permission_id`,`company_id`,`allowed`,`created_at`,`updated_at`) VALUES('11','2','177','0','1','2026-06-07 14:22:50','2026-06-07 14:22:50');
INSERT INTO `role_permissions` (`id`,`role_id`,`permission_id`,`company_id`,`allowed`,`created_at`,`updated_at`) VALUES('15','2','192','0','1','2026-06-07 14:39:13','2026-06-07 14:39:13');
INSERT INTO `role_permissions` (`id`,`role_id`,`permission_id`,`company_id`,`allowed`,`created_at`,`updated_at`) VALUES('16','2','193','0','1','2026-06-07 14:39:13','2026-06-07 14:39:13');
INSERT INTO `role_permissions` (`id`,`role_id`,`permission_id`,`company_id`,`allowed`,`created_at`,`updated_at`) VALUES('17','2','190','0','1','2026-06-07 14:39:13','2026-06-07 14:39:13');

DROP TABLE IF EXISTS `salary_structures`;
CREATE TABLE `salary_structures` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `gross_salary` decimal(14,2) NOT NULL DEFAULT 0.00,
  `basic_salary` decimal(14,2) NOT NULL DEFAULT 0.00,
  `hra` decimal(14,2) NOT NULL DEFAULT 0.00,
  `special_allowance` decimal(14,2) NOT NULL DEFAULT 0.00,
  `other_allowance` decimal(14,2) NOT NULL DEFAULT 0.00,
  `professional_tax_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `retention_type` enum('none','fixed','percent') NOT NULL DEFAULT 'none',
  `retention_value` decimal(14,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_salary_structure_employee` (`employee_id`,`effective_from`,`effective_to`),
  KEY `fk_salary_structure_company` (`company_id`),
  CONSTRAINT `fk_salary_structure_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `fk_salary_structure_employee` FOREIGN KEY (`employee_id`) REFERENCES `employeestbl` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('1','1','1','2000-01-01',NULL,'35000.00','17500.00','7000.00','10500.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('2','2','1','2019-09-01',NULL,'35000.00','17500.00','7000.00','10500.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('3','3','1','2000-01-01',NULL,'31000.00','15500.00','6200.00','9300.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('4','4','1','2018-05-31',NULL,'24000.00','12000.00','4800.00','7200.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('5','6','1','2018-02-08',NULL,'0.00','0.00','0.00','0.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('6','7','1','2018-07-09',NULL,'14000.00','7000.00','2800.00','4200.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('7','8','1','2019-05-01',NULL,'10000.00','5000.00','2000.00','3000.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('8','9','1','2018-12-03',NULL,'18000.00','9000.00','3600.00','5400.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('9','10','1','2019-01-04',NULL,'8500.00','4250.00','1700.00','2550.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('10','11','1','2019-03-18',NULL,'10000.00','5000.00','2000.00','3000.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('11','12','1','2019-03-22',NULL,'20000.00','10000.00','4000.00','6000.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('12','13','1','2019-04-01',NULL,'17500.00','8750.00','3500.00','5250.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('13','14','1','2019-05-21',NULL,'2500.00','1250.00','500.00','750.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('14','15','1','2019-09-02',NULL,'25000.00','12500.00','5000.00','7500.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('15','17','1','2022-06-01',NULL,'12000.00','6000.00','2400.00','3600.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('16','18','1','2000-01-01',NULL,'0.00','0.00','0.00','0.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('17','19','1','2021-08-02',NULL,'8000.00','4000.00','1600.00','2400.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('18','20','1','2021-11-16',NULL,'27000.00','13500.00','5400.00','8100.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('19','21','1','2022-02-23',NULL,'0.00','0.00','0.00','0.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('20','22','1','2022-09-21',NULL,'10000.00','5000.00','2000.00','3000.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('21','23','1','2022-09-26',NULL,'30000.00','15000.00','6000.00','9000.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('22','24','1','2022-09-26',NULL,'0.00','0.00','0.00','0.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('23','25','1','2022-10-10',NULL,'45000.00','22500.00','9000.00','13500.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('24','26','1','2018-03-01',NULL,'25000.00','12500.00','5000.00','7500.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('25','27','1','2022-12-01',NULL,'8000.00','4000.00','1600.00','2400.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('26','28','1','2023-01-05',NULL,'0.00','0.00','0.00','0.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('27','30','1','2023-01-09',NULL,'12000.00','6000.00','2400.00','3600.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('28','31','1','2000-01-01',NULL,'33500.00','16750.00','6700.00','10050.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('29','32','1','2023-07-04',NULL,'0.00','0.00','0.00','0.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('30','33','1','2000-01-01',NULL,'75000.00','37500.00','15000.00','22500.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('31','34','1','2023-08-28',NULL,'20000.00','10000.00','4000.00','6000.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('32','35','1','2023-10-16',NULL,'0.00','0.00','0.00','0.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('33','36','1','2023-11-20',NULL,'0.00','0.00','0.00','0.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('34','37','1','2023-12-26',NULL,'0.00','0.00','0.00','0.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('35','38','1','2024-06-10',NULL,'0.00','0.00','0.00','0.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('36','39','1','2024-06-10',NULL,'0.00','0.00','0.00','0.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('37','40','1','2024-06-10',NULL,'46000.00','23000.00','9200.00','13800.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('38','41','1','2024-06-20',NULL,'0.00','0.00','0.00','0.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('39','42','1','2024-10-01',NULL,'10000.00','5000.00','2000.00','3000.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('40','44','1','2025-03-17',NULL,'22000.00','11000.00','4400.00','6600.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('41','45','1','2024-10-14',NULL,'3000.00','1500.00','600.00','900.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('42','46','1','2024-10-14',NULL,'3000.00','1500.00','600.00','900.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('43','47','1','2000-01-01',NULL,'11000.00','5500.00','2200.00','3300.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('44','48','1','2025-05-01',NULL,'65000.00','32500.00','13000.00','19500.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('45','49','1','2025-06-09',NULL,'45000.00','22500.00','9000.00','13500.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('46','50','1','2000-01-01',NULL,'0.00','0.00','0.00','0.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('47','51','1','2026-03-09',NULL,'0.00','0.00','0.00','0.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');
INSERT INTO `salary_structures` (`id`,`employee_id`,`company_id`,`effective_from`,`effective_to`,`gross_salary`,`basic_salary`,`hra`,`special_allowance`,`other_allowance`,`professional_tax_enabled`,`retention_type`,`retention_value`,`status`,`created_by`,`created_at`,`updated_at`) VALUES('48','52','1','2000-01-01',NULL,'10000.00','5000.00','2000.00','3000.00','0.00','1','none','0.00','1','0','2026-06-06 21:58:51','2026-06-06 21:58:51');

DROP TABLE IF EXISTS `salaryreporttbl`;
CREATE TABLE `salaryreporttbl` (
  `salaryReportId` int(11) NOT NULL AUTO_INCREMENT,
  `payment_date` varchar(255) NOT NULL,
  `salary_month` varchar(255) NOT NULL,
  `salary_year` varchar(255) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `emp_name` varchar(255) NOT NULL,
  `employeeCode` varchar(255) NOT NULL,
  `employeeDesignation` varchar(255) NOT NULL,
  `emp_salary` varchar(255) NOT NULL,
  `professional_tax` varchar(255) NOT NULL,
  `totalLeaves` int(11) NOT NULL,
  `totalDeductAmount` varchar(255) NOT NULL,
  `netPayebaleAmount` varchar(255) NOT NULL,
  `bankName` varchar(255) NOT NULL,
  `bankIFSCno` varchar(255) NOT NULL,
  `bankAcHolderName` varchar(255) NOT NULL,
  `bankAcNo` varchar(255) NOT NULL,
  `total_days` varchar(255) DEFAULT NULL,
  `total_days_work` varchar(255) DEFAULT NULL,
  `total_hours` varchar(255) DEFAULT NULL,
  `total_hours_work` varchar(255) DEFAULT NULL,
  `avgHours` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`salaryReportId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

DROP TABLE IF EXISTS `shift_weekly_off_rules`;
CREATE TABLE `shift_weekly_off_rules` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `shift_id` int(11) NOT NULL,
  `weekday` tinyint(4) NOT NULL,
  `week_of_month` tinyint(4) NOT NULL DEFAULT 0,
  `is_off` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_shift_weekday_week` (`shift_id`,`weekday`,`week_of_month`),
  CONSTRAINT `fk_shift_weekly_rule` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `shift_weekly_off_rules` (`id`,`shift_id`,`weekday`,`week_of_month`,`is_off`,`created_at`) VALUES('1','1','0','0','1','2026-06-06 21:27:06');
INSERT INTO `shift_weekly_off_rules` (`id`,`shift_id`,`weekday`,`week_of_month`,`is_off`,`created_at`) VALUES('2','1','6','1','1','2026-06-06 21:27:06');
INSERT INTO `shift_weekly_off_rules` (`id`,`shift_id`,`weekday`,`week_of_month`,`is_off`,`created_at`) VALUES('3','1','6','3','1','2026-06-06 21:27:06');
INSERT INTO `shift_weekly_off_rules` (`id`,`shift_id`,`weekday`,`week_of_month`,`is_off`,`created_at`) VALUES('4','1','6','5','1','2026-06-06 21:27:06');

DROP TABLE IF EXISTS `shifts`;
CREATE TABLE `shifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `code` varchar(30) NOT NULL,
  `shift_type` enum('general','night','us','flexible','custom') NOT NULL DEFAULT 'general',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `crosses_midnight` tinyint(1) NOT NULL DEFAULT 0,
  `required_minutes` int(11) NOT NULL DEFAULT 510,
  `break_minutes` int(11) NOT NULL DEFAULT 60,
  `grace_in_minutes` int(11) NOT NULL DEFAULT 0,
  `grace_out_minutes` int(11) NOT NULL DEFAULT 0,
  `earliest_checkin_minutes` int(11) NOT NULL DEFAULT 120,
  `latest_checkout_minutes` int(11) NOT NULL DEFAULT 240,
  `flexible_start_from` time DEFAULT NULL,
  `flexible_start_to` time DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_shift_company_code` (`company_id`,`code`),
  KEY `idx_shifts_company` (`company_id`),
  CONSTRAINT `fk_shifts_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `shifts` (`id`,`company_id`,`name`,`code`,`shift_type`,`start_time`,`end_time`,`crosses_midnight`,`required_minutes`,`break_minutes`,`grace_in_minutes`,`grace_out_minutes`,`earliest_checkin_minutes`,`latest_checkout_minutes`,`flexible_start_from`,`flexible_start_to`,`status`,`created_at`,`updated_at`) VALUES('1','1','General Shift','GENERAL','general','10:00:00','19:30:00','0','510','60','30','15','120','240',NULL,NULL,'1','2026-06-06 21:27:06','2026-06-06 21:27:06');

DROP TABLE IF EXISTS `task_attachments`;
CREATE TABLE `task_attachments` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `stored_name` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `file_size` bigint(20) NOT NULL DEFAULT 0,
  `uploaded_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_task_attachment` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `task_comments`;
CREATE TABLE `task_comments` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_by_type` enum('admin','employee') NOT NULL DEFAULT 'admin',
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_task_comments` (`task_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `taskhourstbl`;
CREATE TABLE `taskhourstbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `taskId` int(11) NOT NULL,
  `projectId` int(11) DEFAULT NULL,
  `developerId` int(11) NOT NULL,
  `startTaskDate` varchar(255) NOT NULL,
  `startTaskTime` varchar(255) NOT NULL,
  `endTaskTime` varchar(255) NOT NULL DEFAULT '',
  `worklog` text NOT NULL,
  `totalTime` varchar(255) NOT NULL,
  `adminResponse` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

DROP TABLE IF EXISTS `tasktbl`;
CREATE TABLE `tasktbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL DEFAULT 0,
  `projectId` varchar(255) NOT NULL,
  `sprint_id` bigint(20) DEFAULT NULL,
  `developerId` varchar(255) NOT NULL,
  `assignDate` varchar(255) NOT NULL,
  `expectedDate` varchar(255) NOT NULL,
  `taskTitle` text NOT NULL,
  `task_details` text NOT NULL,
  `priority` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `estimated_hours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `files` varchar(255) NOT NULL,
  `filePath` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `board_status` enum('backlog','todo','in_progress','review','done') NOT NULL DEFAULT 'backlog',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_time` varchar(20) NOT NULL,
  `totalHour` varchar(255) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

DROP TABLE IF EXISTS `timesheet_entries`;
CREATE TABLE `timesheet_entries` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `work_date` date NOT NULL,
  `description` text NOT NULL,
  `hours` decimal(5,2) NOT NULL,
  `billable_hours` decimal(5,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','submitted','approved','rejected') NOT NULL DEFAULT 'draft',
  `rejection_reason` varchar(500) DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_timesheet_company_date` (`company_id`,`work_date`),
  KEY `idx_timesheet_employee` (`employee_id`,`work_date`),
  KEY `idx_timesheet_project` (`project_id`,`work_date`),
  KEY `idx_timesheet_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `user_access`;
CREATE TABLE `user_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utype` varchar(100) NOT NULL,
  `mname` varchar(100) NOT NULL,
  `mtitle` varchar(100) DEFAULT NULL,
  `mid` int(11) NOT NULL DEFAULT 0,
  `is_access` varchar(100) NOT NULL DEFAULT '0',
  `org_id` bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('1','1','dashboard','Dashboard','1','1','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('2','1','employee','Employee','2','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('3','1','project','Project','4','1','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('4','1','role','Role','5','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('5','1','lead','Lead','6','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('6','1','invoice','Invoice','7','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('7','1','task','Task','8','1','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('8','1','domain_hosting','Domain Hosting','9','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('9','1','salary','Salary','10','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('10','1','leave','Leave','11','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('11','1','bank_account','Bank Account','12','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('12','1','deposit','Deposit','13','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('13','1','expense','Expense','14','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('14','1','project_expense','Project Expense','15','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('15','1','settings','Settings','16','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('16','1','add_employee','Add Employee','17','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('17','1','update_employee','Update Employee','18','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('18','1','view_employee','View Employee','19','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('19','1','delete_employee','Delete Employee','20','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('20','1','add_project','Add Project','21','1','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('21','1','update_project','Update Project','22','1','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('22','1','view_project','View Project','23','1','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('23','1','delete_project','Delete Project','24','1','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('24','1','add_role','Add Role','25','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('25','1','update_role','Update Role','26','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('26','1','view_role','View Role','27','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('27','1','delete_role','Delete Role','28','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('28','1','add_lead','Add Lead','29','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('29','1','update_lead','Update Lead','30','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('30','1','view_lead','View Lead','31','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('31','1','delete_lead','Delete Lead','32','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('32','1','add_invoice','Add Invoice','33','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('33','1','update_invoice','Update Invoice','34','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('34','1','view_invoice','View Invoice','35','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('35','1','delete_invoice','Delete Invoice','36','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('36','1','add_task','Add Task','37','1','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('37','1','update_task','Update Task','38','1','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('38','1','view_task','View Task','39','1','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('39','1','delete_task','Delete Task','40','1','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('40','1','add_domain_hosting','Add Domain Hosting','41','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('41','1','update_domain_hosting','Update Domain Hosting','42','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('42','1','view_domain_hosting','View Domain Hosting','43','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('43','1','delete_domain_hosting','Delete Domain Hosting','44','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('44','1','add_salary','Add Salary','45','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('45','1','update_salary','Update Salary','46','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('46','1','view_salary','View Salary','47','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('47','1','delete_salary','Delete Salary','48','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('48','1','add_leave','Add Leave','49','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('49','1','update_leave','Update Leave','50','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('50','1','view_leave','View Leave','51','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('51','1','delete_leave','Delete Leave','52','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('52','1','add_bank_account','Add Bank Account','53','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('53','1','update_bank_account','Update Bank Account','54','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('54','1','view_bank_account','View Bank Account','55','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('55','1','delete_bank_account','Delete Bank Account','56','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('56','1','add_deposit','Add Deposit','57','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('57','1','update_deposit','Update Deposit','58','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('58','1','view_deposit','View Deposit','59','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('59','1','delete_deposit','Delete Deposit','60','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('60','1','add_expense','Add Expense','61','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('61','1','update_expense','Update Expense','62','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('62','1','view_expense','View Expense','63','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('63','1','delete_expense','Delete Expense','64','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('64','1','add_project_expense','Add Project Expense','65','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('65','1','update_project_expense','Update Project Expense','66','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('66','1','view_project_expense','View Project Expense','67','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('67','1','delete_project_expense','Delete Project Expense','68','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('68','2','dashboard','Dashboard','1','1','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('69','2','employee','Employee','2','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('70','2','project','Project','4','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('71','2','role','Role','5','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('72','2','lead','Lead','6','1','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('73','2','invoice','Invoice','7','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('74','2','task','Task','8','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('75','2','domain_hosting','Domain Hosting','9','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('76','2','salary','Salary','10','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('77','2','leave','Leave','11','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('78','2','bank_account','Bank Account','12','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('79','2','deposit','Deposit','13','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('80','2','expense','Expense','14','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('81','2','project_expense','Project Expense','15','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('82','2','settings','Settings','16','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('83','2','add_employee','Add Employee','17','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('84','2','update_employee','Update Employee','18','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('85','2','view_employee','View Employee','19','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('86','2','delete_employee','Delete Employee','20','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('87','2','add_project','Add Project','21','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('88','2','update_project','Update Project','22','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('89','2','view_project','View Project','23','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('90','2','delete_project','Delete Project','24','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('91','2','add_role','Add Role','25','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('92','2','update_role','Update Role','26','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('93','2','view_role','View Role','27','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('94','2','delete_role','Delete Role','28','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('95','2','add_lead','Add Lead','29','1','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('96','2','update_lead','Update Lead','30','1','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('97','2','view_lead','View Lead','31','1','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('98','2','delete_lead','Delete Lead','32','1','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('99','2','add_invoice','Add Invoice','33','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('100','2','update_invoice','Update Invoice','34','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('101','2','view_invoice','View Invoice','35','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('102','2','delete_invoice','Delete Invoice','36','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('103','2','add_task','Add Task','37','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('104','2','update_task','Update Task','38','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('105','2','view_task','View Task','39','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('106','2','delete_task','Delete Task','40','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('107','2','add_domain_hosting','Add Domain Hosting','41','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('108','2','update_domain_hosting','Update Domain Hosting','42','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('109','2','view_domain_hosting','View Domain Hosting','43','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('110','2','delete_domain_hosting','Delete Domain Hosting','44','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('111','2','add_salary','Add Salary','45','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('112','2','update_salary','Update Salary','46','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('113','2','view_salary','View Salary','47','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('114','2','delete_salary','Delete Salary','48','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('115','2','add_leave','Add Leave','49','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('116','2','update_leave','Update Leave','50','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('117','2','view_leave','View Leave','51','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('118','2','delete_leave','Delete Leave','52','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('119','2','add_bank_account','Add Bank Account','53','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('120','2','update_bank_account','Update Bank Account','54','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('121','2','view_bank_account','View Bank Account','55','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('122','2','delete_bank_account','Delete Bank Account','56','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('123','2','add_deposit','Add Deposit','57','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('124','2','update_deposit','Update Deposit','58','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('125','2','view_deposit','View Deposit','59','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('126','2','delete_deposit','Delete Deposit','60','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('127','2','add_expense','Add Expense','61','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('128','2','update_expense','Update Expense','62','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('129','2','view_expense','View Expense','63','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('130','2','delete_expense','Delete Expense','64','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('131','2','add_project_expense','Add Project Expense','65','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('132','2','update_project_expense','Update Project Expense','66','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('133','2','view_project_expense','View Project Expense','67','0','0');
INSERT INTO `user_access` (`id`,`utype`,`mname`,`mtitle`,`mid`,`is_access`,`org_id`) VALUES('134','2','delete_project_expense','Delete Project Expense','68','0','0');

DROP TABLE IF EXISTS `user_type`;
CREATE TABLE `user_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `org_id` int(11) NOT NULL DEFAULT 0,
  `is_deleted` int(11) NOT NULL DEFAULT 0,
  `created_by` bigint(20) NOT NULL,
  `updated_by` bigint(20) NOT NULL,
  `created_at` varchar(25) NOT NULL,
  `updated_at` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
INSERT INTO `user_type` (`id`,`name`,`org_id`,`is_deleted`,`created_by`,`updated_by`,`created_at`,`updated_at`) VALUES('1','User','0','0','0','0','','');
INSERT INTO `user_type` (`id`,`name`,`org_id`,`is_deleted`,`created_by`,`updated_by`,`created_at`,`updated_at`) VALUES('2','Sales','0','0','0','0','','');

DROP TABLE IF EXISTS `workhourstbl`;
CREATE TABLE `workhourstbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employeeId` int(11) NOT NULL,
  `workDate` varchar(255) NOT NULL,
  `month` varchar(255) NOT NULL,
  `year` varchar(255) NOT NULL,
  `signinTime` varchar(255) NOT NULL,
  `lunchinTime` varchar(255) NOT NULL,
  `lunchoutTime` varchar(255) NOT NULL,
  `breakinTime` varchar(255) NOT NULL,
  `breakoutTime` varchar(255) NOT NULL,
  `signoutTime` varchar(255) NOT NULL,
  `workHours` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
SET FOREIGN_KEY_CHECKS=1;
