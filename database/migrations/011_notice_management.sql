CREATE TABLE IF NOT EXISTS notices (
 id BIGINT NOT NULL AUTO_INCREMENT, company_id INT NOT NULL, notice_type ENUM('company','hr','policy','holiday') NOT NULL DEFAULT 'company',
 title VARCHAR(180) NOT NULL, body_html MEDIUMTEXT NOT NULL, priority ENUM('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
 audience_type ENUM('all','department','employee') NOT NULL DEFAULT 'all', publish_at DATETIME NOT NULL, expires_at DATETIME DEFAULT NULL,
 show_popup TINYINT(1) NOT NULL DEFAULT 0, acknowledgement_required TINYINT(1) NOT NULL DEFAULT 0,
 attachment_stored_name VARCHAR(255) DEFAULT NULL, attachment_original_name VARCHAR(255) DEFAULT NULL, attachment_mime VARCHAR(120) DEFAULT NULL, attachment_size INT NOT NULL DEFAULT 0,
 status ENUM('draft','scheduled','published','expired','cancelled') NOT NULL DEFAULT 'draft', created_by INT NOT NULL DEFAULT 0,
 published_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY(id), KEY idx_notice_active(company_id,status,publish_at,expires_at), CONSTRAINT fk_notice_company FOREIGN KEY(company_id) REFERENCES companies(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS notice_targets (
 id BIGINT NOT NULL AUTO_INCREMENT, notice_id BIGINT NOT NULL, department_id INT DEFAULT NULL, employee_id INT DEFAULT NULL,
 PRIMARY KEY(id), UNIQUE KEY uq_notice_department(notice_id,department_id), UNIQUE KEY uq_notice_employee(notice_id,employee_id),
 CONSTRAINT fk_notice_target_notice FOREIGN KEY(notice_id) REFERENCES notices(id) ON DELETE CASCADE,
 CONSTRAINT fk_notice_target_department FOREIGN KEY(department_id) REFERENCES departments(id) ON DELETE CASCADE,
 CONSTRAINT fk_notice_target_employee FOREIGN KEY(employee_id) REFERENCES employeestbl(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS notice_receipts (
 id BIGINT NOT NULL AUTO_INCREMENT, notice_id BIGINT NOT NULL, employee_id INT NOT NULL, first_seen_at DATETIME DEFAULT NULL, read_at DATETIME DEFAULT NULL,
 acknowledged_at DATETIME DEFAULT NULL, acknowledgement_ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(id), UNIQUE KEY uq_notice_receipt(notice_id,employee_id), KEY idx_employee_receipts(employee_id,read_at),
 CONSTRAINT fk_notice_receipt_notice FOREIGN KEY(notice_id) REFERENCES notices(id) ON DELETE CASCADE,
 CONSTRAINT fk_notice_receipt_employee FOREIGN KEY(employee_id) REFERENCES employeestbl(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS notice_events (
 id BIGINT NOT NULL AUTO_INCREMENT, notice_id BIGINT NOT NULL, employee_id INT DEFAULT NULL, actor_type ENUM('admin','employee','system') NOT NULL,
 actor_id INT NOT NULL DEFAULT 0, event_type VARCHAR(40) NOT NULL, details VARCHAR(500) DEFAULT NULL, ip_address VARCHAR(45) DEFAULT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id), KEY idx_notice_events(notice_id,created_at),
 CONSTRAINT fk_notice_event_notice FOREIGN KEY(notice_id) REFERENCES notices(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT IGNORE INTO permissions(module_key,action_key,label) VALUES
('notices','view','View notices'),('notices','create','Create notices'),('notices','edit','Edit notices'),('notices','publish','Publish notices'),('notices','cancel','Cancel notices'),('notices','reports','View notice delivery reports');
INSERT INTO notices(company_id,notice_type,title,body_html,priority,audience_type,publish_at,expires_at,status,created_by,published_at)
SELECT (SELECT id FROM companies ORDER BY id LIMIT 1),'company',LEFT(REPLACE(REPLACE(n.notice,'<b>',''),'</b>',''),180),n.notice,'normal','all',STR_TO_DATE(n.startingDate,'%d-%m-%Y'),STR_TO_DATE(n.endingDate,'%d-%m-%Y'),CASE WHEN STR_TO_DATE(n.endingDate,'%d-%m-%Y')<NOW() THEN 'expired' WHEN n.noticeStatus=1 THEN 'published' ELSE 'draft' END,0,STR_TO_DATE(n.startingDate,'%d-%m-%Y')
FROM noticetbl n WHERE NOT EXISTS(SELECT 1 FROM notices x WHERE x.body_html=n.notice AND x.publish_at=STR_TO_DATE(n.startingDate,'%d-%m-%Y'));
