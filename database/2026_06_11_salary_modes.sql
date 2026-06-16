ALTER TABLE salary_structures
    ADD COLUMN IF NOT EXISTS salary_mode ENUM('fixed','hourly') NOT NULL DEFAULT 'fixed' AFTER effective_to,
    ADD COLUMN IF NOT EXISTS hourly_rate DECIMAL(14,2) NOT NULL DEFAULT 0.00 AFTER gross_salary,
    ADD COLUMN IF NOT EXISTS overtime_policy ENUM('tracking_only','paid') NOT NULL DEFAULT 'tracking_only' AFTER professional_tax_enabled,
    ADD COLUMN IF NOT EXISTS overtime_rate DECIMAL(14,2) NOT NULL DEFAULT 0.00 AFTER overtime_policy;
