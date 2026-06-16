CREATE TABLE IF NOT EXISTS employee_profiles (
  employee_id INT NOT NULL,
  employment_type ENUM('permanent','probation','contract','intern','consultant') NOT NULL DEFAULT 'permanent',
  employment_status ENUM('active','inactive','notice_period','resigned','terminated','retired') NOT NULL DEFAULT 'active',
  confirmation_date DATE DEFAULT NULL,
  notice_period_days INT NOT NULL DEFAULT 0,
  exit_date DATE DEFAULT NULL,
  exit_reason VARCHAR(255) DEFAULT NULL,
  emergency_contact_name VARCHAR(150) DEFAULT NULL,
  emergency_contact_phone VARCHAR(40) DEFAULT NULL,
  blood_group VARCHAR(10) DEFAULT NULL,
  marital_status VARCHAR(30) DEFAULT NULL,
  current_address TEXT DEFAULT NULL,
  permanent_address TEXT DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (employee_id),
  CONSTRAINT fk_employee_profiles_employee FOREIGN KEY (employee_id) REFERENCES employeestbl(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS employee_contracts (
  id BIGINT NOT NULL AUTO_INCREMENT,
  employee_id INT NOT NULL,
  contract_type ENUM('employment','nda','consulting','internship','other') NOT NULL DEFAULT 'employment',
  title VARCHAR(180) NOT NULL,
  start_date DATE DEFAULT NULL,
  end_date DATE DEFAULT NULL,
  status ENUM('draft','active','expired','terminated') NOT NULL DEFAULT 'active',
  notes TEXT DEFAULT NULL,
  created_by INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_employee_contracts_employee (employee_id),
  CONSTRAINT fk_employee_contracts_employee FOREIGN KEY (employee_id) REFERENCES employeestbl(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS employee_increments (
  id BIGINT NOT NULL AUTO_INCREMENT,
  employee_id INT NOT NULL,
  effective_date DATE NOT NULL,
  previous_salary DECIMAL(14,2) NOT NULL DEFAULT 0,
  revised_salary DECIMAL(14,2) NOT NULL DEFAULT 0,
  increment_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
  increment_percent DECIMAL(8,2) NOT NULL DEFAULT 0,
  reason VARCHAR(255) DEFAULT NULL,
  approved_by INT NOT NULL DEFAULT 0,
  created_by INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_employee_increments_employee_date (employee_id, effective_date),
  CONSTRAINT fk_employee_increments_employee FOREIGN KEY (employee_id) REFERENCES employeestbl(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS employee_documents (
  id BIGINT NOT NULL AUTO_INCREMENT,
  employee_id INT NOT NULL,
  document_type ENUM('resume','aadhaar','pan','passport','driving_licence','address_proof','photograph','experience_letter','relieving_letter','salary_slip','contract','other') NOT NULL,
  title VARCHAR(180) NOT NULL,
  stored_name VARCHAR(255) NOT NULL,
  original_name VARCHAR(255) NOT NULL,
  mime_type VARCHAR(120) DEFAULT NULL,
  file_size BIGINT NOT NULL DEFAULT 0,
  document_number VARCHAR(100) DEFAULT NULL,
  issue_date DATE DEFAULT NULL,
  expiry_date DATE DEFAULT NULL,
  notes VARCHAR(500) DEFAULT NULL,
  uploaded_by INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_employee_documents_employee_type (employee_id, document_type),
  CONSTRAINT fk_employee_documents_employee FOREIGN KEY (employee_id) REFERENCES employeestbl(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS employee_status_history (
  id BIGINT NOT NULL AUTO_INCREMENT,
  employee_id INT NOT NULL,
  old_status VARCHAR(40) DEFAULT NULL,
  new_status VARCHAR(40) NOT NULL,
  effective_date DATE NOT NULL,
  reason VARCHAR(255) DEFAULT NULL,
  changed_by INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_employee_status_history_employee (employee_id, effective_date),
  CONSTRAINT fk_employee_status_history_employee FOREIGN KEY (employee_id) REFERENCES employeestbl(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO employee_profiles (employee_id, employment_status, current_address, permanent_address)
SELECT id, IF(status=0,'active','inactive'), address, address FROM employeestbl;

INSERT IGNORE INTO permissions(module_key,action_key,label) VALUES
('employee_profiles','view','View employee profiles'),('employee_profiles','edit','Edit employee profiles'),
('employee_contracts','view','View employee contracts'),('employee_contracts','create','Create employee contracts'),('employee_contracts','edit','Edit employee contracts'),('employee_contracts','delete','Delete employee contracts'),
('employee_increments','view','View employee increments'),('employee_increments','create','Create employee increments'),
('employee_documents','view','View employee documents'),('employee_documents','upload','Upload employee documents'),('employee_documents','download','Download employee documents'),('employee_documents','delete','Delete employee documents');