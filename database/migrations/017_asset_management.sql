CREATE TABLE IF NOT EXISTS assets (
 id BIGINT AUTO_INCREMENT PRIMARY KEY, company_id INT NOT NULL, asset_code VARCHAR(40) NOT NULL,
 asset_type ENUM('laptop','desktop','monitor','mouse','keyboard','headphones','mobile','other') NOT NULL,
 brand VARCHAR(100) DEFAULT NULL, model VARCHAR(120) DEFAULT NULL, serial_number VARCHAR(150) DEFAULT NULL,
 purchase_date DATE DEFAULT NULL, purchase_cost DECIMAL(14,2) NOT NULL DEFAULT 0, warranty_until DATE DEFAULT NULL,
 condition_status ENUM('new','good','fair','damaged') NOT NULL DEFAULT 'good',
 lifecycle_status ENUM('available','allocated','repair','retired','lost') NOT NULL DEFAULT 'available',
 notes TEXT DEFAULT NULL, created_by INT NOT NULL DEFAULT 0,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 UNIQUE KEY uq_asset_code(company_id,asset_code), KEY idx_asset_status(company_id,lifecycle_status)
);
CREATE TABLE IF NOT EXISTS asset_allocations (
 id BIGINT AUTO_INCREMENT PRIMARY KEY, company_id INT NOT NULL, asset_id BIGINT NOT NULL, employee_id INT NOT NULL,
 allocated_on DATE NOT NULL, expected_return_date DATE DEFAULT NULL, returned_on DATE DEFAULT NULL,
 issue_condition ENUM('new','good','fair','damaged') NOT NULL DEFAULT 'good',
 return_condition ENUM('new','good','fair','damaged') DEFAULT NULL,
 status ENUM('allocated','returned') NOT NULL DEFAULT 'allocated', remarks TEXT DEFAULT NULL,
 allocated_by INT NOT NULL DEFAULT 0, returned_by INT DEFAULT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 KEY idx_allocation_asset(asset_id,status), KEY idx_allocation_employee(employee_id,status)
);
CREATE TABLE IF NOT EXISTS asset_history (
 id BIGINT AUTO_INCREMENT PRIMARY KEY, company_id INT NOT NULL, asset_id BIGINT NOT NULL,
 event_type ENUM('created','updated','allocated','returned','repair','retired','lost') NOT NULL,
 employee_id INT DEFAULT NULL, details TEXT DEFAULT NULL, performed_by INT NOT NULL DEFAULT 0,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, KEY idx_asset_history(asset_id,created_at)
);
INSERT IGNORE INTO permissions(module_key,action_key,label) VALUES
('assets','view','View assets'),('assets','create','Create assets'),('assets','edit','Edit assets'),
('assets','allocate','Allocate assets'),('assets','return','Return assets'),('assets','export','Export asset reports');
