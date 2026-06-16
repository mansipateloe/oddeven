SET @default_company_id := (SELECT id FROM companies WHERE status=1 ORDER BY id LIMIT 1);
INSERT INTO departments(company_id,name,code,status)
SELECT @default_company_id,'General','GEN',1
WHERE @default_company_id IS NOT NULL
AND NOT EXISTS(SELECT 1 FROM departments WHERE company_id=@default_company_id AND name='General');
SET @general_department_id := (SELECT id FROM departments WHERE company_id=@default_company_id AND name='General' ORDER BY id LIMIT 1);
UPDATE employeestbl SET department_id=@general_department_id WHERE company_id=@default_company_id AND department_id=0 AND @general_department_id IS NOT NULL;