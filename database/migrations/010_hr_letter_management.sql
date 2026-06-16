CREATE TABLE IF NOT EXISTS hr_letter_templates (
 id BIGINT NOT NULL AUTO_INCREMENT, company_id INT NOT NULL, letter_type VARCHAR(40) NOT NULL, name VARCHAR(150) NOT NULL,
 subject VARCHAR(255) NOT NULL, body_html MEDIUMTEXT NOT NULL, is_default TINYINT(1) NOT NULL DEFAULT 0, status TINYINT(1) NOT NULL DEFAULT 1,
 created_by INT NOT NULL DEFAULT 0, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY(id), KEY idx_letter_template_company(company_id,letter_type,status), CONSTRAINT fk_letter_template_company FOREIGN KEY(company_id) REFERENCES companies(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS hr_letters (
 id BIGINT NOT NULL AUTO_INCREMENT, company_id INT NOT NULL, employee_id INT NOT NULL, template_id BIGINT DEFAULT NULL, letter_type VARCHAR(40) NOT NULL,
 reference_no VARCHAR(60) NOT NULL, subject VARCHAR(255) NOT NULL, body_html MEDIUMTEXT NOT NULL, issue_date DATE NOT NULL,
 status ENUM('draft','issued','emailed','cancelled') NOT NULL DEFAULT 'draft', recipient_email VARCHAR(180) DEFAULT NULL,
 created_by INT NOT NULL DEFAULT 0, issued_by INT NOT NULL DEFAULT 0, issued_at DATETIME DEFAULT NULL, emailed_at DATETIME DEFAULT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY(id), UNIQUE KEY uq_letter_reference(company_id,reference_no), KEY idx_letter_employee(employee_id,issue_date),
 CONSTRAINT fk_hr_letter_company FOREIGN KEY(company_id) REFERENCES companies(id), CONSTRAINT fk_hr_letter_employee FOREIGN KEY(employee_id) REFERENCES employeestbl(id),
 CONSTRAINT fk_hr_letter_template FOREIGN KEY(template_id) REFERENCES hr_letter_templates(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS hr_letter_delivery_logs (
 id BIGINT NOT NULL AUTO_INCREMENT, letter_id BIGINT NOT NULL, recipient_email VARCHAR(180) NOT NULL, delivery_status ENUM('sent','failed') NOT NULL,
 error_message VARCHAR(500) DEFAULT NULL, sent_by INT NOT NULL DEFAULT 0, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(id), KEY idx_letter_delivery(letter_id,created_at), CONSTRAINT fk_letter_delivery FOREIGN KEY(letter_id) REFERENCES hr_letters(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT IGNORE INTO permissions(module_key,action_key,label) VALUES
('hr_letters','view','View HR letters'),('hr_letters','create','Create HR letters'),('hr_letters','issue','Issue HR letters'),('hr_letters','email','Email HR letters'),('hr_letters','cancel','Cancel HR letters'),
('hr_letter_templates','view','View HR letter templates'),('hr_letter_templates','create','Create HR letter templates'),('hr_letter_templates','edit','Edit HR letter templates');
