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
$actor = (int) $_SESSION["adminId"];
$action = $_POST["action"] ?? "";
$id = (int) ($_POST["id"] ?? 0);
try {
    if ($action === "save") {
        oecrm_require_permission(
            $conn,
            "access_management",
            $id ? "edit" : "create"
        );
        $type = $_POST["service_type"] ?? "other";
        $name = trim($_POST["service_name"] ?? "");
        $url = trim($_POST["login_url"] ?? "");
        $username = trim($_POST["account_username"] ?? "");
        $email = trim($_POST["owner_email"] ?? "");
        $status = $_POST["status"] ?? "active";
        $notes = trim($_POST["notes"] ?? "");
        if ($name === "") {
            throw new RuntimeException("Service name is required.");
        }
        if ($id) {
            $s = mysqli_prepare(
                $conn,
                "UPDATE access_accounts SET service_type=?,service_name=?,login_url=?,account_username=?,owner_email=?,status=?,notes=? WHERE id=? AND company_id=?"
            );
            mysqli_stmt_bind_param(
                $s,
                "sssssssii",
                $type,
                $name,
                $url,
                $username,
                $email,
                $status,
                $notes,
                $id,
                $companyId
            );
        } else {
            $s = mysqli_prepare(
                $conn,
                "INSERT INTO access_accounts(company_id,service_type,service_name,login_url,account_username,owner_email,status,notes,created_by) VALUES(?,?,?,?,?,?,?,?,?)"
            );
            mysqli_stmt_bind_param(
                $s,
                "isssssssi",
                $companyId,
                $type,
                $name,
                $url,
                $username,
                $email,
                $status,
                $notes,
                $actor
            );
        }
        mysqli_stmt_execute($s);
        if (!$id) {
            $id = mysqli_insert_id($conn);
        }
        mysqli_stmt_close($s);
        mysqli_query(
            $conn,
            "INSERT INTO access_history(company_id,account_id,event_type,details,performed_by) VALUES($companyId,$id,'" .
                ($id ? "updated" : "created") .
                "','Account saved',$actor)"
        );
        oecrm_audit(
            $conn,
            "access_management",
            "save",
            "access_account",
            $id,
            "Access account saved"
        );
    } elseif ($action === "assign") {
        oecrm_require_permission($conn, "access_management", "assign");
        $employee = (int) ($_POST["employee_id"] ?? 0);
        $level = $_POST["access_level"] ?? "member";
        $expires = trim($_POST["expires_on"] ?? "") ?: null;
        $s = mysqli_prepare(
            $conn,
            "INSERT INTO access_assignments(company_id,account_id,employee_id,access_level,assigned_on,expires_on,assigned_by) VALUES(?,?,?, ?,CURDATE(),?,?)"
        );
        mysqli_stmt_bind_param(
            $s,
            "iiissi",
            $companyId,
            $id,
            $employee,
            $level,
            $expires,
            $actor
        );
        mysqli_stmt_execute($s);
        mysqli_stmt_close($s);
        mysqli_query(
            $conn,
            "INSERT INTO access_history(company_id,account_id,employee_id,event_type,details,performed_by) VALUES($companyId,$id,$employee,'assigned','Access assigned',$actor)"
        );
        oecrm_audit(
            $conn,
            "access_management",
            "assign",
            "access_account",
            $id,
            "Employee access assigned",
            null,
            ["employee_id" => $employee]
        );
    } elseif ($action === "revoke") {
        oecrm_require_permission($conn, "access_management", "revoke");
        $assignment = (int) ($_POST["assignment_id"] ?? 0);
        $row = mysqli_fetch_assoc(
            mysqli_query(
                $conn,
                "SELECT * FROM access_assignments WHERE id=" .
                    $assignment .
                    " AND account_id=" .
                    $id .
                    " AND company_id=" .
                    $companyId .
                    ' AND status="active"'
            )
        );
        if (!$row) {
            throw new RuntimeException("Active assignment not found.");
        }
        mysqli_query(
            $conn,
            "UPDATE access_assignments SET status='revoked',revoked_on=CURDATE(),revoked_by=$actor WHERE id=$assignment"
        );
        mysqli_query(
            $conn,
            "INSERT INTO access_history(company_id,account_id,employee_id,event_type,details,performed_by) VALUES($companyId,$id," .
                (int) $row["employee_id"] .
                ",'revoked','Access revoked',$actor)"
        );
        oecrm_audit(
            $conn,
            "access_management",
            "revoke",
            "access_account",
            $id,
            "Employee access revoked",
            null,
            ["employee_id" => $row["employee_id"]]
        );
    } else {
        throw new RuntimeException("Invalid action.");
    }
    $_SESSION["access_flash"] = "Access information updated.";
    header("Location: accessAccount.php?id=" . $id);
    exit();
} catch (Throwable $e) {
    $_SESSION["access_error"] = $e->getMessage();
    header(
        "Location: " .
            ($id ? "accessAccount.php?id=" . $id : "accessAccount.php")
    );
    exit();
}