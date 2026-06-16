CREATE TABLE IF NOT EXISTS task_assignees (
    task_id INT NOT NULL,
    employee_id INT NOT NULL,
    is_primary TINYINT(1) NOT NULL DEFAULT 0,
    assigned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    assigned_by INT NOT NULL DEFAULT 0,
    PRIMARY KEY (task_id, employee_id),
    KEY idx_task_assignees_employee (employee_id, task_id),
    CONSTRAINT fk_task_assignees_task FOREIGN KEY (task_id) REFERENCES tasktbl(id) ON DELETE CASCADE,
    CONSTRAINT fk_task_assignees_employee FOREIGN KEY (employee_id) REFERENCES employeestbl(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO task_assignees(task_id,employee_id,is_primary,assigned_at)
SELECT t.id,CAST(t.developerId AS UNSIGNED),1,COALESCE(t.created_at,NOW())
FROM tasktbl t
JOIN employeestbl e ON e.id=CAST(t.developerId AS UNSIGNED)
WHERE CAST(t.developerId AS UNSIGNED)>0;

ALTER TABLE tasktbl MODIFY developerId VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE projectstbl MODIFY developerId VARCHAR(255) NOT NULL DEFAULT '';

INSERT IGNORE INTO role_permissions(role_id,permission_id,company_id,allowed)
SELECT ua.utype,p.id,0,1
FROM user_access ua
JOIN permissions p
  ON p.action_key=CASE
      WHEN ua.mname LIKE 'add\_%' THEN 'create'
      WHEN ua.mname LIKE 'update\_%' THEN 'edit'
      WHEN ua.mname LIKE 'delete\_%' THEN 'delete'
      ELSE 'view'
    END
 AND p.module_key=CASE
      WHEN REPLACE(REPLACE(REPLACE(REPLACE(ua.mname,'add_',''),'update_',''),'view_',''),'delete_','')='lead' THEN 'clients'
      WHEN REPLACE(REPLACE(REPLACE(REPLACE(ua.mname,'add_',''),'update_',''),'view_',''),'delete_','')='project' THEN 'projects'
      WHEN REPLACE(REPLACE(REPLACE(REPLACE(ua.mname,'add_',''),'update_',''),'view_',''),'delete_','')='employee' THEN 'employees'
      WHEN REPLACE(REPLACE(REPLACE(REPLACE(ua.mname,'add_',''),'update_',''),'view_',''),'delete_','')='leave' THEN 'leave_requests'
      WHEN REPLACE(REPLACE(REPLACE(REPLACE(ua.mname,'add_',''),'update_',''),'view_',''),'delete_','')='salary' THEN 'payroll'
      WHEN REPLACE(REPLACE(REPLACE(REPLACE(ua.mname,'add_',''),'update_',''),'view_',''),'delete_','')='domain_hosting' THEN 'subscriptions'
      WHEN REPLACE(REPLACE(REPLACE(REPLACE(ua.mname,'add_',''),'update_',''),'view_',''),'delete_','') IN ('invocie','invoice','deposit','expense','project_expense') THEN 'finance'
      ELSE REPLACE(REPLACE(REPLACE(REPLACE(ua.mname,'add_',''),'update_',''),'view_',''),'delete_','')
    END
WHERE ua.is_access=1;
