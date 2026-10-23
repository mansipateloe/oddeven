<?php
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_admin_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method not allowed.');
}
oecrm_require_csrf();
oecrm_require_permission($conn, 'employees', 'edit');

$id = oecrm_int_param($_POST, 'id');
$name = trim($_POST['designation'] ?? '');
$old = null;
$saved = false;
if ($name === '') {
    $_SESSION['designation_flash'] = 'Designation is required.';
} else {
    mysqli_begin_transaction($conn);
    try {
        $stmt = mysqli_prepare($conn, 'SELECT id, designation FROM designation WHERE id=? FOR UPDATE');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $old = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        if (!$old) {
            throw new RuntimeException('Designation not found.');
        }
        $stmt = mysqli_prepare($conn, 'SELECT id FROM designation WHERE LOWER(TRIM(designation))=LOWER(?) AND id<>? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 'si', $name, $id);
        mysqli_stmt_execute($stmt);
        $duplicate = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        if ($duplicate) {
            throw new RuntimeException('Designation already exists.');
        }
        $stmt = mysqli_prepare($conn, 'UPDATE designation SET designation=? WHERE id=?');
        mysqli_stmt_bind_param($stmt, 'si', $name, $id);
        if (!mysqli_stmt_execute($stmt)) {
            throw new RuntimeException('Designation could not be updated.');
        }
        mysqli_stmt_close($stmt);
        // Employee assignments store the designation name rather than its ID.
        $stmt = mysqli_prepare($conn, 'UPDATE employeestbl SET designation=? WHERE designation=?');
        mysqli_stmt_bind_param($stmt, 'ss', $name, $old['designation']);
        if (!mysqli_stmt_execute($stmt)) {
            throw new RuntimeException('Employee designations could not be updated.');
        }
        mysqli_stmt_close($stmt);
        mysqli_commit($conn);
        $saved = true;
        $_SESSION['designation_flash'] = 'Designation updated successfully.';
    } catch (mysqli_sql_exception $exception) {
        mysqli_rollback($conn);
        $_SESSION['designation_flash'] = 'Designation could not be updated.';
    } catch (RuntimeException $exception) {
        mysqli_rollback($conn);
        $_SESSION['designation_flash'] = $exception->getMessage();
    }
}
if ($saved) {
    oecrm_audit($conn, 'employees', 'update', 'designation', $id, 'Designation updated', $old, ['designation' => $name]);
}
header('Location: manageDesignation.php' . (!$saved && $id > 0 ? '?edit=' . $id : ''));
exit;
