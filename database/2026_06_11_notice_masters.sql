CREATE TABLE IF NOT EXISTS notice_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    type_key VARCHAR(40) NOT NULL,
    name VARCHAR(100) NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uq_notice_type_company_key (company_id,type_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS notice_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uq_notice_category_company_name (company_id,name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE notices
    MODIFY notice_type VARCHAR(40) NOT NULL DEFAULT 'company',
    ADD COLUMN IF NOT EXISTS category_id INT NULL AFTER notice_type,
    ADD INDEX IF NOT EXISTS idx_notice_category (category_id);

INSERT IGNORE INTO notice_types(company_id,type_key,name)
SELECT id,'company','Company Notice' FROM companies;
INSERT IGNORE INTO notice_types(company_id,type_key,name)
SELECT id,'hr','HR Notice' FROM companies;
INSERT IGNORE INTO notice_types(company_id,type_key,name)
SELECT id,'policy','Policy Notice' FROM companies;
INSERT IGNORE INTO notice_types(company_id,type_key,name)
SELECT id,'holiday','Holiday Notice' FROM companies;
INSERT IGNORE INTO notice_categories(company_id,name)
SELECT id,'General' FROM companies;
