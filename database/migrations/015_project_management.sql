ALTER TABLE projectstbl ADD COLUMN IF NOT EXISTS company_id INT NOT NULL DEFAULT 0 AFTER id;
ALTER TABLE projectstbl ADD COLUMN IF NOT EXISTS priority ENUM('low','medium','high','critical') NOT NULL DEFAULT 'medium' AFTER status;
ALTER TABLE projectstbl ADD COLUMN IF NOT EXISTS progress_percent TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER priority;
ALTER TABLE projectstbl ADD COLUMN IF NOT EXISTS budget_hours DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER amount;

ALTER TABLE tasktbl ADD COLUMN IF NOT EXISTS company_id INT NOT NULL DEFAULT 0 AFTER id;
ALTER TABLE tasktbl ADD COLUMN IF NOT EXISTS priority ENUM('low','medium','high','critical') NOT NULL DEFAULT 'medium' AFTER task_details;
ALTER TABLE tasktbl ADD COLUMN IF NOT EXISTS estimated_hours DECIMAL(8,2) NOT NULL DEFAULT 0 AFTER priority;

CREATE TABLE IF NOT EXISTS project_team_members (
 id BIGINT AUTO_INCREMENT PRIMARY KEY, project_id INT NOT NULL, employee_id INT NOT NULL,
 role_name VARCHAR(100) DEFAULT NULL, allocation_percent DECIMAL(5,2) NOT NULL DEFAULT 100,
 joined_at DATE NOT NULL, left_at DATE DEFAULT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_project_employee (project_id,employee_id), KEY idx_team_employee(employee_id)
);
CREATE TABLE IF NOT EXISTS project_milestones (
 id BIGINT AUTO_INCREMENT PRIMARY KEY, project_id INT NOT NULL, title VARCHAR(180) NOT NULL,
 description TEXT DEFAULT NULL, due_date DATE DEFAULT NULL,
 status ENUM('pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
 created_by INT NOT NULL DEFAULT 0, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 KEY idx_milestone_project(project_id,status)
);
CREATE TABLE IF NOT EXISTS task_comments (
 id BIGINT AUTO_INCREMENT PRIMARY KEY, task_id INT NOT NULL, comment TEXT NOT NULL,
 created_by_type ENUM('admin','employee') NOT NULL DEFAULT 'admin', created_by INT NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, KEY idx_task_comments(task_id,created_at)
);
CREATE TABLE IF NOT EXISTS task_attachments (
 id BIGINT AUTO_INCREMENT PRIMARY KEY, task_id INT NOT NULL, stored_name VARCHAR(255) NOT NULL,
 original_name VARCHAR(255) NOT NULL, mime_type VARCHAR(100) DEFAULT NULL, file_size BIGINT NOT NULL DEFAULT 0,
 uploaded_by INT NOT NULL DEFAULT 0, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 KEY idx_task_attachment(task_id)
);
INSERT IGNORE INTO permissions(module_key,action_key,label) VALUES
('projects','view','View projects'),('projects','create','Create projects'),('projects','edit','Edit projects'),
('projects','delete','Delete projects'),('project_tasks','view','View project tasks'),
('project_tasks','create','Create project tasks'),('project_tasks','edit','Edit project tasks'),
('project_tasks','comment','Comment on tasks');
UPDATE projectstbl SET company_id=(SELECT id FROM companies WHERE status=1 ORDER BY id LIMIT 1) WHERE company_id=0;
UPDATE tasktbl t INNER JOIN projectstbl p ON p.id=CAST(t.projectId AS UNSIGNED) SET t.company_id=p.company_id WHERE t.company_id=0;
