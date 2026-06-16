INSERT INTO companies(code,legal_name,display_name,company_type,country_code,currency_code,gstin,status)
VALUES
('OE-IN-GST','Oddeven Infotech Private Limited','Oddeven Infotech Pvt Ltd','india_gst','IN','INR',NULL,1),
('PHP-IN-GST','PHP Tech','PHP Tech','india_gst','IN','INR',NULL,1),
('OE-US-LLC','Oddeven Infotech LLC','Oddeven Infotech LLC','usa','US','USD',NULL,1)
ON DUPLICATE KEY UPDATE
legal_name=VALUES(legal_name),display_name=VALUES(display_name),company_type=VALUES(company_type),
country_code=VALUES(country_code),currency_code=VALUES(currency_code),status=1;

INSERT INTO app_settings(company_id,setting_key,setting_value,is_encrypted)
SELECT id,'invoice_tax_percent',IF(company_type='india_gst','18','0'),0 FROM companies
WHERE code IN('OE-IN-GST','PHP-IN-GST','OE-US-LLC')
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);

INSERT INTO app_settings(company_id,setting_key,setting_value,is_encrypted)
SELECT id,'invoice_prefix',
CASE code WHEN 'OE-IN-GST' THEN 'OE' WHEN 'PHP-IN-GST' THEN 'PHP' ELSE 'OELLC' END,0
FROM companies WHERE code IN('OE-IN-GST','PHP-IN-GST','OE-US-LLC')
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);
