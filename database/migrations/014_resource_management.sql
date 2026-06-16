CREATE TABLE IF NOT EXISTS resource_allocations (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  company_id INT NOT NULL,
  employee_id INT NOT NULL,
  client_id BIGINT NOT NULL,
  project_id INT DEFAULT NULL,
  allocation_type ENUM('full_time','part_time','shared','hourly') NOT NULL DEFAULT 'full_time',
  allocation_percent DECIMAL(5,2) NOT NULL DEFAULT 100.00,
  billing_rate DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  billing_cycle ENUM('monthly','hourly','fixed') NOT NULL DEFAULT 'monthly',
  salary_cost DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  currency_code CHAR(3) NOT NULL DEFAULT 'INR',
  start_date DATE NOT NULL,
  end_date DATE DEFAULT NULL,
  status ENUM('planned','active','completed','cancelled') NOT NULL DEFAULT 'active',
  notes TEXT DEFAULT NULL,
  created_by INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_resource_company_status (company_id,status),
  KEY idx_resource_employee (employee_id,start_date,end_date),
  KEY idx_resource_client (client_id),
  KEY idx_resource_project (project_id)
);

INSERT IGNORE INTO permissions(module_key,action_key,label) VALUES
('resources','view','View resource allocations'),
('resources','create','Create resource allocations'),
('resources','edit','Edit resource allocations'),
('resources','delete','Delete resource allocations'),
('resources','export','Export resource reports');

INSERT INTO role_permissions(role_id,permission_id,company_id,allowed)
SELECT 2,id,0,1 FROM permissions
WHERE module_key='resources' AND action_key IN ('view','create','edit','export')
ON DUPLICATE KEY UPDATE allowed=1;
