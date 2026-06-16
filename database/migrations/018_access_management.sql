CREATE TABLE IF NOT EXISTS access_accounts (
 id BIGINT AUTO_INCREMENT PRIMARY KEY, company_id INT NOT NULL,
 service_type ENUM('gmail','github','gitlab','aws','digitalocean','figma','chatgpt','claude','hosting','domain','vpn','other') NOT NULL,
 service_name VARCHAR(150) NOT NULL, login_url VARCHAR(500) DEFAULT NULL, account_username VARCHAR(180) DEFAULT NULL,
 owner_email VARCHAR(180) DEFAULT NULL, status ENUM('active','suspended','closed') NOT NULL DEFAULT 'active',
 notes TEXT DEFAULT NULL, created_by INT NOT NULL DEFAULT 0,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 KEY idx_access_company(company_id,status)
);
CREATE TABLE IF NOT EXISTS access_assignments (
 id BIGINT AUTO_INCREMENT PRIMARY KEY, company_id INT NOT NULL, account_id BIGINT NOT NULL, employee_id INT NOT NULL,
 access_level ENUM('viewer','member','admin','owner') NOT NULL DEFAULT 'member',
 assigned_on DATE NOT NULL, expires_on DATE DEFAULT NULL,
 status ENUM('active','revoked') NOT NULL DEFAULT 'active', revoked_on DATE DEFAULT NULL,
 assigned_by INT NOT NULL DEFAULT 0, revoked_by INT DEFAULT NULL, notes VARCHAR(500) DEFAULT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 KEY idx_access_employee(employee_id,status), KEY idx_access_account(account_id,status)
);
CREATE TABLE IF NOT EXISTS access_history (
 id BIGINT AUTO_INCREMENT PRIMARY KEY, company_id INT NOT NULL, account_id BIGINT NOT NULL,
 employee_id INT DEFAULT NULL, event_type ENUM('created','updated','assigned','revoked') NOT NULL,
 details TEXT DEFAULT NULL, performed_by INT NOT NULL DEFAULT 0, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 KEY idx_access_history(account_id,created_at)
);
INSERT IGNORE INTO permissions(module_key,action_key,label) VALUES
('access_management','view','View access register'),('access_management','create','Create access accounts'),
('access_management','edit','Edit access accounts'),('access_management','assign','Assign access'),
('access_management','revoke','Revoke access'),('access_management','export','Export access report');
