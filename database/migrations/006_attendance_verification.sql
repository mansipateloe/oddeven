ALTER TABLE attendance_sessions
  ADD COLUMN IF NOT EXISTS review_status ENUM('pending','approved','rejected','corrected') NOT NULL DEFAULT 'pending' AFTER attendance_status,
  ADD COLUMN IF NOT EXISTS reviewed_by INT NOT NULL DEFAULT 0 AFTER review_status,
  ADD COLUMN IF NOT EXISTS reviewed_at DATETIME DEFAULT NULL AFTER reviewed_by,
  ADD COLUMN IF NOT EXISTS review_notes VARCHAR(500) DEFAULT NULL AFTER reviewed_at,
  ADD COLUMN IF NOT EXISTS is_locked TINYINT(1) NOT NULL DEFAULT 0 AFTER review_notes;

CREATE TABLE IF NOT EXISTS attendance_corrections (
 id BIGINT NOT NULL AUTO_INCREMENT, session_id BIGINT NOT NULL, employee_id INT NOT NULL,
 old_actual_in DATETIME DEFAULT NULL, new_actual_in DATETIME DEFAULT NULL, old_actual_out DATETIME DEFAULT NULL, new_actual_out DATETIME DEFAULT NULL,
 old_break_minutes INT NOT NULL DEFAULT 0, new_break_minutes INT NOT NULL DEFAULT 0, reason VARCHAR(500) NOT NULL,
 requested_by INT NOT NULL DEFAULT 0, approved_by INT NOT NULL DEFAULT 0, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(id), KEY idx_correction_session(session_id), KEY idx_correction_employee(employee_id,created_at),
 CONSTRAINT fk_correction_session FOREIGN KEY(session_id) REFERENCES attendance_sessions(id) ON DELETE CASCADE,
 CONSTRAINT fk_correction_employee FOREIGN KEY(employee_id) REFERENCES employeestbl(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS attendance_period_locks (
 id BIGINT NOT NULL AUTO_INCREMENT, company_id INT NOT NULL, period_year SMALLINT NOT NULL, period_month TINYINT NOT NULL,
 locked_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, locked_by INT NOT NULL DEFAULT 0, lock_reason VARCHAR(255) DEFAULT NULL,
 unlocked_at DATETIME DEFAULT NULL, unlocked_by INT NOT NULL DEFAULT 0, unlock_reason VARCHAR(255) DEFAULT NULL, is_locked TINYINT(1) NOT NULL DEFAULT 1,
 PRIMARY KEY(id), UNIQUE KEY uq_attendance_period(company_id,period_year,period_month), KEY idx_period_lock(company_id,is_locked),
 CONSTRAINT fk_attendance_lock_company FOREIGN KEY(company_id) REFERENCES companies(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO permissions(module_key,action_key,label) VALUES
('attendance_review','view','View attendance review'),('attendance_review','correct','Correct attendance'),('attendance_review','approve','Approve attendance'),
('attendance_review','reject','Reject attendance'),('attendance_review','lock','Lock attendance period'),('attendance_review','unlock','Unlock attendance period'),('attendance_review','export','Export attendance exceptions');