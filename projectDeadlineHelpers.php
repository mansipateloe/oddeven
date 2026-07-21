<?php
if (!function_exists('oecrm_project_deadline_columns')) {
    function oecrm_project_deadline_columns(mysqli $conn): array
    {
        $columns = [];
        $result = mysqli_query($conn, 'SHOW COLUMNS FROM projectstbl');
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $columns[strtolower($row['Field'])] = $row['Field'];
            }
        }
        return $columns;
    }
}

if (!function_exists('oecrm_project_deadline_ensure_schema')) {
    function oecrm_project_deadline_ensure_schema(mysqli $conn): void
    {
        static $checked = false;
        if ($checked) {
            return;
        }
        $checked = true;

        $columns = oecrm_project_deadline_columns($conn);
        if (!isset($columns['original_deadline'])) {
            mysqli_query($conn, 'ALTER TABLE projectstbl ADD COLUMN original_deadline DATE NULL AFTER enddate');
        }
        if (!isset($columns['current_deadline'])) {
            mysqli_query($conn, 'ALTER TABLE projectstbl ADD COLUMN current_deadline DATE NULL AFTER original_deadline');
        }
        if (!isset($columns['deadline_extended_count'])) {
            mysqli_query($conn, 'ALTER TABLE projectstbl ADD COLUMN deadline_extended_count INT NOT NULL DEFAULT 0 AFTER current_deadline');
        }
        if (!isset($columns['extra_scope_value'])) {
            mysqli_query($conn, 'ALTER TABLE projectstbl ADD COLUMN extra_scope_value DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER amount');
        }

        mysqli_query($conn, "UPDATE projectstbl SET original_deadline=NULL WHERE original_deadline='0000-00-00'");
        mysqli_query($conn, "UPDATE projectstbl SET current_deadline=NULL WHERE current_deadline='0000-00-00'");
        mysqli_query($conn, "UPDATE projectstbl SET original_deadline=enddate WHERE original_deadline IS NULL AND enddate IS NOT NULL AND enddate<>'0000-00-00'");
        mysqli_query($conn, "UPDATE projectstbl SET current_deadline=enddate WHERE current_deadline IS NULL AND enddate IS NOT NULL AND enddate<>'0000-00-00'");

        mysqli_query($conn, "CREATE TABLE IF NOT EXISTS project_deadline_history (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            company_id INT NOT NULL DEFAULT 0,
            project_id INT NOT NULL,
            task_id INT NULL,
            old_deadline DATE NULL,
            new_deadline DATE NULL,
            reason TEXT NOT NULL,
            change_type VARCHAR(40) NOT NULL DEFAULT 'manual',
            status VARCHAR(30) NOT NULL DEFAULT 'approved',
            scope_value DECIMAL(14,2) NOT NULL DEFAULT 0,
            requested_by INT NOT NULL DEFAULT 0,
            approved_by INT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            approved_at DATETIME NULL,
            INDEX idx_project_deadline_history_project (project_id),
            INDEX idx_project_deadline_history_company (company_id),
            INDEX idx_project_deadline_history_task (task_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }
}

if (!function_exists('oecrm_project_clean_date')) {
    function oecrm_project_clean_date($date): ?string
    {
        $date = trim((string) $date);
        if ($date === '' || $date === '0000-00-00') {
            return null;
        }
        $parsed = DateTime::createFromFormat('Y-m-d', $date);
        return $parsed && $parsed->format('Y-m-d') === $date ? $date : null;
    }
}

if (!function_exists('oecrm_project_current_deadline')) {
    function oecrm_project_current_deadline(array $project): ?string
    {
        return oecrm_project_clean_date($project['current_deadline'] ?? null)
            ?: oecrm_project_clean_date($project['enddate'] ?? null);
    }
}

if (!function_exists('oecrm_project_original_deadline')) {
    function oecrm_project_original_deadline(array $project): ?string
    {
        return oecrm_project_clean_date($project['original_deadline'] ?? null)
            ?: oecrm_project_current_deadline($project);
    }
}

if (!function_exists('oecrm_project_task_beyond_deadline')) {
    function oecrm_project_task_beyond_deadline($taskDueDate, $projectDeadline): bool
    {
        $taskDueDate = oecrm_project_clean_date($taskDueDate);
        $projectDeadline = oecrm_project_clean_date($projectDeadline);
        return $taskDueDate !== null && $projectDeadline !== null && strtotime($taskDueDate) > strtotime($projectDeadline);
    }
}

if (!function_exists('oecrm_project_deadline_extend')) {
    function oecrm_project_deadline_extend(
        mysqli $conn,
        int $companyId,
        int $projectId,
        ?int $taskId,
        ?string $oldDeadline,
        string $newDeadline,
        string $reason,
        float $scopeValue,
        int $actorId,
        string $changeType = 'extra_task'
    ): void {
        oecrm_project_deadline_ensure_schema($conn);
        $oldDeadline = oecrm_project_clean_date($oldDeadline);
        $newDeadline = oecrm_project_clean_date($newDeadline);
        if ($newDeadline === null) {
            throw new RuntimeException('Please select a valid project deadline.');
        }
        if ($oldDeadline !== null && strtotime($newDeadline) <= strtotime($oldDeadline)) {
            throw new RuntimeException('New project deadline must be later than current project deadline.');
        }
        if (trim($reason) === '') {
            throw new RuntimeException('Deadline extension reason is required.');
        }
        $scopeValue = max(0, $scopeValue);

        $stmt = mysqli_prepare($conn, 'UPDATE projectstbl SET enddate=?, current_deadline=?, deadline_extended_count=deadline_extended_count+1, extra_scope_value=extra_scope_value+? WHERE id=? AND company_id=?');
        mysqli_stmt_bind_param($stmt, 'ssdii', $newDeadline, $newDeadline, $scopeValue, $projectId, $companyId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $status = 'approved';
        $approvedAt = date('Y-m-d H:i:s');
        $stmt = mysqli_prepare($conn, 'INSERT INTO project_deadline_history(company_id,project_id,task_id,old_deadline,new_deadline,reason,change_type,status,scope_value,requested_by,approved_by,approved_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)');
        mysqli_stmt_bind_param($stmt, 'iiisssssdiis', $companyId, $projectId, $taskId, $oldDeadline, $newDeadline, $reason, $changeType, $status, $scopeValue, $actorId, $actorId, $approvedAt);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

if (!function_exists('oecrm_project_deadline_format')) {
    function oecrm_project_deadline_format($date): string
    {
        $date = oecrm_project_clean_date($date);
        return $date ? date('d M Y', strtotime($date)) : '-';
    }
}
