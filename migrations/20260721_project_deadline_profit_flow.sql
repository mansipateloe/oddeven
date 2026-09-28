ALTER TABLE projectstbl
  ADD COLUMN IF NOT EXISTS original_deadline DATE NULL AFTER enddate,
  ADD COLUMN IF NOT EXISTS current_deadline DATE NULL AFTER original_deadline,
  ADD COLUMN IF NOT EXISTS deadline_extended_count INT NOT NULL DEFAULT 0 AFTER current_deadline,
  ADD COLUMN IF NOT EXISTS extra_scope_value DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER amount;

UPDATE projectstbl
SET original_deadline = NULL
WHERE original_deadline = '0000-00-00';

UPDATE projectstbl
SET current_deadline = NULL
WHERE current_deadline = '0000-00-00';

UPDATE projectstbl
SET original_deadline = enddate
WHERE original_deadline IS NULL
  AND enddate IS NOT NULL
  AND enddate <> '0000-00-00';

UPDATE projectstbl
SET current_deadline = enddate
WHERE current_deadline IS NULL
  AND enddate IS NOT NULL
  AND enddate <> '0000-00-00';

CREATE TABLE IF NOT EXISTS project_deadline_history (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  company_id INT NOT NULL DEFAULT 0,
  project_id INT NOT NULL,
  task_id INT NULL,
  old_deadline DATE NULL,
  new_deadline DATE NULL,
  reason TEXT NOT NULL,
  change_type VARCHAR(40) NOT NULL DEFAULT 'manual',
  status VARCHAR(30) NOT NULL DEFAULT 'approved',
  scope_value DECIMAL(14,2) NOT NULL DEFAULT 0,
  requested_by INT NOT NULL DEFAULT 0,
  approved_by INT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  approved_at DATETIME NULL,
  INDEX idx_project_deadline_history_project (project_id),
  INDEX idx_project_deadline_history_company (company_id),
  INDEX idx_project_deadline_history_task (task_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
