<?php
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/../../security.php';
require_once __DIR__ . '/../../foundation.php';

oecrm_require_admin_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location:../projectExpense.php');
    exit;
}

if (!oecrm_verify_csrf()) {
    $_SESSION['project_expense_error'] = 'Invalid or expired security token. Please refresh the page and try again.';
    header('Location:../projectExpense.php');
    exit;
}

function oecrm_project_expense_redirect(string $message = '', bool $isError = false, int $editId = 0): void
{
    if ($message !== '') {
        $_SESSION[$isError ? 'project_expense_error' : 'project_expense_flash'] = $message;
    }

    $url = '../projectExpense.php';
    if ($editId > 0) {
        $url .= '?edit=' . $editId;
    }

    header('Location:' . $url);
    exit;
}

function oecrm_project_expense_post_int(string $key): int
{
    $value = $_POST[$key] ?? 0;
    return preg_match('/^\d+$/', (string) $value) ? (int) $value : 0;
}

function oecrm_project_expense_columns(mysqli $conn): array
{
    $columns = [];
    $result = mysqli_query($conn, 'SHOW COLUMNS FROM project_expenses');

    if (!$result) {
        throw new RuntimeException('Project expenses table is not available.');
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $columns[$row['Field']] = $row['Field'];
        $columns[strtolower($row['Field'])] = $row['Field'];
    }

    return $columns;
}

function oecrm_project_expense_col(array $columns, array $names): string
{
    foreach ($names as $name) {
        if (isset($columns[$name])) {
            return $columns[$name];
        }

        $lower = strtolower($name);
        if (isset($columns[$lower])) {
            return $columns[$lower];
        }
    }

    return '';
}

function oecrm_project_expense_ident(string $name): string
{
    return '`' . str_replace('`', '``', $name) . '`';
}

function oecrm_project_expense_valid_date(string $date): bool
{
    $value = DateTime::createFromFormat('!Y-m-d', $date);
    return $value && $value->format('Y-m-d') === $date;
}

function oecrm_project_expense_require_map(array $columns): array
{
    $map = [
        'date' => oecrm_project_expense_col($columns, ['expensedate', 'expense_date']),
        'project' => oecrm_project_expense_col($columns, ['project_Id', 'project_id']),
        'title' => oecrm_project_expense_col($columns, ['expense_title', 'title']),
        'amount' => oecrm_project_expense_col($columns, ['amount', 'expense_amount']),
        'account' => oecrm_project_expense_col($columns, ['account_Id', 'account_id']),
        'remark' => oecrm_project_expense_col($columns, ['remark', 'description', 'notes']),
        'company' => oecrm_project_expense_col($columns, ['company_id']),
    ];

    foreach (['date', 'project', 'title', 'amount', 'account', 'remark'] as $required) {
        if ($map[$required] === '') {
            throw new RuntimeException('Project expense table schema is incomplete. Missing ' . $required . ' column.');
        }
    }

    return $map;
}

try {
    $companyId = oecrm_current_company_id($conn);
    $columns = oecrm_project_expense_columns($conn);
    $map = oecrm_project_expense_require_map($columns);
    $action = $_POST['action'] ?? '';

    if (isset($_POST['add_project_expense'])) {
        $action = 'add';
    } elseif (isset($_POST['update_project_expense'])) {
        $action = 'update';
    } elseif (isset($_POST['delete_project_expense'])) {
        $action = 'delete';
    }

    if ($action === 'delete') {
        $id = oecrm_project_expense_post_int('id');

        if ($id <= 0) {
            throw new RuntimeException('Invalid project expense selected.');
        }

        if ($map['company'] !== '') {
            $stmt = mysqli_prepare($conn, 'DELETE FROM project_expenses WHERE id=? AND ' . oecrm_project_expense_ident($map['company']) . '=?');
            mysqli_stmt_bind_param($stmt, 'ii', $id, $companyId);
        } else {
            $stmt = mysqli_prepare($conn, 'DELETE pe FROM project_expenses pe INNER JOIN projectstbl p ON p.id=pe.' . oecrm_project_expense_ident($map['project']) . ' WHERE pe.id=? AND p.company_id=?');
            mysqli_stmt_bind_param($stmt, 'ii', $id, $companyId);
        }

        if (!$stmt || !mysqli_stmt_execute($stmt)) {
            throw new RuntimeException('Unable to delete project expense.');
        }

        mysqli_stmt_close($stmt);
        oecrm_project_expense_redirect('Project expense deleted successfully.');
    }

    $id = oecrm_project_expense_post_int('id');
    $expensedate = trim((string) ($_POST['expensedate'] ?? ''));
    $projectId = (int) ($_POST['project_Id'] ?? $_POST['project_id'] ?? 0);
    $expenseTitle = trim((string) ($_POST['expense_title'] ?? ''));
    $amount = trim((string) ($_POST['amount'] ?? ''));
    $accountId = (int) ($_POST['account_Id'] ?? $_POST['account_id'] ?? 0);
    $remark = trim((string) ($_POST['remark'] ?? ''));

    if (!in_array($action, ['add', 'update'], true)) {
        throw new RuntimeException('Invalid project expense action.');
    }

    if ($action === 'update' && $id <= 0) {
        throw new RuntimeException('Invalid project expense selected.');
    }

    if (!oecrm_project_expense_valid_date($expensedate)) {
        throw new RuntimeException('Please enter a valid expense date.');
    }

    if ($projectId <= 0 || $expenseTitle === '' || $amount === '' || !is_numeric($amount) || (float) $amount < 0 || $accountId <= 0) {
        throw new RuntimeException('Date, project, expense title, valid amount and account are required.');
    }

    $projectStmt = mysqli_prepare($conn, 'SELECT id FROM projectstbl WHERE id=? AND company_id=? LIMIT 1');
    mysqli_stmt_bind_param($projectStmt, 'ii', $projectId, $companyId);
    mysqli_stmt_execute($projectStmt);
    $projectResult = mysqli_stmt_get_result($projectStmt);

    if (!mysqli_fetch_assoc($projectResult)) {
        throw new RuntimeException('Selected project is invalid for active company.');
    }

    mysqli_stmt_close($projectStmt);

    $accountStmt = mysqli_prepare($conn, 'SELECT account_id FROM account WHERE account_id=? LIMIT 1');
    mysqli_stmt_bind_param($accountStmt, 'i', $accountId);
    mysqli_stmt_execute($accountStmt);
    $accountResult = mysqli_stmt_get_result($accountStmt);

    if (!mysqli_fetch_assoc($accountResult)) {
        throw new RuntimeException('Selected account is invalid.');
    }

    mysqli_stmt_close($accountStmt);

    $amountValue = number_format((float) $amount, 2, '.', '');

    if ($action === 'update') {
        $sql = 'UPDATE project_expenses SET '
            . oecrm_project_expense_ident($map['date']) . '=?, '
            . oecrm_project_expense_ident($map['project']) . '=?, '
            . oecrm_project_expense_ident($map['title']) . '=?, '
            . oecrm_project_expense_ident($map['amount']) . '=?, '
            . oecrm_project_expense_ident($map['account']) . '=?, '
            . oecrm_project_expense_ident($map['remark']) . '=? WHERE id=?';

        if ($map['company'] !== '') {
            $sql .= ' AND ' . oecrm_project_expense_ident($map['company']) . '=?';
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'sissisii', $expensedate, $projectId, $expenseTitle, $amountValue, $accountId, $remark, $id, $companyId);
        } else {
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'sissisi', $expensedate, $projectId, $expenseTitle, $amountValue, $accountId, $remark, $id);
        }
    } else {
        $insertColumns = [
            $map['date'],
            $map['project'],
            $map['title'],
            $map['amount'],
            $map['account'],
            $map['remark'],
        ];
        $placeholders = ['?', '?', '?', '?', '?', '?'];

        if ($map['company'] !== '') {
            array_unshift($insertColumns, $map['company']);
            array_unshift($placeholders, '?');
        }

        $sql = 'INSERT INTO project_expenses (' . implode(',', array_map('oecrm_project_expense_ident', $insertColumns)) . ') VALUES (' . implode(',', $placeholders) . ')';
        $stmt = mysqli_prepare($conn, $sql);

        if ($map['company'] !== '') {
            mysqli_stmt_bind_param($stmt, 'isissis', $companyId, $expensedate, $projectId, $expenseTitle, $amountValue, $accountId, $remark);
        } else {
            mysqli_stmt_bind_param($stmt, 'sissis', $expensedate, $projectId, $expenseTitle, $amountValue, $accountId, $remark);
        }
    }

    if (!$stmt || !mysqli_stmt_execute($stmt)) {
        throw new RuntimeException('Unable to save project expense. Please check project and account details.');
    }

    mysqli_stmt_close($stmt);
    oecrm_project_expense_redirect('Project expense saved successfully.', false, $action === 'update' ? $id : 0);
} catch (Throwable $exception) {
    oecrm_project_expense_redirect($exception->getMessage(), true, oecrm_project_expense_post_int('id'));
}
