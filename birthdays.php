<?php
require_once __DIR__ . '/notices.php';

function oecrm_employee_birthdays($conn, $companyId, $days = 30)
{
    $companyId = (int) $companyId;
    $days = max(0, (int) $days);
    $result = mysqli_query($conn, "SELECT id,employeeCode,name,birthdate,designation FROM employeestbl WHERE company_id=$companyId AND status=0 AND birthdate REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' ORDER BY name");
    $today = new DateTime('today', new DateTimeZone('Asia/Kolkata'));
    $birthdays = [];
    while ($result && ($employee = mysqli_fetch_assoc($result))) {
        $birthDate = DateTime::createFromFormat('!Y-m-d', $employee['birthdate']);
        if (!$birthDate) continue;
        $nextBirthday = DateTime::createFromFormat('!Y-m-d', $today->format('Y') . '-' . $birthDate->format('m-d'));
        if ($nextBirthday < $today) $nextBirthday->modify('+1 year');
        $daysUntil = (int) $today->diff($nextBirthday)->format('%a');
        if ($daysUntil > $days) continue;
        $employee['days_until'] = $daysUntil;
        $employee['birthday_date'] = $nextBirthday->format('Y-m-d');
        $employee['age'] = (int) $nextBirthday->format('Y') - (int) $birthDate->format('Y');
        $birthdays[] = $employee;
    }
    usort($birthdays, static function ($left, $right) {
        return $left['days_until'] <=> $right['days_until'] ?: strcasecmp($left['name'], $right['name']);
    });
    return $birthdays;
}

function oecrm_send_birthday_wish($conn, $companyId, $employeeId, $actorId, $message = '')
{
    $companyId = (int) $companyId;
    $employeeId = (int) $employeeId;
    $actorId = (int) $actorId;
    $stmt = mysqli_prepare($conn, 'SELECT id,name,birthdate FROM employeestbl WHERE id=? AND company_id=? AND status=0');
    mysqli_stmt_bind_param($stmt, 'ii', $employeeId, $companyId);
    mysqli_stmt_execute($stmt);
    $employee = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$employee) throw new RuntimeException('Active employee not found.');

    $title = 'Happy Birthday, ' . $employee['name'] . '!';
    $safeTitle = mysqli_real_escape_string($conn, $title);
    $existing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT n.id FROM notices n JOIN notice_targets t ON t.notice_id=n.id WHERE n.company_id=$companyId AND n.notice_type='hr' AND n.title='$safeTitle' AND t.employee_id=$employeeId AND YEAR(n.publish_at)=YEAR(CURDATE()) AND n.status<>'cancelled' LIMIT 1"));
    if ($existing) {
        $noticeId = (int) $existing['id'];
        $birthdayMonthDay = '';
        if (!empty($employee['birthdate']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $employee['birthdate'])) {
            $birthdayMonthDay = date('m-d', strtotime($employee['birthdate']));
        }
        if ($birthdayMonthDay !== date('m-d')) {
            mysqli_query($conn, "UPDATE notices SET status='expired',expires_at=LEAST(COALESCE(expires_at,NOW()),NOW()) WHERE id=$noticeId AND company_id=$companyId");
            return ['notice_id'=>$noticeId, 'created'=>false, 'personalized'=>false, 'employee'=>$employee, 'expired'=>true];
        }
        if (trim($message) !== '') {
            $body = '<p>' . nl2br(oecrm_h($message)) . '</p><p>Best wishes from the Management Team.</p>';
            $stmt = mysqli_prepare($conn, 'UPDATE notices SET body_html=?,show_popup=1,priority="high",status="published",publish_at=NOW(),published_at=COALESCE(published_at,NOW()),expires_at=CONCAT(CURDATE()," 23:59:59") WHERE id=? AND company_id=?');
            mysqli_stmt_bind_param($stmt, 'sii', $body, $noticeId, $companyId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $stmt = mysqli_prepare($conn, 'DELETE FROM notice_receipts WHERE notice_id=? AND employee_id=?');
            mysqli_stmt_bind_param($stmt, 'ii', $noticeId, $employeeId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return ['notice_id'=>$noticeId, 'created'=>false, 'personalized'=>true, 'employee'=>$employee];
        }
        $stmt = mysqli_prepare($conn, 'UPDATE notices SET show_popup=1,priority="high",status="published",publish_at=NOW(),published_at=NOW(),expires_at=CONCAT(CURDATE()," 23:59:59") WHERE id=? AND company_id=?');
        mysqli_stmt_bind_param($stmt, 'ii', $noticeId, $companyId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return ['notice_id'=>$noticeId, 'created'=>false, 'personalized'=>false, 'employee'=>$employee];
    }

    $defaultMessage = '<p>Dear <strong>' . oecrm_h($employee['name']) . '</strong>,</p><p>Wishing you a very Happy Birthday! May your year be filled with happiness, good health and success.</p><p>Best wishes from the Management Team.</p>';
    $body = trim($message) !== '' ? '<p>' . nl2br(oecrm_h($message)) . '</p>' : $defaultMessage;
    $category = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM notice_categories WHERE company_id=$companyId AND status=1 ORDER BY id LIMIT 1"));
    $categoryId = (int) ($category['id'] ?? 0);
    $publish = date('Y-m-d H:i:s');
    $expires = date('Y-m-d 23:59:59');
    $status = 'published';
    $priority = 'high';
    $audience = 'employee';
    $type = 'hr';
    $popup = 1;
    $ack = 0;
    mysqli_begin_transaction($conn);
    try {
        $stmt = mysqli_prepare($conn, 'INSERT INTO notices(company_id,notice_type,category_id,title,body_html,priority,audience_type,publish_at,expires_at,show_popup,acknowledgement_required,status,created_by,published_at) VALUES(?,?,?,?,?,?,?,?,?,?,?, ?,?,NOW())');
        mysqli_stmt_bind_param($stmt, 'isissssssiisi', $companyId, $type, $categoryId, $title, $body, $priority, $audience, $publish, $expires, $popup, $ack, $status, $actorId);
        mysqli_stmt_execute($stmt);
        $noticeId = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        $stmt = mysqli_prepare($conn, 'INSERT INTO notice_targets(notice_id,employee_id) VALUES(?,?)');
        mysqli_stmt_bind_param($stmt, 'ii', $noticeId, $employeeId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_commit($conn);
    } catch (Throwable $exception) {
        mysqli_rollback($conn);
        throw $exception;
    }
    return ['notice_id'=>$noticeId, 'created'=>true, 'personalized'=>trim($message) !== '', 'employee'=>$employee];
}

function oecrm_auto_send_birthday_wishes($conn, $companyId, $date = null)
{
    $companyId = (int) $companyId;
    if ($companyId <= 0) return [];
    $date = $date ?: date('Y-m-d');
    $dateObject = DateTime::createFromFormat('!Y-m-d', $date);
    if (!$dateObject) return [];
    $monthDay = $dateObject->format('m-d');
    $safeMonthDay = mysqli_real_escape_string($conn, $monthDay);
    $employees = mysqli_query($conn, "SELECT id FROM employeestbl WHERE company_id=$companyId AND status=0 AND birthdate REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' AND DATE_FORMAT(STR_TO_DATE(birthdate,'%Y-%m-%d'),'%m-%d')='$safeMonthDay'");
    $sent = [];
    while ($employees && ($employee = mysqli_fetch_assoc($employees))) {
        $result = oecrm_send_birthday_wish($conn, $companyId, (int) $employee['id'], 0);
        if (!$result['created']) continue;
        oecrm_notice_event($conn, $result['notice_id'], (int) $employee['id'], 'system', 0, 'birthday_wish_auto', 'Automatic birthday wish sent by CRM');
        oecrm_write_audit($conn, $companyId, 'system', 0, (int) $employee['id'], 'notices', 'birthday_wish_auto', 'notice', $result['notice_id'], 'Automatic birthday wish sent', null, ['employee_id'=>(int) $employee['id']]);
        $sent[] = $result;
    }
    return $sent;
}
