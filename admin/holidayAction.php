<?php
require_once __DIR__ . "/dbconnect.php";
require_once __DIR__ . "/../security.php";
require_once __DIR__ . "/../foundation.php";

oecrm_require_admin_login();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit();
}

oecrm_require_csrf();

$companyId = oecrm_current_company_id($conn);
$id = (int) ($_POST["id"] ?? 0);

if (($_POST["action"] ?? "") === "delete") {
    oecrm_require_permission($conn, "attendance", "correct");

    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM holidaytbl WHERE id=? AND company_id=?"
    );
    mysqli_stmt_bind_param($stmt, "ii", $id, $companyId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $_SESSION["holiday_flash"] = "Holiday removed.";
    header("Location: manageHoliday.php");
    exit();
}

oecrm_require_permission($conn, "attendance", "correct");

$date = trim((string) ($_POST["holiday_date"] ?? ""));
$title = trim((string) ($_POST["title"] ?? ""));
$errors = [];

if ($date === "" || !strtotime($date)) {
    $errors["holiday_date"] = "Holiday date is required.";
}

if ($title === "") {
    $errors["holiday_title"] = "Holiday title is required.";
}

if ($errors) {
    $_SESSION["holiday_errors"] = $errors;
    $_SESSION["holiday_date_value"] = $date;
    $_SESSION["holiday_title_value"] = $title;
    header("Location: manageHoliday.php");
    exit();
}

if ($id) {
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE holidaytbl SET holidayDate=?, holidayTitle=? WHERE id=? AND company_id=?"
    );
    mysqli_stmt_bind_param($stmt, "ssii", $date, $title, $id, $companyId);
} else {
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO holidaytbl(company_id, holidayDate, holidayTitle) VALUES(?,?,?)"
    );
    mysqli_stmt_bind_param($stmt, "iss", $companyId, $date, $title);
}

mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

unset(
    $_SESSION["holiday_errors"],
    $_SESSION["holiday_date_value"],
    $_SESSION["holiday_title_value"]
);
$_SESSION["holiday_flash"] = "Holiday saved.";
header("Location: manageHoliday.php");
exit();
