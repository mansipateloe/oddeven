CREATE TABLE IF NOT EXISTS companies (
  id INT NOT NULL AUTO_INCREMENT,
  code VARCHAR(30) NOT NULL,
  legal_name VARCHAR(180) NOT NULL,
  display_name VARCHAR(180) NOT NULL,
  company_type ENUM('india_gst','india_non_gst','usa','other') NOT NULL DEFAULT 'other',
  country_code CHAR(2) NOT NULL DEFAULT 'IN',
  currency_code CHAR(3) NOT NULL DEFAULT 'INR',
  gstin VARCHAR(30) DEFAULT NULL,
  tax_id VARCHAR(50) DEFAULT NULL,
  email VARCHAR(180) DEFAULT NULL,
  phone VARCHAR(40) DEFAULT NULL,
  address TEXT DEFAULT NULL,
  logo_path VARCHAR(255) DEFAULT NULL,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_companies_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO companies (code, legal_name, display_name, company_type, country_code, currency_code, status)
SELECT 'OE-IN-GST', 'Oddeven Infotech Private Limited', 'Oddeven Infotech', 'india_gst', 'IN', 'INR', 1
WHERE NOT EXISTS (SELECT 1 FROM companies);

CREATE TABLE IF NOT EXISTS departments (
  id INT NOT NULL AUTO_INCREMENT,
  company_id INT NOT NULL,
  name VARCHAR(120) NOT NULL,
  code VARCHAR(30) DEFAULT NULL,
  manager_employee_id INT DEFAULT NULL,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_department_company_name (company_id, name),
  KEY idx_departments_company (company_id),
  CONSTRAINT fk_departments_company FOREIGN KEY (company_id) REFERENCES companies(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS permissions (
  id INT NOT NULL AUTO_INCREMENT,
  module_key VARCHAR(80) NOT NULL,
  action_key VARCHAR(40) NOT NULL,
  label VARCHAR(150) NOT NULL,
  description VARCHAR(255) DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_permission_module_action (module_key, action_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS role_permissions (
  id BIGINT NOT NULL AUTO_INCREMENT,
  role_id INT NOT NULL,
  permission_id INT NOT NULL,
  company_id INT NOT NULL DEFAULT 0,
  allowed TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_role_permission_company (role_id, permission_id, company_id),
  KEY idx_role_permissions_role (role_id),
  KEY idx_role_permissions_permission (permission_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_logs (
  id BIGINT NOT NULL AUTO_INCREMENT,
  company_id INT NOT NULL DEFAULT 0,
  actor_type ENUM('admin','employee','system') NOT NULL DEFAULT 'system',
  actor_id INT NOT NULL DEFAULT 0,
  action VARCHAR(60) NOT NULL,
  module_key VARCHAR(80) NOT NULL,
  entity_type VARCHAR(100) DEFAULT NULL,
  entity_id VARCHAR(100) DEFAULT NULL,
  description VARCHAR(255) DEFAULT NULL,
  old_values LONGTEXT DEFAULT NULL,
  new_values LONGTEXT DEFAULT NULL,
  ip_address VARCHAR(45) DEFAULT NULL,
  user_agent VARCHAR(500) DEFAULT NULL,
  request_method VARCHAR(10) DEFAULT NULL,
  request_uri VARCHAR(500) DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_audit_company_created (company_id, created_at),
  KEY idx_audit_actor (actor_type, actor_id),
  KEY idx_audit_entity (entity_type, entity_id),
  KEY idx_audit_module_action (module_key, action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS app_settings (
  id INT NOT NULL AUTO_INCREMENT,
  company_id INT NOT NULL DEFAULT 0,
  setting_key VARCHAR(120) NOT NULL,
  setting_value LONGTEXT DEFAULT NULL,
  is_encrypted TINYINT(1) NOT NULL DEFAULT 0,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_setting_company_key (company_id, setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @default_company_id := (SELECT id FROM companies ORDER BY id LIMIT 1);

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='employeestbl' AND COLUMN_NAME='company_id')=0,
  'ALTER TABLE employeestbl ADD COLUMN company_id INT NOT NULL DEFAULT 0 AFTER id', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='employeestbl' AND COLUMN_NAME='department_id')=0,
  'ALTER TABLE employeestbl ADD COLUMN department_id INT NOT NULL DEFAULT 0 AFTER company_id', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='admins' AND COLUMN_NAME='company_id')=0,
  'ALTER TABLE admins ADD COLUMN company_id INT NOT NULL DEFAULT 0 AFTER id', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE employeestbl SET company_id=@default_company_id WHERE company_id=0;
UPDATE admins SET company_id=@default_company_id WHERE company_id=0;

INSERT IGNORE INTO permissions (module_key, action_key, label) VALUES
('companies','view','View companies'),('companies','create','Create companies'),('companies','edit','Edit companies'),('companies','delete','Deactivate companies'),
('departments','view','View departments'),('departments','create','Create departments'),('departments','edit','Edit departments'),('departments','delete','Deactivate departments'),
('employees','view','View employees'),('employees','create','Create employees'),('employees','edit','Edit employees'),('employees','delete','Delete employees'),('employees','export','Export employees'),
('roles','view','View roles'),('roles','create','Create roles'),('roles','edit','Edit roles'),('roles','delete','Delete roles'),('roles','manage_permissions','Manage role permissions'),
('audit','view','View audit logs'),('audit','export','Export audit logs'),
('settings','view','View settings'),('settings','edit','Edit settings');