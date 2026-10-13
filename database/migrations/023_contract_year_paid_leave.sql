CREATE TABLE IF NOT EXISTS employee_continuation_history (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    employee_id INT NOT NULL,
    term_no INT NOT NULL DEFAULT 1,
    previous_from DATE NULL,
    previous_to DATE NULL,
    renewed_from DATE NOT NULL,
    renewed_to DATE NOT NULL,
    renewal_date DATE NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'continued',
    new_designation VARCHAR(255) NULL,
    revised_salary VARCHAR(255) NULL,
    remarks TEXT NULL,
    letter_id BIGINT NULL,
    created_by INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_employee_continuation_employee (employee_id),
    INDEX idx_employee_continuation_company (company_id),
    INDEX idx_employee_continuation_letter (letter_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

UPDATE leave_settings
SET paid_leave_per_year=10,sandwich_enabled=1;

UPDATE leave_policies p
JOIN leavetypetbl t ON t.id=p.leave_type_id
SET p.annual_entitlement=10,p.is_paid=1,p.sandwich_enabled=1,p.status=1
WHERE LOWER(t.name)='casual leave';

UPDATE leave_master
SET type='Casual Leave'
WHERE type IS NOT NULL AND type<>'Casual Leave';

UPDATE leave_requests
SET leave_type_id=(
    SELECT id FROM leavetypetbl WHERE LOWER(name)='casual leave' ORDER BY id LIMIT 1
)
WHERE leave_type_id<>(
    SELECT id FROM leavetypetbl WHERE LOWER(name)='casual leave' ORDER BY id LIMIT 1
);
