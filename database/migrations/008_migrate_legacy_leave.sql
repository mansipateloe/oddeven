INSERT IGNORE INTO leave_requests(legacy_leave_id,employee_id,company_id,leave_type_id,subject,description,start_date,end_date,requested_days,sandwich_days,payable_days,unpaid_days,status,approver_note,approved_at,created_at)
SELECT l.id,l.emp_id,e.company_id,COALESCE(t.id,(SELECT id FROM leavetypetbl ORDER BY id LIMIT 1)),l.subject,l.description,
STR_TO_DATE(l.start_date,'%Y-%m-%d'),STR_TO_DATE(l.end_date,'%Y-%m-%d'),
GREATEST(1,DATEDIFF(STR_TO_DATE(l.end_date,'%Y-%m-%d'),STR_TO_DATE(l.start_date,'%Y-%m-%d'))+1),0,
GREATEST(1,DATEDIFF(STR_TO_DATE(l.end_date,'%Y-%m-%d'),STR_TO_DATE(l.start_date,'%Y-%m-%d'))+1),0,
CASE l.is_approved WHEN 1 THEN 'approved' WHEN 2 THEN 'rejected' ELSE 'pending' END,l.remarks,CASE WHEN l.is_approved=1 THEN COALESCE(l.created_at,NOW()) ELSE NULL END,COALESCE(l.created_at,NOW())
FROM leave_master l JOIN employeestbl e ON e.id=l.emp_id LEFT JOIN leavetypetbl t ON t.name=l.type
WHERE STR_TO_DATE(l.start_date,'%Y-%m-%d') IS NOT NULL AND STR_TO_DATE(l.end_date,'%Y-%m-%d') IS NOT NULL;