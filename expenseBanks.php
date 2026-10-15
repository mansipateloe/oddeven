<?php
// Shared schema and balance calculation for bank-funded expenses.
function oecrm_expense_bank_schema($conn)
{
    static $ready = false;
    if ($ready) {
        return;
    }
    try {
        $tables = [
            'bank_details' => [
                'account_number' => "VARCHAR(50) NOT NULL DEFAULT ''",
                'ifsc_code' => "VARCHAR(11) NOT NULL DEFAULT ''",
                'opening_balance' => 'DECIMAL(14,2) NOT NULL DEFAULT 0.00',
            ],
            // Old expenses remain unassigned and do not change any bank balance.
            'expense' => ['bank_id' => 'INT NULL DEFAULT NULL, ADD INDEX idx_expense_bank (bank_id)'],
        ];
        foreach ($tables as $table => $definitions) {
            $result = mysqli_query($conn, "SHOW COLUMNS FROM $table");
            if (!$result) {
                throw new RuntimeException(mysqli_error($conn));
            }
            $existing = [];
            while ($column = mysqli_fetch_assoc($result)) {
                $existing[$column['Field']] = true;
            }
            mysqli_free_result($result);
            foreach ($definitions as $name => $definition) {
                if (isset($existing[$name])) {
                    continue;
                }
                try {
                    if (!mysqli_query($conn, "ALTER TABLE $table ADD COLUMN $name $definition")
                        && mysqli_errno($conn) !== 1060) {
                        throw new RuntimeException(mysqli_error($conn));
                    }
                } catch (mysqli_sql_exception $e) {
                    if ((int) $e->getCode() !== 1060) {
                        throw $e;
                    }
                }
            }
        }
        $ready = true;
    } catch (Throwable $e) {
        error_log('Expense bank schema update failed: ' . $e->getMessage());
        http_response_code(500);
        exit('Bank details could not be initialized. Please check the database ALTER permission and reload this page.');
    }
}

function oecrm_expense_bank_list_sql()
{
    // Derive current balance from saved expenses; editing never deducts twice.
    return "SELECT b.*, b.opening_balance - COALESCE(x.spent,0) AS current_balance
        FROM bank_details b
        LEFT JOIN (
            SELECT bank_id, SUM(CAST(amount AS DECIMAL(14,2))) AS spent
            FROM expense WHERE bank_id IS NOT NULL GROUP BY bank_id
        ) x ON x.bank_id=b.bank_id ORDER BY b.bank_name";
}

function oecrm_expense_bank_field($conn, $selectedId = 0)
{
    $banks = mysqli_query($conn, oecrm_expense_bank_list_sql());
    echo '<div class="row"><div class="col-lg-6"><div class="form-group">';
    echo '<label for="expense_bank_id">Pay From Bank <span class="text-danger">*</span></label>';
    echo '<select name="bank_id" id="expense_bank_id" class="form-control" required>';
    echo '<option value="">Select Bank</option>';
    while ($bank = mysqli_fetch_assoc($banks)) {
        $selected = (int) $bank['bank_id'] === (int) $selectedId ? ' selected' : '';
        $label = $bank['bank_name'];
        if ($bank['account_number'] !== '') {
            $label .= ' (A/c ending ' . substr($bank['account_number'], -4) . ')';
        }
        $label .= ' - Balance: ' . number_format((float) $bank['current_balance'], 2);
        echo '<option value="' . (int) $bank['bank_id'] . '"' . $selected . '>' . oecrm_h($label) . '</option>';
    }
    echo '</select><p class="help-block">This expense is deducted from the selected bank balance.</p>';
    echo '</div></div></div>';
}

// Validation and persistence shared by both add forms and the edit form.
function oecrm_save_bank_expense($conn, $id, $category, $date, $amount, $description, $bankId)
{
    if (!preg_match('/^[0-9]{1,12}(\.[0-9]{1,2})?$/D', $amount) || (float) $amount <= 0) {
        throw new InvalidArgumentException('Enter an expense amount greater than zero with up to 2 decimal places.');
    }
    $parsedDate = DateTime::createFromFormat('!Y-m-d', $date);
    if (!$parsedDate || $parsedDate->format('Y-m-d') !== $date || $category <= 0 || $description === '' || strlen($description) > 255 || $bankId <= 0) {
        throw new InvalidArgumentException('Valid date, category, bank and description (maximum 255 characters) are required.');
    }
    $stmt = mysqli_prepare($conn, 'SELECT category_id FROM expencecategory WHERE category_id=?');
    mysqli_stmt_bind_param($stmt, 'i', $category);
    mysqli_stmt_execute($stmt);
    $validCategory = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$validCategory) {
        throw new InvalidArgumentException('Please select a valid expense category.');
    }

    mysqli_begin_transaction($conn);
    try {
        $stmt = mysqli_prepare($conn, 'SELECT bank_id FROM bank_details WHERE bank_id=? FOR UPDATE');
        mysqli_stmt_bind_param($stmt, 'i', $bankId);
        mysqli_stmt_execute($stmt);
        $bank = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        if (!$bank) {
            throw new InvalidArgumentException('Please select a valid bank.');
        }
        if ($id > 0) {
            $stmt = mysqli_prepare($conn, 'SELECT expense_id FROM expense WHERE expense_id=? FOR UPDATE');
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $old = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);
            if (!$old) {
                throw new InvalidArgumentException('Expense not found.');
            }
            $stmt = mysqli_prepare($conn, 'UPDATE expense SET expenseCategory=?,expensedate=?,amount=?,description=?,bank_id=? WHERE expense_id=?');
            mysqli_stmt_bind_param($stmt, 'isssii', $category, $date, $amount, $description, $bankId, $id);
        } else {
            $stmt = mysqli_prepare($conn, 'INSERT INTO expense(expenseCategory,expensedate,amount,description,bank_id) VALUES(?,?,?,?,?)');
            mysqli_stmt_bind_param($stmt, 'isssi', $category, $date, $amount, $description, $bankId);
        }
        if (!mysqli_stmt_execute($stmt)) {
            throw new RuntimeException(mysqli_stmt_error($stmt));
        }
        $savedId = $id > 0 ? $id : (int) mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        mysqli_commit($conn);
        return $savedId;
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        throw $e;
    }
}