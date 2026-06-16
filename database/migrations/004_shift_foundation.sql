CREATE TABLE IF NOT EXISTS shifts (
 id INT NOT NULL AUTO_INCREMENT, company_id INT NOT NULL, name VARCHAR(120) NOT NULL, code VARCHAR(30) NOT NULL,
 shift_type ENUM('general','night','us','flexible','custom') NOT NULL DEFAULT 'general', start_time TIME NOT NULL, end_time TIME NOT NULL,
 crosses_midnight TINYINT(1) NOT NULL DEFAULT 0, required_minutes INT NOT NULL DEFAULT 510, break_minutes INT NOT NULL DEFAULT 60,
 grace_in_minutes INT NOT NULL DEFAULT 0, grace_out_minutes INT NOT NULL DEFAULT 0, earliest_checkin_minutes INT NOT NULL DEFAULT 120,
 latest_checkout_minutes INT NOT NULL DEFAULT 240, flexible_start_from TIME DEFAULT NULL, flexible_start_to TIME DEFAULT NULL,
 status TINYINT(1) NOT NULL DEFAULT 1, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY(id), UNIQUE KEY uq_shift_company_code(company_id,code), KEY idx_shifts_company(company_id), CONSTRAINT fk_shifts_company FOREIGN KEY(company_id) REFERENCES companies(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS shift_weekly_off_rules (
 id BIGINT NOT NULL AUTO_INCREMENT, shift_id INT NOT NULL, weekday TINYINT NOT NULL, week_of_month TINYINT NOT NULL DEFAULT 0,
 is_off TINYINT(1) NOT NULL DEFAULT 1, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(id), UNIQUE KEY uq_shift_weekday_week(shift_id,weekday,week_of_month), CONSTRAINT fk_shift_weekly_rule FOREIGN KEY(shift_id) REFERENCES shifts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS employee_shift_assignments (
 id BIGINT NOT NULL AUTO_INCREMENT, employee_id INT NOT NULL, shift_id INT NOT NULL, effective_from DATE NOT NULL, effective_to DATE DEFAULT NULL,
 status TINYINT(1) NOT NULL DEFAULT 1, notes VARCHAR(255) DEFAULT NULL, assigned_by INT NOT NULL DEFAULT 0, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(id), KEY idx_assignment_employee_dates(employee_id,effective_from,effective_to), KEY idx_assignment_shift(shift_id),
 CONSTRAINT fk_shift_assignment_employee FOREIGN KEY(employee_id) REFERENCES employeestbl(id) ON DELETE CASCADE,
 CONSTRAINT fk_shift_assignment_shift FOREIGN KEY(shift_id) REFERENCES shifts(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS employee_shift_history (
 id BIGINT NOT NULL AUTO_INCREMENT, employee_id INT NOT NULL, old_shift_id INT DEFAULT NULL, new_shift_id INT NOT NULL,
 effective_from DATE NOT NULL, effective_to DATE DEFAULT NULL, reason VARCHAR(255) DEFAULT NULL, changed_by INT NOT NULL DEFAULT 0,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id), KEY idx_shift_history_employee(employee_id,effective_from)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @company_id := (SELECT id FROM companies WHERE status=1 ORDER BY id LIMIT 1);
INSERT INTO shifts(company_id,name,code,shift_type,start_time,end_time,crosses_midnight,required_minutes,break_minutes,grace_in_minutes,grace_out_minutes)
SELECT @company_id,'General Shift','GENERAL','general','10:00:00','19:30:00',0,510,60,30,15
WHERE @company_id IS NOT NULL AND NOT EXISTS(SELECT 1 FROM shifts WHERE company_id=@company_id AND code='GENERAL');
SET @general_shift := (SELECT id FROM shifts WHERE company_id=@company_id AND code='GENERAL' LIMIT 1);
INSERT IGNORE INTO shift_weekly_off_rules(shift_id,weekday,week_of_month,is_off) VALUES(@general_shift,0,0,1),(@general_shift,6,1,1),(@general_shift,6,3,1),(@general_shift,6,5,1);
INSERT INTO employee_shift_assignments(employee_id,shift_id,effective_from,status,notes,assigned_by)
SELECT e.id,@general_shift,COALESCE(NULLIF(e.joiningDate,''),'2000-01-01'),1,'Initial migration assignment',0 FROM employeestbl e
WHERE @general_shift IS NOT NULL AND NOT EXISTS(SELECT 1 FROM employee_shift_assignments a WHERE a.employee_id=e.id AND a.status=1);
INSERT IGNORE INTO permissions(module_key,action_key,label) VALUES
('shifts','view','View shifts'),('shifts','create','Create shifts'),('shifts','edit','Edit shifts'),('shifts','delete','Deactivate shifts'),
('shift_assignments','view','View shift assignments'),('shift_assignments','assign','Assign employee shifts'),('shift_assignments','history','View shift history');