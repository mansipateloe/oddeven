<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_employee_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

oecrm_require_csrf();

$employeeId = (int) ($_SESSION['employeeId'] ?? 0);
$companyId = (int) (mysqli_fetch_assoc(mysqli_query($conn, 'SELECT company_id FROM employeestbl WHERE id=' . $employeeId))['company_id'] ?? 0);

$title = trim($_POST['title'] ?? '');
$body = trim($_POST['body_html'] ?? '');
$shareType = in_array($_POST['share_type'] ?? '', ['announcement', 'poll', 'skill_share'], true) ? $_POST['share_type'] : 'announcement';

if ($employeeId <= 0 || $companyId <= 0 || $title === '' || $body === '') {
    $_SESSION['employee_share_error'] = 'Title and content are required.';
    header('Location: shareUpdate.php');
    exit;
}

$displayBody = '<p><strong>Shared by:</strong> ' . oecrm_h($_SESSION['employeeName'] ?? 'Employee') . '</p>'
    . '<p><strong>Type:</strong> ' . oecrm_h(ucwords(str_replace('_', ' ', $shareType))) . '</p>'
    . '<div>' . nl2br(oecrm_h($body)) . '</div>';

$noticeType = 'employee_share';
$priority = 'normal';
$audience = 'all';
$status = 'pending_review';

$stmt = mysqli_prepare(
    $conn,
    'INSERT INTO notices(company_id, notice_type, title, body_html, priority, audience_type, publish_at, status, created_by, published_at)
     VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?, NULL)'
);
mysqli_stmt_bind_param($stmt, 'isssssis', $companyId, $noticeType, $title, $displayBody, $priority, $audience, $status, $employeeId);

if (!mysqli_stmt_execute($stmt)) {
    $_SESSION['employee_share_error'] = 'Unable to submit the update right now.';
    mysqli_stmt_close($stmt);
    header('Location: shareUpdate.php');
    exit;
}

mysqli_stmt_close($stmt);
$_SESSION['employee_share_flash'] = 'Your update has been submitted and is waiting for admin approval.';
header('Location: shareUpdate.php');
exit;
