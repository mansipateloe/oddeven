ALTER TABLE audit_logs ADD COLUMN IF NOT EXISTS employee_id INT DEFAULT NULL AFTER actor_id;
ALTER TABLE audit_logs ADD COLUMN IF NOT EXISTS session_key VARCHAR(128) DEFAULT NULL AFTER employee_id;
ALTER TABLE audit_logs ADD COLUMN IF NOT EXISTS browser_name VARCHAR(80) DEFAULT NULL AFTER user_agent;
ALTER TABLE audit_logs ADD COLUMN IF NOT EXISTS device_type VARCHAR(40) DEFAULT NULL AFTER browser_name;
ALTER TABLE audit_logs ADD COLUMN IF NOT EXISTS platform_name VARCHAR(80) DEFAULT NULL AFTER device_type;
ALTER TABLE audit_logs ADD COLUMN IF NOT EXISTS risk_level ENUM('normal','review','suspicious') NOT NULL DEFAULT 'normal' AFTER platform_name;
ALTER TABLE audit_logs ADD COLUMN IF NOT EXISTS risk_reasons VARCHAR(500) DEFAULT NULL AFTER risk_level;
ALTER TABLE audit_logs ADD KEY IF NOT EXISTS idx_audit_employee(employee_id,created_at);
ALTER TABLE audit_logs ADD KEY IF NOT EXISTS idx_audit_risk(company_id,risk_level,created_at);
CREATE TABLE IF NOT EXISTS audit_retention_settings (
 company_id INT NOT NULL, retention_days INT NOT NULL DEFAULT 730, preserve_security_events TINYINT(1) NOT NULL DEFAULT 1,
 last_purged_at DATETIME DEFAULT NULL, updated_by INT NOT NULL DEFAULT 0, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY(company_id), CONSTRAINT fk_audit_retention_company FOREIGN KEY(company_id) REFERENCES companies(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT IGNORE INTO audit_retention_settings(company_id) SELECT id FROM companies;
INSERT IGNORE INTO permissions(module_key,action_key,label) VALUES
('activity_audit','view','View activity audit dashboard'),('activity_audit','export','Export activity audit logs'),('activity_audit','retention','Manage audit retention'),('activity_audit','purge','Purge expired audit logs');
