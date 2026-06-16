CREATE TABLE IF NOT EXISTS clients (
 id BIGINT NOT NULL AUTO_INCREMENT, company_id INT NOT NULL, client_code VARCHAR(30) NOT NULL, legal_name VARCHAR(180) NOT NULL, display_name VARCHAR(180) NOT NULL,
 client_type ENUM('company','individual') NOT NULL DEFAULT 'company', status ENUM('prospect','active','inactive','on_hold','closed') NOT NULL DEFAULT 'active',
 industry VARCHAR(120) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, email VARCHAR(180) DEFAULT NULL, phone VARCHAR(40) DEFAULT NULL,
 billing_address TEXT DEFAULT NULL, city VARCHAR(100) DEFAULT NULL, state VARCHAR(100) DEFAULT NULL, country VARCHAR(100) DEFAULT NULL, postal_code VARCHAR(30) DEFAULT NULL,
 tax_id VARCHAR(80) DEFAULT NULL, currency_code CHAR(3) NOT NULL DEFAULT 'INR', payment_terms_days INT NOT NULL DEFAULT 0, source_lead_id INT DEFAULT NULL,
 notes TEXT DEFAULT NULL, created_by INT NOT NULL DEFAULT 0, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY(id), UNIQUE KEY uq_client_code(company_id,client_code), KEY idx_client_company_status(company_id,status), CONSTRAINT fk_client_company FOREIGN KEY(company_id) REFERENCES companies(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS client_contacts (
 id BIGINT NOT NULL AUTO_INCREMENT, client_id BIGINT NOT NULL, name VARCHAR(150) NOT NULL, designation VARCHAR(120) DEFAULT NULL, email VARCHAR(180) DEFAULT NULL,
 phone VARCHAR(40) DEFAULT NULL, is_primary TINYINT(1) NOT NULL DEFAULT 0, status TINYINT(1) NOT NULL DEFAULT 1, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(id), KEY idx_client_contact(client_id,status), CONSTRAINT fk_client_contact FOREIGN KEY(client_id) REFERENCES clients(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS client_documents (
 id BIGINT NOT NULL AUTO_INCREMENT, client_id BIGINT NOT NULL, document_type ENUM('contract','nda','tax','proposal','other') NOT NULL DEFAULT 'other', title VARCHAR(180) NOT NULL,
 stored_name VARCHAR(255) NOT NULL, original_name VARCHAR(255) NOT NULL, mime_type VARCHAR(120) DEFAULT NULL, file_size INT NOT NULL DEFAULT 0,
 expiry_date DATE DEFAULT NULL, uploaded_by INT NOT NULL DEFAULT 0, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(id), KEY idx_client_document(client_id,document_type), CONSTRAINT fk_client_document FOREIGN KEY(client_id) REFERENCES clients(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS client_contracts (
 id BIGINT NOT NULL AUTO_INCREMENT, client_id BIGINT NOT NULL, contract_type ENUM('service','retainer','dedicated_resource','nda','other') NOT NULL DEFAULT 'service',
 title VARCHAR(180) NOT NULL, reference_no VARCHAR(80) DEFAULT NULL, start_date DATE DEFAULT NULL, end_date DATE DEFAULT NULL, value_amount DECIMAL(16,2) NOT NULL DEFAULT 0,
 currency_code CHAR(3) NOT NULL DEFAULT 'INR', billing_cycle ENUM('one_time','weekly','monthly','quarterly','yearly','hourly') NOT NULL DEFAULT 'one_time',
 status ENUM('draft','active','expired','terminated') NOT NULL DEFAULT 'draft', notes TEXT DEFAULT NULL, created_by INT NOT NULL DEFAULT 0, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY(id), KEY idx_client_contract(client_id,status),
 CONSTRAINT fk_client_contract FOREIGN KEY(client_id) REFERENCES clients(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS client_communications (
 id BIGINT NOT NULL AUTO_INCREMENT, client_id BIGINT NOT NULL, contact_id BIGINT DEFAULT NULL, communication_type ENUM('call','email','meeting','note','message') NOT NULL DEFAULT 'note',
 subject VARCHAR(180) NOT NULL, details TEXT DEFAULT NULL, communication_at DATETIME NOT NULL, next_followup_at DATETIME DEFAULT NULL, created_by INT NOT NULL DEFAULT 0,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id), KEY idx_client_communication(client_id,communication_at),
 CONSTRAINT fk_client_communication FOREIGN KEY(client_id) REFERENCES clients(id) ON DELETE CASCADE, CONSTRAINT fk_client_communication_contact FOREIGN KEY(contact_id) REFERENCES client_contacts(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE projectstbl ADD COLUMN IF NOT EXISTS client_id BIGINT DEFAULT NULL AFTER id;
INSERT IGNORE INTO permissions(module_key,action_key,label) VALUES
('clients','view','View clients'),('clients','create','Create clients'),('clients','edit','Edit clients'),('clients','delete','Delete clients'),('clients','export','Export clients'),
('client_contacts','create','Create client contacts'),('client_contacts','edit','Edit client contacts'),('client_documents','upload','Upload client documents'),('client_documents','download','Download client documents'),
('client_contracts','create','Create client contracts'),('client_contracts','edit','Edit client contracts'),('client_communications','create','Record client communications');
INSERT INTO clients(company_id,client_code,legal_name,display_name,status,email,phone,city,country,created_by)
SELECT c.id,CONCAT('LEG-',LPAD(p.id,5,'0')),p.customerName,p.customerName,'active',NULLIF(p.email,''),NULLIF(p.phone,''),NULLIF(p.location,''),NULLIF(p.country,''),0
FROM projectstbl p JOIN companies c ON c.id=(SELECT id FROM companies ORDER BY id LIMIT 1)
WHERE p.customerName<>'' AND NOT EXISTS(SELECT 1 FROM clients x WHERE x.company_id=c.id AND LOWER(x.display_name)=LOWER(p.customerName));
UPDATE projectstbl p JOIN clients c ON LOWER(c.display_name)=LOWER(p.customerName) SET p.client_id=c.id WHERE p.client_id IS NULL AND p.customerName<>'';
