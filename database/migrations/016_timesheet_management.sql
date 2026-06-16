CREATE TABLE IF NOT EXISTS timesheet_entries (
 id BIGINT AUTO_INCREMENT PRIMARY KEY,
 company_id INT NOT NULL,
 employee_id INT NOT NULL,
 project_id INT NOT NULL,
 task_id INT DEFAULT NULL,
 work_date DATE NOT NULL,
 description TEXT NOT NULL,
 hours DECIMAL(5,2) NOT NULL,
 billable_hours DECIMAL(5,2) NOT NULL DEFAULT 0,
 status ENUM('draft','submitted','approved','rejected') NOT NULL DEFAULT 'draft',
 rejection_reason VARCHAR(500) DEFAULT NULL,
 submitted_at DATETIME DEFAULT NULL,
 reviewed_by INT DEFAULT NULL,
 reviewed_at DATETIME DEFAULT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 KEY idx_timesheet_company_date(company_id,work_date),
 KEY idx_timesheet_employee(employee_id,work_date),
 KEY idx_timesheet_project(project_id,work_date),
 KEY idx_timesheet_status(status)
);
INSERT IGNORE INTO permissions(module_key,action_key,label) VALUES
('timesheets','view','View timesheets'),('timesheets','review','Review timesheets'),
('timesheets','export','Export timesheets'),('timesheets','report','View timesheet reports');
INSERT INTO role_permissions(role_id,permission_id,company_id,allowed)
SELECT 2,id,0,1 FROM permissions WHERE module_key='timesheets' AND action_key IN ('view','report','export')
ON DUPLICATE KEY UPDATE allowed=1;
