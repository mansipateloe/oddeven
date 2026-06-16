<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';
require_once __DIR__ . '/../birthdays.php';
oecrm_require_admin_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
oecrm_require_csrf();
oecrm_require_permission($conn, 'notices', 'create');
$companyId = oecrm_current_company_id($conn);
$employeeId = (int) ($_POST['employee_id'] ?? 0);
$message = trim($_POST['message'] ?? '');
try {
    $employee = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT id,name,birthdate FROM employeestbl WHERE id=' . $employeeId . ' AND company_id=' . (int) $companyId . ' AND status=0'));
    if (!$employee || date('m-d', strtotime($employee['birthdate'])) !== date('m-d')) throw new RuntimeException('Birthday wish can only be sent on the employee birthday.');
    $result = oecrm_send_birthday_wish($conn, $companyId, $employeeId, (int) $_SESSION['adminId'], $message);
    $event = !empty($result['personalized']) ? 'birthday_wish_personalized' : ($result['created'] ? 'birthday_wish' : 'birthday_wish_duplicate');
    oecrm_notice_event($conn, $result['notice_id'], $employeeId, 'admin', (int) $_SESSION['adminId'], $event, 'Birthday wish sent to employee');
    oecrm_audit($conn, 'notices', 'birthday_wish', 'notice', $result['notice_id'], 'Birthday wish sent', null, ['employee_id'=>$employeeId]);
    $_SESSION['birthday_flash'] = !empty($result['personalized'])
        ? 'Personal birthday message sent to ' . $result['employee']['name'] . '.'
        : ($result['created'] ? 'Birthday wish sent to ' . $result['employee']['name'] . '.' : 'Birthday wish was already sent this year.');
} catch (Throwable $exception) {
    $_SESSION['birthday_error'] = $exception->getMessage();
}
header('Location: birthdayCenter.php');
exit;
