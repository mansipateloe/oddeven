CREATE TABLE IF NOT EXISTS attendance_sessions (
 id BIGINT NOT NULL AUTO_INCREMENT, employee_id INT NOT NULL, shift_id INT NOT NULL, attendance_date DATE NOT NULL,
 scheduled_start DATETIME NOT NULL, scheduled_end DATETIME NOT NULL, actual_in DATETIME DEFAULT NULL, actual_out DATETIME DEFAULT NULL,
 total_minutes INT NOT NULL DEFAULT 0, break_minutes INT NOT NULL DEFAULT 0, effective_minutes INT NOT NULL DEFAULT 0,
 late_minutes INT NOT NULL DEFAULT 0, early_exit_minutes INT NOT NULL DEFAULT 0, overtime_minutes INT NOT NULL DEFAULT 0,
 attendance_status ENUM('not_started','present','late','half_day','absent','weekly_off','holiday','incomplete') NOT NULL DEFAULT 'not_started',
 late_reason VARCHAR(500) DEFAULT NULL, ip_address VARCHAR(45) DEFAULT NULL, user_agent VARCHAR(500) DEFAULT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY(id), UNIQUE KEY uq_attendance_employee_date(employee_id,attendance_date), KEY idx_attendance_shift_date(shift_id,attendance_date),
 CONSTRAINT fk_attendance_employee FOREIGN KEY(employee_id) REFERENCES employeestbl(id) ON DELETE CASCADE,
 CONSTRAINT fk_attendance_shift FOREIGN KEY(shift_id) REFERENCES shifts(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS attendance_events (
 id BIGINT NOT NULL AUTO_INCREMENT, session_id BIGINT NOT NULL, employee_id INT NOT NULL,
 event_type ENUM('sign_in','break_in','break_out','sign_out','manual_adjustment') NOT NULL, event_time DATETIME NOT NULL,
 ip_address VARCHAR(45) DEFAULT NULL, user_agent VARCHAR(500) DEFAULT NULL, notes VARCHAR(500) DEFAULT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id), KEY idx_attendance_events_session(session_id,event_time),
 CONSTRAINT fk_attendance_event_session FOREIGN KEY(session_id) REFERENCES attendance_sessions(id) ON DELETE CASCADE,
 CONSTRAINT fk_attendance_event_employee FOREIGN KEY(employee_id) REFERENCES employeestbl(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO permissions(module_key,action_key,label) VALUES
('attendance','view','View attendance'),('attendance','clock','Use attendance clock'),('attendance','correct','Correct attendance'),('attendance','lock','Lock attendance'),('attendance','approve','Approve attendance');