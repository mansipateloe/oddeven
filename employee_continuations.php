<?php

function oecrm_ensure_employee_continuation_tables(mysqli $conn): void
{
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS employee_continuation_history (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        company_id INT NOT NULL,
        employee_id INT NOT NULL,
        term_no INT NOT NULL DEFAULT 1,
        previous_from DATE NULL,
        previous_to DATE NULL,
        renewed_from DATE NOT NULL,
        renewed_to DATE NOT NULL,
        renewal_date DATE NOT NULL,
        status VARCHAR(30) NOT NULL DEFAULT 'continued',
        new_designation VARCHAR(255) NULL,
        revised_salary VARCHAR(255) NULL,
        remarks TEXT NULL,
        letter_id BIGINT NULL,
        created_by INT NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_employee_continuation_employee (employee_id),
        INDEX idx_employee_continuation_company (company_id),
        INDEX idx_employee_continuation_letter (letter_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function oecrm_continuation_sql_date(?string $value): ?string
{
    $value = trim((string) $value);
    if ($value === '') {
        return null;
    }
    $timestamp = strtotime($value);
    return $timestamp ? date('Y-m-d', $timestamp) : null;
}

function oecrm_continuation_format_date(?string $value): string
{
    $date = oecrm_continuation_sql_date($value);
    return $date ? date('d M Y', strtotime($date)) : '-';
}

function oecrm_continuation_joining_date(array $employee): string
{
    return oecrm_continuation_sql_date($employee['joiningDate'] ?? '') ?: date('Y-m-d');
}

function oecrm_continuation_add_year_term(string $from): string
{
    $date = new DateTime($from);
    $date->modify('+1 year -1 day');
    return $date->format('Y-m-d');
}

function oecrm_continuation_next_day(string $date): string
{
    $next = new DateTime($date);
    $next->modify('+1 day');
    return $next->format('Y-m-d');
}

function oecrm_employee_latest_continuation(mysqli $conn, int $employeeId, int $companyId): ?array
{
    oecrm_ensure_employee_continuation_tables($conn);
    $stmt = mysqli_prepare($conn, 'SELECT * FROM employee_continuation_history WHERE employee_id=? AND company_id=? ORDER BY term_no DESC,id DESC LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'ii', $employeeId, $companyId);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $row ?: null;
}

function oecrm_employee_continuation_term(array $employee, ?array $latest): array
{
    $joining = oecrm_continuation_joining_date($employee);
    if ($latest && oecrm_continuation_sql_date($latest['renewed_from'] ?? '') && oecrm_continuation_sql_date($latest['renewed_to'] ?? '')) {
        $currentFrom = $latest['renewed_from'];
        $currentTo = $latest['renewed_to'];
        $currentTermNo = max(1, (int) ($latest['term_no'] ?? 1));
    } else {
        $currentFrom = $joining;
        $currentTo = oecrm_continuation_add_year_term($joining);
        $currentTermNo = 1;
    }

    $nextFrom = oecrm_continuation_next_day($currentTo);
    $nextTo = oecrm_continuation_add_year_term($nextFrom);

    return [
        'joining_date' => $joining,
        'current_from' => $currentFrom,
        'current_to' => $currentTo,
        'current_term_no' => $currentTermNo,
        'next_from' => $nextFrom,
        'next_to' => $nextTo,
        'next_term_no' => $currentTermNo + 1,
        'is_due' => strtotime($currentTo) <= strtotime(date('Y-m-d')),
    ];
}

function oecrm_continuation_default_template(): array
{
    $body = '<p>Dear {{employee_name}},</p>'
        . '<p>We are pleased to confirm continuation of your employment with <strong>{{company_name}}</strong>.</p>'
        . '<p>Your previous employment period was from <strong>{{previous_from}}</strong> to <strong>{{previous_to}}</strong>. Your employment is renewed for the next term from <strong>{{continuation_from}}</strong> to <strong>{{continuation_to}}</strong>.</p>'
        . '<p>Designation: <strong>{{new_designation}}</strong><br>Compensation: <strong>{{revised_salary}}</strong></p>'
        . '<p>All other terms and policies of employment remain unchanged unless specifically communicated in writing.</p>'
        . '<p>Sincerely,<br><strong>{{company_name}}</strong><br>Human Resources</p>';
    return [
        'subject' => 'Employment Continuation Letter - {{employee_name}}',
        'body' => $body,
    ];
}

function oecrm_ensure_continuation_letter_template(mysqli $conn, int $companyId, int $actor): int
{
    $preset = oecrm_continuation_default_template();
    $stmt = mysqli_prepare($conn, 'SELECT id FROM hr_letter_templates WHERE company_id=? AND letter_type="continuation" AND status=1 ORDER BY is_default DESC,id DESC LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'i', $companyId);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if ($row) {
        return (int) $row['id'];
    }

    $name = 'Standard Employment Continuation Letter';
    $type = 'continuation';
    $subject = $preset['subject'];
    $body = $preset['body'];
    $stmt = mysqli_prepare($conn, 'INSERT INTO hr_letter_templates(company_id,letter_type,name,subject,body_html,is_default,status,created_by) VALUES(?,?,?,?,?,1,1,?)');
    mysqli_stmt_bind_param($stmt, 'issssi', $companyId, $type, $name, $subject, $body, $actor);
    mysqli_stmt_execute($stmt);
    $id = (int) mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    return $id;
}

function oecrm_create_continuation_letter(mysqli $conn, int $companyId, array $employee, array $company, array $history, int $actor): int
{
    $templateId = oecrm_ensure_continuation_letter_template($conn, $companyId, $actor);
    $stmt = mysqli_prepare($conn, 'SELECT * FROM hr_letter_templates WHERE id=? AND company_id=?');
    mysqli_stmt_bind_param($stmt, 'ii', $templateId, $companyId);
    mysqli_stmt_execute($stmt);
    $template = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$template) {
        throw new RuntimeException('Continuation letter template not found.');
    }

    $designation = trim((string) ($history['new_designation'] ?? '')) ?: ($employee['designation'] ?? '');
    $salary = trim((string) ($history['revised_salary'] ?? '')) ?: ($employee['salary'] ?? 'As per company records');
    $extra = [
        'previous_from' => oecrm_continuation_format_date($history['previous_from'] ?? ''),
        'previous_to' => oecrm_continuation_format_date($history['previous_to'] ?? ''),
        'continuation_from' => oecrm_continuation_format_date($history['renewed_from'] ?? ''),
        'continuation_to' => oecrm_continuation_format_date($history['renewed_to'] ?? ''),
        'renewal_date' => oecrm_continuation_format_date($history['renewal_date'] ?? ''),
        'term_no' => (string) ($history['term_no'] ?? ''),
        'new_designation' => $designation,
        'revised_salary' => $salary,
        'reason' => trim((string) ($history['remarks'] ?? '')),
    ];

    $reference = oecrm_letter_reference($conn, $companyId, 'continuation');
    $subject = oecrm_render_letter($template['subject'], $employee, $company, $extra);
    $body = oecrm_render_letter($template['body_html'], $employee, $company, $extra);
    $email = $employee['companyEmail'] ?: $employee['personalEmail'];
    $issueDate = $history['renewal_date'] ?: date('Y-m-d');
    $type = 'continuation';

    $employeeId = (int) $employee['id'];
    $stmt = mysqli_prepare($conn, 'INSERT INTO hr_letters(company_id,employee_id,template_id,letter_type,reference_no,subject,body_html,issue_date,recipient_email,created_by) VALUES(?,?,?,?,?,?,?,?,?,?)');
    mysqli_stmt_bind_param($stmt, 'iiissssssi', $companyId, $employeeId, $templateId, $type, $reference, $subject, $body, $issueDate, $email, $actor);
    mysqli_stmt_execute($stmt);
    $letterId = (int) mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    return $letterId;
}

