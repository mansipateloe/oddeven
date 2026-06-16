CREATE TABLE IF NOT EXISTS leave_policies (
 id INT NOT NULL AUTO_INCREMENT, company_id INT NOT NULL, leave_type_id INT NOT NULL, annual_entitlement DECIMAL(6,2) NOT NULL DEFAULT 0,
 carry_forward TINYINT(1) NOT NULL DEFAULT 0, max_carry_forward DECIMAL(6,2) NOT NULL DEFAULT 0, is_paid TINYINT(1) NOT NULL DEFAULT 1,
 sandwich_enabled TINYINT(1) NOT NULL DEFAULT 0, allow_half_day TINYINT(1) NOT NULL DEFAULT 0, requires_document_after_days DECIMAL(6,2) NOT NULL DEFAULT 0,
 status TINYINT(1) NOT NULL DEFAULT 1, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY(id), UNIQUE KEY uq_leave_policy(company_id,leave_type_id), CONSTRAINT fk_leave_policy_company FOREIGN KEY(company_id) REFERENCES companies(id), CONSTRAINT fk_leave_policy_type FOREIGN KEY(leave_type_id) REFERENCES leavetypetbl(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS employee_leave_balances (
 id BIGINT NOT NULL AUTO_INCREMENT, employee_id INT NOT NULL, leave_type_id INT NOT NULL, balance_year SMALLINT NOT NULL,
 opening_balance DECIMAL(7,2) NOT NULL DEFAULT 0, credited DECIMAL(7,2) NOT NULL DEFAULT 0, used DECIMAL(7,2) NOT NULL DEFAULT 0,
 pending DECIMAL(7,2) NOT NULL DEFAULT 0, adjusted DECIMAL(7,2) NOT NULL DEFAULT 0, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY(id), UNIQUE KEY uq_employee_leave_balance(employee_id,leave_type_id,balance_year), CONSTRAINT fk_leave_balance_employee FOREIGN KEY(employee_id) REFERENCES employeestbl(id) ON DELETE CASCADE, CONSTRAINT fk_leave_balance_type FOREIGN KEY(leave_type_id) REFERENCES leavetypetbl(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS leave_requests (
 id BIGINT NOT NULL AUTO_INCREMENT, legacy_leave_id INT DEFAULT NULL, employee_id INT NOT NULL, company_id INT NOT NULL, leave_type_id INT NOT NULL,
 subject VARCHAR(180) NOT NULL, description TEXT DEFAULT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, requested_days DECIMAL(7,2) NOT NULL DEFAULT 0,
 sandwich_days DECIMAL(7,2) NOT NULL DEFAULT 0, payable_days DECIMAL(7,2) NOT NULL DEFAULT 0, unpaid_days DECIMAL(7,2) NOT NULL DEFAULT 0,
 status ENUM('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending', employee_note VARCHAR(500) DEFAULT NULL, approver_note VARCHAR(500) DEFAULT NULL,
 approved_by INT NOT NULL DEFAULT 0, approved_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY(id), UNIQUE KEY uq_leave_legacy(legacy_leave_id), KEY idx_leave_request_employee(employee_id,start_date), KEY idx_leave_request_company_status(company_id,status),
 CONSTRAINT fk_leave_request_employee FOREIGN KEY(employee_id) REFERENCES employeestbl(id) ON DELETE CASCADE, CONSTRAINT fk_leave_request_company FOREIGN KEY(company_id) REFERENCES companies(id), CONSTRAINT fk_leave_request_type FOREIGN KEY(leave_type_id) REFERENCES leavetypetbl(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS comp_off_ledger (
 id BIGINT NOT NULL AUTO_INCREMENT, employee_id INT NOT NULL, source_type ENUM('holiday_work','weekly_off_work','manual','adjustment','leave_usage') NOT NULL,
 source_date DATE DEFAULT NULL, credit_days DECIMAL(6,2) NOT NULL DEFAULT 0, debit_days DECIMAL(6,2) NOT NULL DEFAULT 0, expiry_date DATE DEFAULT NULL,
 reference_type VARCHAR(60) DEFAULT NULL, reference_id BIGINT DEFAULT NULL, notes VARCHAR(500) DEFAULT NULL, status ENUM('available','used','expired','cancelled') NOT NULL DEFAULT 'available',
 created_by INT NOT NULL DEFAULT 0, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id), KEY idx_comp_off_employee(employee_id,status,expiry_date), CONSTRAINT fk_comp_off_employee FOREIGN KEY(employee_id) REFERENCES employeestbl(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS leave_settings (
 company_id INT NOT NULL, paid_leave_per_year DECIMAL(6,2) NOT NULL DEFAULT 10, comp_off_expiry_days INT NOT NULL DEFAULT 90,
 minimum_comp_off_minutes INT NOT NULL DEFAULT 240, sandwich_enabled TINYINT(1) NOT NULL DEFAULT 1, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY(company_id), CONSTRAINT fk_leave_settings_company FOREIGN KEY(company_id) REFERENCES companies(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SET @company_id := (SELECT id FROM companies WHERE status=1 ORDER BY id LIMIT 1);
INSERT IGNORE INTO leave_settings(company_id,paid_leave_per_year,comp_off_expiry_days,minimum_comp_off_minutes,sandwich_enabled) VALUES(@company_id,10,90,240,1);
INSERT INTO leave_policies(company_id,leave_type_id,annual_entitlement,is_paid,sandwich_enabled,allow_half_day,status)
SELECT @company_id,id,CASE WHEN name='Paid Leave' THEN 10 ELSE 0 END,CASE WHEN name='Paid Leave' THEN 1 ELSE 0 END,CASE WHEN name='Paid Leave' THEN 1 ELSE 0 END,CASE WHEN name='Half Day Leave' THEN 1 ELSE 0 END,1 FROM leavetypetbl
ON DUPLICATE KEY UPDATE status=VALUES(status);
INSERT IGNORE INTO employee_leave_balances(employee_id,leave_type_id,balance_year,credited)
SELECT e.id,p.leave_type_id,YEAR(CURDATE()),p.annual_entitlement FROM employeestbl e JOIN leave_policies p ON p.company_id=e.company_id AND p.status=1;
INSERT IGNORE INTO permissions(module_key,action_key,label) VALUES
('leave_policies','view','View leave policies'),('leave_policies','edit','Edit leave policies'),('leave_requests','view','View leave requests'),('leave_requests','apply','Apply leave'),('leave_requests','approve','Approve leave'),('leave_requests','reject','Reject leave'),('leave_balances','view','View leave balances'),('leave_balances','adjust','Adjust leave balances'),('comp_off','view','View comp-off'),('comp_off','credit','Credit comp-off'),('comp_off','adjust','Adjust comp-off');