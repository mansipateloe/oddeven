ALTER TABLE finance_invoices ADD COLUMN IF NOT EXISTS recurring_frequency ENUM('none','monthly','quarterly','yearly') NOT NULL DEFAULT 'none' AFTER status;
ALTER TABLE finance_invoices ADD COLUMN IF NOT EXISTS next_invoice_date DATE DEFAULT NULL AFTER recurring_frequency;
ALTER TABLE finance_invoices ADD COLUMN IF NOT EXISTS sent_at DATETIME DEFAULT NULL AFTER next_invoice_date;
ALTER TABLE finance_invoices ADD COLUMN IF NOT EXISTS viewed_at DATETIME DEFAULT NULL AFTER sent_at;
CREATE TABLE IF NOT EXISTS invoice_reminders (
 id BIGINT AUTO_INCREMENT PRIMARY KEY,company_id INT NOT NULL,invoice_id BIGINT NOT NULL,reminder_type ENUM('manual','due_soon','overdue') NOT NULL DEFAULT 'manual',recipient_email VARCHAR(180) NOT NULL,subject VARCHAR(255) NOT NULL,message TEXT NOT NULL,status ENUM('queued','sent','failed') NOT NULL DEFAULT 'queued',sent_at DATETIME DEFAULT NULL,created_by INT NOT NULL DEFAULT 0,created_at DATETIME DEFAULT CURRENT_TIMESTAMP,KEY idx_reminder(invoice_id,status)
);
CREATE TABLE IF NOT EXISTS bank_reconciliation (
 id BIGINT AUTO_INCREMENT PRIMARY KEY,company_id INT NOT NULL,account_id INT NOT NULL,statement_date DATE NOT NULL,statement_balance DECIMAL(14,2) NOT NULL,book_balance DECIMAL(14,2) NOT NULL,difference_amount DECIMAL(14,2) NOT NULL,status ENUM('draft','reconciled') NOT NULL DEFAULT 'draft',notes TEXT DEFAULT NULL,reconciled_by INT DEFAULT NULL,reconciled_at DATETIME DEFAULT NULL,created_at DATETIME DEFAULT CURRENT_TIMESTAMP,KEY idx_reconciliation(company_id,statement_date)
);
CREATE TABLE IF NOT EXISTS database_backups (
 id BIGINT AUTO_INCREMENT PRIMARY KEY,file_name VARCHAR(255) NOT NULL,file_path VARCHAR(500) NOT NULL,file_size BIGINT NOT NULL DEFAULT 0,status ENUM('completed','failed') NOT NULL DEFAULT 'completed',created_by INT NOT NULL DEFAULT 0,created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS project_sprints (
 id BIGINT AUTO_INCREMENT PRIMARY KEY,project_id INT NOT NULL,name VARCHAR(150) NOT NULL,goal TEXT DEFAULT NULL,start_date DATE NOT NULL,end_date DATE NOT NULL,status ENUM('planned','active','completed','cancelled') NOT NULL DEFAULT 'planned',created_by INT NOT NULL DEFAULT 0,created_at DATETIME DEFAULT CURRENT_TIMESTAMP,KEY idx_sprint(project_id,status)
);
ALTER TABLE tasktbl ADD COLUMN IF NOT EXISTS sprint_id BIGINT DEFAULT NULL AFTER projectId;
ALTER TABLE tasktbl ADD COLUMN IF NOT EXISTS board_status ENUM('backlog','todo','in_progress','review','done') NOT NULL DEFAULT 'backlog' AFTER status;
INSERT IGNORE INTO permissions(module_key,action_key,label) VALUES
('backups','view','View database backups'),('backups','create','Create database backups'),('bank_reconciliation','view','View reconciliation'),('bank_reconciliation','create','Create reconciliation'),('project_sprints','view','View sprints'),('project_sprints','manage','Manage sprints');
