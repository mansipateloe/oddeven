<<<<<<< HEAD
﻿<?php
$active_menu = 'finance';
include 'header.php';

$companyId = oecrm_current_company_id($conn);
$flash = $_SESSION['project_expense_flash'] ?? '';
$error = $_SESSION['project_expense_error'] ?? '';
unset($_SESSION['project_expense_flash'], $_SESSION['project_expense_error']);

if (!function_exists('oecrm_project_expense_columns')) {
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
}

if (!function_exists('oecrm_project_expense_col')) {
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
}

if (!function_exists('oecrm_project_expense_ident')) {
    function oecrm_project_expense_ident(string $name): string
    {
        return '`' . str_replace('`', '``', $name) . '`';
    }
}

if (!function_exists('oecrm_project_expense_map')) {
    function oecrm_project_expense_map(array $columns): array
    {
        return [
            'date' => oecrm_project_expense_col($columns, ['expensedate', 'expense_date']),
            'project' => oecrm_project_expense_col($columns, ['project_Id', 'project_id']),
            'title' => oecrm_project_expense_col($columns, ['expense_title', 'title']),
            'amount' => oecrm_project_expense_col($columns, ['amount', 'expense_amount']),
            'account' => oecrm_project_expense_col($columns, ['account_Id', 'account_id']),
            'remark' => oecrm_project_expense_col($columns, ['remark', 'description', 'notes']),
            'company' => oecrm_project_expense_col($columns, ['company_id']),
        ];
    }
}

$schemaError = '';
$map = [];
$editId = (int) ($_GET['edit'] ?? 0);
$editRow = null;
$projects = null;
$accounts = null;
$expenses = null;

try {
    $columns = oecrm_project_expense_columns($conn);
    $map = oecrm_project_expense_map($columns);

    foreach (['date', 'project', 'title', 'amount', 'account', 'remark'] as $requiredColumn) {
        if ($map[$requiredColumn] === '') {
            throw new RuntimeException('Project expense table schema is incomplete.');
        }
    }

    if ($editId > 0) {
        if ($map['company'] !== '') {
            $stmt = mysqli_prepare($conn, 'SELECT pe.* FROM project_expenses pe WHERE pe.id=? AND pe.' . oecrm_project_expense_ident($map['company']) . '=? LIMIT 1');
            mysqli_stmt_bind_param($stmt, 'ii', $editId, $companyId);
        } else {
            $stmt = mysqli_prepare($conn, 'SELECT pe.* FROM project_expenses pe INNER JOIN projectstbl p ON p.id=pe.' . oecrm_project_expense_ident($map['project']) . ' WHERE pe.id=? AND p.company_id=? LIMIT 1');
            mysqli_stmt_bind_param($stmt, 'ii', $editId, $companyId);
        }

        mysqli_stmt_execute($stmt);
        $editResult = mysqli_stmt_get_result($stmt);
        $editRow = mysqli_fetch_assoc($editResult) ?: null;
        mysqli_stmt_close($stmt);

        if (!$editRow) {
            $error = 'Selected project expense was not found.';
            $editId = 0;
        }
    }

    $projectStmt = mysqli_prepare($conn, "SELECT id,projectName FROM projectstbl WHERE company_id=? AND status NOT IN ('cancel','cancelled','deleted') ORDER BY projectName");
    mysqli_stmt_bind_param($projectStmt, 'i', $companyId);
    mysqli_stmt_execute($projectStmt);
    $projects = mysqli_stmt_get_result($projectStmt);

    $accounts = mysqli_query($conn, 'SELECT account_id,account_name FROM account ORDER BY account_name');

    $selectSql = 'SELECT pe.id, pe.' . oecrm_project_expense_ident($map['date']) . ' AS expense_date_value, '
        . 'pe.' . oecrm_project_expense_ident($map['title']) . ' AS expense_title_value, '
        . 'pe.' . oecrm_project_expense_ident($map['amount']) . ' AS amount_value, '
        . 'pe.' . oecrm_project_expense_ident($map['remark']) . ' AS remark_value, '
        . 'p.projectName, a.account_name '
        . 'FROM project_expenses pe '
        . 'LEFT JOIN projectstbl p ON p.id=pe.' . oecrm_project_expense_ident($map['project']) . ' '
        . 'LEFT JOIN account a ON a.account_id=pe.' . oecrm_project_expense_ident($map['account']) . ' ';

    if ($map['company'] !== '') {
        $selectSql .= 'WHERE pe.' . oecrm_project_expense_ident($map['company']) . '=? ';
    } else {
        $selectSql .= 'WHERE p.company_id=? ';
    }

    $selectSql .= 'ORDER BY pe.' . oecrm_project_expense_ident($map['date']) . ' DESC, pe.id DESC';
    $expenseStmt = mysqli_prepare($conn, $selectSql);
    mysqli_stmt_bind_param($expenseStmt, 'i', $companyId);
    mysqli_stmt_execute($expenseStmt);
    $expenses = mysqli_stmt_get_result($expenseStmt);
} catch (Throwable $exception) {
    $schemaError = $exception->getMessage();
}

$formValues = [
    'id' => $editRow['id'] ?? 0,
    'expensedate' => $editRow[$map['date'] ?? ''] ?? date('Y-m-d'),
    'project_Id' => $editRow[$map['project'] ?? ''] ?? '',
    'expense_title' => $editRow[$map['title'] ?? ''] ?? '',
    'amount' => $editRow[$map['amount'] ?? ''] ?? '',
    'account_Id' => $editRow[$map['account'] ?? ''] ?? '',
    'remark' => $editRow[$map['remark'] ?? ''] ?? '',
];
?>
<div id="page-wrapper" class="compact-admin-page">
    <div class="foundation-titlebar">
        <a href="finance.php"><i class="fa fa-arrow-left"></i> Finance</a>
        <h2>Project Expense</h2>
    </div>

    <?php if ($flash): ?><div class="alert alert-success"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo oecrm_h($error); ?></div><?php endif; ?>
    <?php if ($schemaError): ?><div class="alert alert-danger"><?php echo oecrm_h($schemaError); ?></div><?php endif; ?>

    <?php if (!$schemaError): ?>
        <div class="panel panel-default expense">
            <div class="panel-heading"><?php echo $editId > 0 ? 'Edit Project Expense' : 'Add Project Expense'; ?></div>
            <div class="panel-body">
                <form role="form" method="post" action="process/projectexpense.php">
                    <?php echo oecrm_csrf_field(); ?>
                    <input type="hidden" name="action" value="<?php echo $editId > 0 ? 'update' : 'add'; ?>">
                    <?php if ($editId > 0): ?><input type="hidden" name="id" value="<?php echo (int) $formValues['id']; ?>"><?php endif; ?>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="expensedate" value="<?php echo oecrm_h($formValues['expensedate']); ?>" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Project Name <span class="text-danger">*</span></label>
                                <select name="project_Id" id="project_Id" class="form-control" required>
                                    <option value="">Select Project Name</option>
                                    <?php if ($projects): while ($project = mysqli_fetch_assoc($projects)): ?>
                                        <option value="<?php echo (int) $project['id']; ?>" <?php echo (int) $formValues['project_Id'] === (int) $project['id'] ? 'selected' : ''; ?>><?php echo oecrm_h($project['projectName']); ?></option>
                                    <?php endwhile; endif; ?>
                                </select>
=======
<?php include 'header.php'; ?>
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default expense">
                    <div class="panel-heading">Add Project Expense</div>
                    <div class="panel-body">
                        <div class="dataTablesbox2">
                            <?php 
                                if(isset($_GET['edit'])){
                                    $id = $_GET['edit'];
                                    $get_edit_row = "SELECT * FROM project_expenses WHERE id=".$id." limit 1";
                                    $get_edit_result = mysqli_query($conn,$get_edit_row);
                                    if($get_edit_result->num_rows > 0){
                                        $edit_row = mysqli_fetch_assoc($get_edit_result);
                                        ?>
                                        <form role="form" method="POST" action="process/projectexpense.php">
                                            <input type="hidden" name="id" value="<?php echo $_GET['edit'] ?>">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Date :</label>
                                                        <input type="date" class="form-control" name="expensedate" value="<?php echo $edit_row['expensedate']; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Project Name :</label>
                                                        <select name="project_Id" id="project_Id" class="form-control" required>
                                                            <option value="">Select Project Name</option>
                                                            <?php 
                                                                $qryProject = "SELECT * FROM projectstbl where status IN ('pending','inprogress') order by id desc";
                                                                $resultProject = mysqli_query($conn,$qryProject);
                                                                if($resultProject->num_rows > 0){
                                                                    while($resProject = $resultProject->fetch_assoc()){
                                                                        if($resProject['id'] == $edit_row['project_Id']){
                                                                            echo "<option value='".$resProject['id']."' selected>".$resProject['projectName']."</option>";
                                                                        }else{
                                                                            echo "<option value='".$resProject['id']."'>".$resProject['projectName']."</option>";
                                                                        }
                                                                    }
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Expense Title:</label>
                                                        <input type="text" class="form-control" placeholder="Expense Title" name="expense_title" value="<?php echo $edit_row['expense_title']; ?>" minlength="3" maxlength="200" required>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Amount :</label>
                                                        <input type="text" class="form-control" min="0" value="<?php echo $edit_row['amount']; ?>" pattern="[0-9]+" placeholder="Amount" name="amount" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Account Name :</label>
                                                        <select name="account_Id" id="account_Id" class="form-control" required>
                                                            <option value="">Select Account Name</option>
                                                            <?php 
                                                                $qryAccount = "SELECT * FROM account";
                                                                $resultAccount = mysqli_query($conn,$qryAccount);
                                                                if($resultAccount->num_rows > 0){
                                                                    while($resAccount = $resultAccount->fetch_assoc()){
                                                                        if($resAccount['account_id'] == $edit_row['account_Id']){
                                                                            echo "<option value='".$resAccount['account_id']."' selected>".$resAccount['account_name']."</option>";
                                                                        }else{
                                                                            echo "<option value='".$resAccount['account_id']."'>".$resAccount['account_name']."</option>";
                                                                        }
                                                                    }
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Remark :</label>
                                                        <textarea type="text" class="form-control" placeholder="Remark" rows="5" name="remark"><?php echo $edit_row['remark']; ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="submit" class="btn btn-primary viewreport" name="update_project_expense" value="Update Expense">
                                                        <input class="btn btn-danger cancel_btn" type="reset" value="Cancel">    
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                        <?php    
                                    }
                                }else{
                                    ?>
                                    <form role="form" method="POST" action="process/projectexpense.php">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Date :</label>
                                                    <input type="date" class="form-control" name="expensedate" value="<?php echo date('Y-m-d'); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Project Name :</label>
                                                    <select name="project_Id" id="project_Id" class="form-control" required>
                                                        <option value="">Select Project Name</option>
                                                        <?php 
                                                            $qryProject = "SELECT * FROM projectstbl where status IN ('pending','inprogress') order by id desc";
                                                            $resultProject = mysqli_query($conn,$qryProject);
                                                            if($resultProject->num_rows > 0){
                                                                while($resProject = $resultProject->fetch_assoc()){
                                                                    echo "<option value='".$resProject['id']."'>".$resProject['projectName']."</option>";
                                                                }
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Expense Title:</label>
                                                    <input type="text" class="form-control" placeholder="Expense Title" name="expense_title" minlength="3" maxlength="200" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Amount :</label>
                                                    <input type="text" class="form-control" min="0" pattern="[0-9]+" placeholder="Amount" name="amount" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Account Name :</label>
                                                    <select name="account_Id" id="account_Id" class="form-control" required>
                                                        <option value="">Select Account Name</option>
                                                        <?php 
                                                            $qryAccount = "SELECT * FROM account";
                                                            $resultAccount = mysqli_query($conn,$qryAccount);
                                                            if($resultAccount->num_rows > 0){
                                                                while($resAccount = $resultAccount->fetch_assoc()){
                                                                    echo "<option value='".$resAccount['account_id']."'>".$resAccount['account_name']."</option>";
                                                                }
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Remark :</label>
                                                    <textarea type="text" class="form-control" placeholder="Remark" rows="5" name="remark"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input type="submit" class="btn btn-primary viewreport" name="add_project_expense" value="Add Expense">
                                                    <input class="btn btn-danger cancel_btn" type="reset" value="Cancel">    
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <?php
                                }
                             ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="panel panel-default view_expense">
                    <div class="panel-heading">
                       View All Project Expense
                    </div>
                    <div class="panel-body">
                        <div id="dataTables-example_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="dataTables_length" id="dataTables-example_length">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div id="dataTables-example_filter" class="dataTables_filter">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <table width="100%" class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline" id="dataTables-example" role="grid">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Project</th>
                                                <th>Expense</th>
                                                <th>Account</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $displayExpense = "select * from project_expenses";
                                                $result = mysqli_query($conn,$displayExpense);
                                                if($result->num_rows > 0){
                                                    while($row = $result->fetch_assoc()){
                                                    echo "<tr class='gradeA even' role='row'>";
                                                        echo "<td>".$row['expensedate']."</td>";
                                                        $get_proj_qry = "select * from projectstbl where id=".$row['project_Id'];
                                                        $get_proj_result = mysqli_query($conn,$get_proj_qry);
                                                        $get_proj_row = $get_proj_result->fetch_assoc();
                                                        echo "<td>".$get_proj_row['projectName']."</td>";
                                                        echo "<td>".$row['expense_title']."</td>";
                                                        $get_acc_qry = "select * from account where account_id=".$row['account_Id'];
                                                        $get_acc_result = mysqli_query($conn,$get_acc_qry);
                                                        $get_acc_row = $get_acc_result->fetch_assoc();
                                                        echo "<td>".$get_acc_row['account_name']."</td>";
                                                        echo "<td>".$row['amount']."</td>";
                                                        echo '<td><a href="projectExpense.php?edit='.$row["id"].'"><i class="fa fa-pencil" style="font-size:22px;"></i></a>&nbsp;&nbsp;
                                                        <a href="process/projectexpense.php?delete='.$row["id"].'" onclick="return confirm(\'Are you sure you want to delete?\');"><i class="fa fa-trash-o" style="font-size:25px; color:red;"></i></a></td>';
                                                    echo "</tr>";
                                                    }
                                                }
                                            ?>                                  
                                        </tbody>
                                    </table>
                                </div>
>>>>>>> 16f952b06473752f063dd7dad31e1ed1d31da926
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Expense Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Expense Title" name="expense_title" value="<?php echo oecrm_h($formValues['expense_title']); ?>" minlength="3" maxlength="200" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Amount <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" min="0" step="0.01" value="<?php echo oecrm_h($formValues['amount']); ?>" placeholder="Amount" name="amount" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Account Name <span class="text-danger">*</span></label>
                                <select name="account_Id" id="account_Id" class="form-control" required>
                                    <option value="">Select Account Name</option>
                                    <?php if ($accounts): while ($account = mysqli_fetch_assoc($accounts)): ?>
                                        <option value="<?php echo (int) $account['account_id']; ?>" <?php echo (int) $formValues['account_Id'] === (int) $account['account_id'] ? 'selected' : ''; ?>><?php echo oecrm_h($account['account_name']); ?></option>
                                    <?php endwhile; endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Remark</label>
                                <textarea class="form-control" placeholder="Remark" rows="4" name="remark"><?php echo oecrm_h($formValues['remark']); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" name="<?php echo $editId > 0 ? 'update_project_expense' : 'add_project_expense'; ?>" value="1">
                            <i class="fa fa-save"></i> <?php echo $editId > 0 ? 'Update Expense' : 'Add Expense'; ?>
                        </button>
                        <a class="btn btn-default" href="projectExpense.php">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="panel panel-default view_expense">
            <div class="panel-heading">View All Project Expense</div>
            <div class="panel-body table-responsive">
                <table width="100%" class="table table-striped table-bordered table-hover" id="project-expense-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Project</th>
                            <th>Expense</th>
                            <th>Account</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($expenses && mysqli_num_rows($expenses) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($expenses)): ?>
                                <tr>
                                    <td><?php echo oecrm_h($row['expense_date_value']); ?></td>
                                    <td><?php echo oecrm_h($row['projectName'] ?: '-'); ?></td>
                                    <td><?php echo oecrm_h($row['expense_title_value']); ?></td>
                                    <td><?php echo oecrm_h($row['account_name'] ?: '-'); ?></td>
                                    <td><?php echo number_format((float) $row['amount_value'], 2); ?></td>
                                    <td class="text-nowrap">
                                        <a class="btn btn-xs btn-primary" href="projectExpense.php?edit=<?php echo (int) $row['id']; ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                                        <form method="post" action="process/projectexpense.php" style="display:inline" onsubmit="return confirm('Are you sure you want to delete this expense?');">
                                            <?php echo oecrm_csrf_field(); ?>
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="delete_project_expense" value="1">
                                            <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                            <button type="submit" class="btn btn-xs btn-danger" title="Delete"><i class="fa fa-trash-o"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center">No project expenses found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.jQuery && jQuery.fn.DataTable && !jQuery.fn.DataTable.isDataTable('#project-expense-table')) {
            jQuery('#project-expense-table').DataTable({ order: [[0, 'desc']] });
        }
    });
</script>
<?php include 'footer.php'; ?>
