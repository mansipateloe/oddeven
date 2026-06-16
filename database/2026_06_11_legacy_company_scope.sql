ALTER TABLE leads ADD COLUMN IF NOT EXISTS company_id INT NOT NULL DEFAULT 0 AFTER lead_id;
ALTER TABLE leads ADD KEY IF NOT EXISTS idx_leads_company_active (company_id,is_active,lead_id);

UPDATE leads l
LEFT JOIN admins a ON a.id=l.created_by
SET l.company_id=COALESCE(NULLIF(a.company_id,0),1)
WHERE l.company_id=0;
