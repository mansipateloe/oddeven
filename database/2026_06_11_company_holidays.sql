ALTER TABLE holidaytbl
    ADD COLUMN IF NOT EXISTS company_id INT NOT NULL DEFAULT 0 AFTER id,
    ADD INDEX IF NOT EXISTS idx_holiday_company_date (company_id,holidayDate);
