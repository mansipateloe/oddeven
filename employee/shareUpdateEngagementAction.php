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
$noticeId = (int) ($_POST['notice_id'] ?? 0);
$action = $_POST['action'] ?? '';
$redirectUrl = 'home.php#employee-share-feed';

if ($employeeId <= 0 || $noticeId <= 0 || !in_array($action, ['like', 'unlike', 'comment'], true)) {
    $_SESSION['employee_share_feed_error'] = 'Invalid update request.';
    header('Location: ' . $redirectUrl);
    exit;
}

$companyStmt = mysqli_prepare($conn, 'SELECT company_id FROM employeestbl WHERE id=? LIMIT 1');
if (!$companyStmt) {
    $_SESSION['employee_share_feed_error'] = 'Unable to load this update right now.';
    header('Location: ' . $redirectUrl);
    exit;
}
mysqli_stmt_bind_param($companyStmt, 'i', $employeeId);
mysqli_stmt_execute($companyStmt);
mysqli_stmt_bind_result($companyStmt, $companyId);
$hasEmployee = mysqli_stmt_fetch($companyStmt);
mysqli_stmt_close($companyStmt);

if (!$hasEmployee) {
    $_SESSION['employee_share_feed_error'] = 'Employee account not found.';
    header('Location: ' . $redirectUrl);
    exit;
}

$companyId = (int) $companyId;
$postStmt = mysqli_prepare(
    $conn,
    "SELECT id FROM notices
     WHERE id=? AND company_id=? AND notice_type='employee_share' AND status='published'
       AND publish_at<=NOW() AND (expires_at IS NULL OR expires_at>=NOW())
     LIMIT 1"
);
if (!$postStmt) {
    $_SESSION['employee_share_feed_error'] = 'Unable to load this update right now.';
    header('Location: ' . $redirectUrl);
    exit;
}
mysqli_stmt_bind_param($postStmt, 'ii', $noticeId, $companyId);
mysqli_stmt_execute($postStmt);
mysqli_stmt_store_result($postStmt);
$isAvailable = mysqli_stmt_num_rows($postStmt) === 1;
mysqli_stmt_close($postStmt);

if (!$isAvailable) {
    $_SESSION['employee_share_feed_error'] = 'This update is no longer available.';
    header('Location: ' . $redirectUrl);
    exit;
}

if ($action === 'like') {
    $stmt = mysqli_prepare($conn, 'INSERT IGNORE INTO employee_share_likes(notice_id,employee_id) VALUES(?,?)');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ii', $noticeId, $employeeId);
    }
} elseif ($action === 'unlike') {
    $stmt = mysqli_prepare($conn, 'DELETE FROM employee_share_likes WHERE notice_id=? AND employee_id=?');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ii', $noticeId, $employeeId);
    }
} else {
    $comment = trim((string) ($_POST['comment'] ?? ''));
    $commentLength = function_exists('mb_strlen') ? mb_strlen($comment, 'UTF-8') : strlen($comment);
    if ($comment === '' || $commentLength > 500) {
        $_SESSION['employee_share_feed_error'] = 'Comments must contain between 1 and 500 characters.';
        header('Location: ' . $redirectUrl);
        exit;
    }
    $stmt = mysqli_prepare($conn, 'INSERT INTO employee_share_comments(notice_id,employee_id,comment_text) VALUES(?,?,?)');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'iis', $noticeId, $employeeId, $comment);
    }
}

if (!$stmt || !mysqli_stmt_execute($stmt)) {
    $_SESSION['employee_share_feed_error'] = 'Unable to save your response. Please try again.';
} else {
    $_SESSION['employee_share_feed_flash'] = $action === 'comment'
        ? 'Your comment was posted.'
        : ($action === 'like' ? 'You liked this update.' : 'Your like was removed.');
}

if ($stmt) {
    mysqli_stmt_close($stmt);
}
header('Location: ' . $redirectUrl);
exit;
