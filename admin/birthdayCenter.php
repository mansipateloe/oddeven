<?php
$active_menu = 'notices';
include 'header.php';
require_once __DIR__ . '/../birthdays.php';
oecrm_require_permission($conn, 'notices', 'view');
$companyId = oecrm_current_company_id($conn);
$birthdays = oecrm_employee_birthdays($conn, $companyId, 30);
$flash = $_SESSION['birthday_flash'] ?? '';
$error = $_SESSION['birthday_error'] ?? '';
unset($_SESSION['birthday_flash'], $_SESSION['birthday_error']);
?>
<div id="page-wrapper" class="compact-admin-page birthday-center-page">
    <?php if ($flash): ?><div class="alert alert-success"><?php echo oecrm_h($flash); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo oecrm_h($error); ?></div><?php endif; ?>
    <div class="panel panel-default">
        <div class="panel-heading foundation-heading"><span>Employee Birthday Reminders</span><small>Today and upcoming birthdays for the next 30 days</small></div>
        <div class="panel-body">
            <div class="birthday-grid">
                <?php if (!$birthdays): ?><div class="empty-cell">No employee birthdays in the next 30 days.</div><?php endif; ?>
                <?php foreach ($birthdays as $employee): ?>
                    <article class="birthday-card <?php echo $employee['days_until'] === 0 ? 'today' : ''; ?>">
                        <div class="birthday-avatar"><i class="fa fa-birthday-cake"></i></div>
                        <div class="birthday-info">
                            <span><?php echo $employee['days_until'] === 0 ? 'Birthday Today' : 'In ' . $employee['days_until'] . ' days'; ?></span>
                            <h4><?php echo oecrm_h($employee['name']); ?></h4>
                            <p><?php echo oecrm_h($employee['employeeCode'] . ' | ' . $employee['designation']); ?></p>
                            <small><?php echo date('d F', strtotime($employee['birthday_date'])); ?></small>
                        </div>
                        <?php if ($employee['days_until'] === 0 && oecrm_can($conn, 'notices', 'create')): ?>
                            <form method="post" action="birthdayAction.php" class="birthday-wish-form">
                                <?php echo oecrm_csrf_field(); ?>
                                <input type="hidden" name="employee_id" value="<?php echo (int) $employee['id']; ?>">
                                <textarea class="form-control" name="message" rows="2" placeholder="Optional personal birthday message"></textarea>
                                <button class="btn btn-primary btn-sm"><i class="fa fa-gift"></i> Send Birthday Wish</button>
                            </form>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
