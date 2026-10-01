<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_admin_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

oecrm_require_csrf();
$companyId = oecrm_current_company_id($conn);
$noticeId = (int) ($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($noticeId <= 0 || !in_array($action, ['approve', 'reject', 'delete'], true)) {
    $_SESSION['employee_share_error'] = 'Invalid request.';
    header('Location: employeeShareApprovals.php');
    exit;
}

oecrm_require_permission($conn, 'notices', 'edit');

if ($action === 'delete') {
    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM notices
         WHERE id=? AND company_id=? AND notice_type='employee_share'"
    );
    if (!$stmt) {
        $_SESSION['employee_share_error'] = 'Unable to delete the employee update. Please try again.';
    } else {
        mysqli_stmt_bind_param($stmt, 'ii', $noticeId, $companyId);
        if (!mysqli_stmt_execute($stmt)) {
            $_SESSION['employee_share_error'] = 'Unable to delete the employee update. Please try again.';
        } elseif (mysqli_stmt_affected_rows($stmt) !== 1) {
            $_SESSION['employee_share_error'] = 'Employee update not found.';
        } else {
            $_SESSION['employee_share_flash'] = 'Employee update deleted.';
        }
        mysqli_stmt_close($stmt);
    }

    header('Location: employeeShareApprovals.php');
    exit;
}

$status = $action === 'approve' ? 'published' : 'rejected';
$publishAt = $action === 'approve' ? date('Y-m-d H:i:s') : null;

if ($action === 'approve') {
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE notices
         SET status=?, publish_at=?, published_at=?
         WHERE id=? AND company_id=? AND notice_type='employee_share' AND status='pending_review'"
    );
} else {
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE notices
         SET status=?
         WHERE id=? AND company_id=? AND notice_type='employee_share' AND status='pending_review'"
    );
}

if (!$stmt) {
    $_SESSION['employee_share_error'] = 'Unable to update the employee submission. Please try again.';
} else {
    if ($action === 'approve') {
        mysqli_stmt_bind_param($stmt, 'sssii', $status, $publishAt, $publishAt, $noticeId, $companyId);
    } else {
        mysqli_stmt_bind_param($stmt, 'sii', $status, $noticeId, $companyId);
    }

    if (!mysqli_stmt_execute($stmt)) {
        $_SESSION['employee_share_error'] = 'Unable to update the employee submission. Please try again.';
    } elseif (mysqli_stmt_affected_rows($stmt) !== 1) {
        $_SESSION['employee_share_error'] = 'This submission was not found or has already been reviewed.';
    } else {
        $_SESSION['employee_share_flash'] = $action === 'approve'
            ? 'Employee update approved and published.'
            : 'Employee update declined.';
    }
}

if ($stmt) {
    mysqli_stmt_close($stmt);
}

header('Location: employeeShareApprovals.php');
exit;
