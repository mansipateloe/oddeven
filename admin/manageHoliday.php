<?php
$active_menu = 'attendance';
include 'header.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_permission($conn, 'attendance', 'view');

$companyId = oecrm_current_company_id($conn);
$editId = (int) ($_GET['edit'] ?? 0);
$edit = $editId
    ? mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            'SELECT * FROM holidaytbl WHERE id=' . $editId . ' AND company_id IN (0,' . $companyId . ')'
        )
    )
    : null;

$rows = mysqli_query(
    $conn,
    'SELECT h.*, c.display_name company_name
     FROM holidaytbl h
     LEFT JOIN companies c ON c.id = h.company_id
     WHERE h.company_id IN (0,' . $companyId . ')
     ORDER BY h.holidayDate'
);

$flash = $_SESSION['holiday_flash'] ?? '';
$holidayErrors = $_SESSION['holiday_errors'] ?? [];
$holidayDateValue = $_SESSION['holiday_date_value'] ?? ($edit['holidayDate'] ?? '');
$holidayTitleValue = $_SESSION['holiday_title_value'] ?? ($edit['holidayTitle'] ?? '');

unset(
    $_SESSION['holiday_flash'],
    $_SESSION['holiday_errors'],
    $_SESSION['holiday_date_value'],
    $_SESSION['holiday_title_value']
);
?>
<div id="page-wrapper" class="compact-admin-page">
    <?php if ($flash): ?>
        <div class="alert alert-info"><?php echo oecrm_h($flash); ?></div>
    <?php endif; ?>

    <div class="panel panel-default">
        <div class="panel-heading"><?php echo $edit ? 'Edit' : 'Add'; ?> Holiday</div>
        <div class="panel-body">
            <style>
                .holiday-form .required-field:after {
                    content: " *";
                    color: #d9534f;
                    font-weight: 700;
                }

                .holiday-form .is-invalid {
                    border-color: #d9534f !important;
                    box-shadow: 0 0 0 0.2rem rgba(217, 83, 79, 0.15) !important;
                }

                .holiday-form .field-error {
                    display: block;
                    color: #d9534f;
                    font-size: 12px;
                    margin-top: 6px;
                    line-height: 1.3;
                }
            </style>

            <?php if (!empty($holidayErrors)): ?>
                <div class="alert alert-danger">Please fix the highlighted holiday fields.</div>
            <?php endif; ?>

            <form method="post" action="holidayAction.php" class="holiday-form form-inline">
                <?php echo oecrm_csrf_field(); ?>
                <input type="hidden" name="id" value="<?php echo (int) ($edit['id'] ?? 0); ?>">

                <div class="form-group">
                    <label class="required-field">Date</label>
                    <input
                        class="form-control <?php echo !empty($holidayErrors['holiday_date']) ? 'is-invalid' : ''; ?>"
                        type="date"
                        name="holiday_date"
                        required
                        value="<?php echo oecrm_h($holidayDateValue); ?>"
                    >
                    <?php if (!empty($holidayErrors['holiday_date'])): ?>
                        <span class="field-error"><?php echo oecrm_h($holidayErrors['holiday_date']); ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="required-field">Title</label>
                    <input
                        class="form-control <?php echo !empty($holidayErrors['holiday_title']) ? 'is-invalid' : ''; ?>"
                        name="title"
                        required
                        value="<?php echo oecrm_h($holidayTitleValue); ?>"
                    >
                    <?php if (!empty($holidayErrors['holiday_title'])): ?>
                        <span class="field-error"><?php echo oecrm_h($holidayErrors['holiday_title']); ?></span>
                    <?php endif; ?>
                </div>

                <button class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
            </form>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Holiday Calendar</div>
        <div class="panel-body table-responsive">
            <table class="table foundation-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Holiday</th>
                        <th>Scope</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($rows)): ?>
                        <tr>
                            <td><?php echo date('d M Y', strtotime($row['holidayDate'])); ?></td>
                            <td><?php echo oecrm_h($row['holidayTitle']); ?></td>
                            <td><?php echo $row['company_id'] ? oecrm_h($row['company_name']) : 'All Companies'; ?></td>
                            <td>
                                <?php if ((int) $row['company_id'] === $companyId): ?>
                                    <a class="icon-action" href="manageHoliday.php?edit=<?php echo (int) $row['id']; ?>">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <form method="post" action="holidayAction.php" style="display:inline" data-confirm="Archive this holiday?">
                                        <?php echo oecrm_csrf_field(); ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                        <button class="icon-action danger"><i class="fa fa-trash"></i></button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
