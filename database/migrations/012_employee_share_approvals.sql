ALTER TABLE notices
    MODIFY notice_type VARCHAR(40) NOT NULL DEFAULT 'company',
    MODIFY status VARCHAR(30) NOT NULL DEFAULT 'draft';

UPDATE notices
SET status = 'pending_review'
WHERE notice_type = 'employee_share' AND (status IS NULL OR status = '');