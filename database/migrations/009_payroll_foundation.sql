CREATE TABLE IF NOT EXISTS salary_structures (
 id BIGINT NOT NULL AUTO_INCREMENT, employee_id INT NOT NULL, company_id INT NOT NULL, effective_from DATE NOT NULL, effective_to DATE DEFAULT NULL,
 gross_salary DECIMAL(14,2) NOT NULL DEFAULT 0, basic_salary DECIMAL(14,2) NOT NULL DEFAULT 0, hra DECIMAL(14,2) NOT NULL DEFAULT 0,
 special_allowance DECIMAL(14,2) NOT NULL DEFAULT 0, other_allowance DECIMAL(14,2) NOT NULL DEFAULT 0,
 professional_tax_enabled TINYINT(1) NOT NULL DEFAULT 1, retention_type ENUM('none','fixed','percent') NOT NULL DEFAULT 'none',
 retention_value DECIMAL(14,2) NOT NULL DEFAULT 0, status TINYINT(1) NOT NULL DEFAULT 1, created_by INT NOT NULL DEFAULT 0,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY(id), KEY idx_salary_structure_employee(employee_id,effective_from,effective_to),
 CONSTRAINT fk_salary_structure_employee FOREIGN KEY(employee_id) REFERENCES employeestbl(id) ON DELETE CASCADE,
 CONSTRAINT fk_salary_structure_company FOREIGN KEY(company_id) REFERENCES companies(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS payroll_runs (
 id BIGINT NOT NULL AUTO_INCREMENT, company_id INT NOT NULL, period_year SMALLINT NOT NULL, period_month TINYINT NOT NULL,
 status ENUM('draft','generated','pending_approval','approved','locked','cancelled') NOT NULL DEFAULT 'draft',
 attendance_locked TINYINT(1) NOT NULL DEFAULT 0, employee_count INT NOT NULL DEFAULT 0, gross_total DECIMAL(16,2) NOT NULL DEFAULT 0,
 deduction_total DECIMAL(16,2) NOT NULL DEFAULT 0, retention_total DECIMAL(16,2) NOT NULL DEFAULT 0, net_total DECIMAL(16,2) NOT NULL DEFAULT 0,
 generated_by INT NOT NULL DEFAULT 0, generated_at DATETIME DEFAULT NULL, approved_by INT NOT NULL DEFAULT 0, approved_at DATETIME DEFAULT NULL,
 locked_by INT NOT NULL DEFAULT 0, locked_at DATETIME DEFAULT NULL, notes VARCHAR(500) DEFAULT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(id), UNIQUE KEY uq_payroll_company_period(company_id,period_year,period_month), CONSTRAINT fk_payroll_run_company FOREIGN KEY(company_id) REFERENCES companies(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS payroll_items (
 id BIGINT NOT NULL AUTO_INCREMENT, payroll_run_id BIGINT NOT NULL, employee_id INT NOT NULL, salary_structure_id BIGINT NOT NULL,
 calendar_days INT NOT NULL DEFAULT 0, payable_days DECIMAL(7,2) NOT NULL DEFAULT 0, present_days DECIMAL(7,2) NOT NULL DEFAULT 0,
 paid_leave_days DECIMAL(7,2) NOT NULL DEFAULT 0, unpaid_leave_days DECIMAL(7,2) NOT NULL DEFAULT 0, weekly_off_days DECIMAL(7,2) NOT NULL DEFAULT 0,
 holiday_days DECIMAL(7,2) NOT NULL DEFAULT 0, required_minutes INT NOT NULL DEFAULT 0, effective_minutes INT NOT NULL DEFAULT 0,
 overtime_minutes INT NOT NULL DEFAULT 0, gross_salary DECIMAL(14,2) NOT NULL DEFAULT 0, attendance_deduction DECIMAL(14,2) NOT NULL DEFAULT 0,
 leave_deduction DECIMAL(14,2) NOT NULL DEFAULT 0, professional_tax DECIMAL(14,2) NOT NULL DEFAULT 0, custom_deduction DECIMAL(14,2) NOT NULL DEFAULT 0,
 retention_amount DECIMAL(14,2) NOT NULL DEFAULT 0, overtime_amount DECIMAL(14,2) NOT NULL DEFAULT 0, extra_day_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
 total_deduction DECIMAL(14,2) NOT NULL DEFAULT 0, net_salary DECIMAL(14,2) NOT NULL DEFAULT 0,
 status ENUM('calculated','reviewed','approved','held') NOT NULL DEFAULT 'calculated', notes VARCHAR(500) DEFAULT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY(id), UNIQUE KEY uq_payroll_item_employee(payroll_run_id,employee_id), KEY idx_payroll_item_employee(employee_id),
 CONSTRAINT fk_payroll_item_run FOREIGN KEY(payroll_run_id) REFERENCES payroll_runs(id) ON DELETE CASCADE,
 CONSTRAINT fk_payroll_item_employee FOREIGN KEY(employee_id) REFERENCES employeestbl(id),
 CONSTRAINT fk_payroll_item_structure FOREIGN KEY(salary_structure_id) REFERENCES salary_structures(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS payroll_adjustments (
 id BIGINT NOT NULL AUTO_INCREMENT, payroll_item_id BIGINT NOT NULL, adjustment_type ENUM('earning','deduction','retention','reimbursement') NOT NULL,
 title VARCHAR(150) NOT NULL, amount DECIMAL(14,2) NOT NULL DEFAULT 0, notes VARCHAR(500) DEFAULT NULL, created_by INT NOT NULL DEFAULT 0,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id), CONSTRAINT fk_payroll_adjustment_item FOREIGN KEY(payroll_item_id) REFERENCES payroll_items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS retention_ledger (
 id BIGINT NOT NULL AUTO_INCREMENT, employee_id INT NOT NULL, payroll_item_id BIGINT DEFAULT NULL, transaction_type ENUM('hold','release','adjustment') NOT NULL,
 amount DECIMAL(14,2) NOT NULL DEFAULT 0, balance_after DECIMAL(14,2) NOT NULL DEFAULT 0, notes VARCHAR(500) DEFAULT NULL, created_by INT NOT NULL DEFAULT 0,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id), KEY idx_retention_employee(employee_id,created_at),
 CONSTRAINT fk_retention_employee FOREIGN KEY(employee_id) REFERENCES employeestbl(id), CONSTRAINT fk_retention_payroll_item FOREIGN KEY(payroll_item_id) REFERENCES payroll_items(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO salary_structures(employee_id,company_id,effective_from,gross_salary,basic_salary,hra,special_allowance,other_allowance,status)
SELECT e.id,e.company_id,COALESCE(NULLIF(e.joiningDate,''),'2000-01-01'),CAST(e.salary AS DECIMAL(14,2)),ROUND(CAST(e.salary AS DECIMAL(14,2))*0.50,2),ROUND(CAST(e.salary AS DECIMAL(14,2))*0.20,2),ROUND(CAST(e.salary AS DECIMAL(14,2))*0.30,2),0,1 FROM employeestbl e
WHERE NOT EXISTS(SELECT 1 FROM salary_structures s WHERE s.employee_id=e.id AND s.status=1);
INSERT IGNORE INTO permissions(module_key,action_key,label) VALUES
('salary_structures','view','View salary structures'),('salary_structures','edit','Edit salary structures'),
('payroll','view','View payroll'),('payroll','generate','Generate payroll'),('payroll','recalculate','Recalculate payroll'),('payroll','approve','Approve payroll'),('payroll','lock','Lock payroll'),('payroll','adjust','Adjust payroll'),('payroll','export','Export payroll');