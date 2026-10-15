<?php
function oecrm_invoice_numbering_schema($conn)
{
    static $ready = false;
    if ($ready) return;
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // Fail explicitly for a partial deployment instead of reporting permissions.
    $dependency = __DIR__ . '/invoiceTax.php';
    if (!is_file($dependency)) {
        error_log('Invoice numbering initialization: missing invoiceTax.php');
        http_response_code(500);
        exit('Invoice initialization requires invoiceTax.php. Upload it alongside invoiceNumbering.php and reload.');
    }
    require_once $dependency;
    $step = 'checking invoice sequence table';
    try {
        $exists = false;
        try {
            mysqli_query($conn, 'SELECT company_id FROM company_invoice_sequences LIMIT 0');
            $exists = true;
        } catch (mysqli_sql_exception $e) {
            if ((int) $e->getCode() !== 1146) throw $e;
        }
        // CREATE IF NOT EXISTS still checks CREATE permission on some hosts.
        if (!$exists) {
            $step = 'creating invoice sequence table';
            mysqli_query($conn, "CREATE TABLE IF NOT EXISTS company_invoice_sequences (
                company_id INT NOT NULL PRIMARY KEY,
                prefix VARCHAR(40) NOT NULL,
                number_width TINYINT UNSIGNED NOT NULL DEFAULT 3,
                last_number BIGINT UNSIGNED NOT NULL DEFAULT 0,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }
        $step = 'reading invoice company column';
        $columns = oecrm_invoice_schema_query($conn, "SHOW COLUMNS FROM invoiceTbl LIKE 'company_id'");
        if (mysqli_num_rows($columns) === 0) {
            $step = 'adding invoice company column';
            try {
                oecrm_invoice_schema_query($conn, 'ALTER TABLE invoiceTbl ADD COLUMN company_id INT NULL DEFAULT NULL');
            } catch (mysqli_sql_exception $e) {
                if ((int) $e->getCode() !== 1060) throw $e;
            }
            $step = 'indexing invoice company column';
            try {
                oecrm_invoice_schema_query($conn, 'ALTER TABLE invoiceTbl ADD INDEX idx_invoice_company (company_id)');
            } catch (mysqli_sql_exception $e) {
                if ((int) $e->getCode() !== 1061) throw $e;
            }
        }
        mysqli_free_result($columns);
        $step = 'initializing invoice tax columns';
        oecrm_invoice_tax_schema($conn);
        $ready = true;
    } catch (Throwable $e) {
        oecrm_invoice_schema_error('Invoice numbering initialization', $step, $e);
    }
}
function oecrm_invoice_number_format($sequence, $number)
{
    if ($number > 999999999999) throw new InvalidArgumentException('Invoice number limit reached. Set a new prefix in Global Settings.');
    $value = $sequence['prefix'] . str_pad((string) $number, (int) $sequence['number_width'], '0', STR_PAD_LEFT);
    if (strlen($value) > 50) throw new InvalidArgumentException('Invoice number is too long. Use a shorter prefix.');
    return $value;
}

function oecrm_invoice_number_parse($value)
{
    if (strlen($value) > 50 || !preg_match('~^([A-Za-z0-9/_-]*?)([0-9]+)$~D', $value, $parts)
        || strlen($parts[1]) > 40 || strlen($parts[2]) > 12) {
        throw new InvalidArgumentException('Enter a number ending in up to 12 digits, for example odd001. Use only letters, digits, /, _ or - in the prefix.');
    }
    if ((int) $parts[2] >= 999999999999) throw new InvalidArgumentException('Use a smaller starting number or a new prefix.');
    return ['prefix' => $parts[1], 'number_width' => strlen($parts[2]), 'last_number' => (int) $parts[2]];
}

function oecrm_invoice_number_max($conn, $companyId, $prefix)
{
    // Legacy invoices without explicit ownership inherit their project's company.
    $stmt = oecrm_invoice_schema_prepare($conn, 'SELECT i.invoice_no AS number FROM invoiceTbl i
        LEFT JOIN projectstbl p ON p.id=i.project_Id WHERE COALESCE(i.company_id,p.company_id)=?
        UNION ALL SELECT invoice_number AS number FROM finance_invoices WHERE company_id=?');
    mysqli_stmt_bind_param($stmt, 'ii', $companyId, $companyId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $max = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $number = (string) $row['number'];
        if (strncasecmp($number, $prefix, strlen($prefix)) !== 0) continue;
        $suffix = substr($number, strlen($prefix));
        if ($suffix !== '' && ctype_digit($suffix)) {
            $max = max($max, min(1000000000000, (float) $suffix));
        }
    }
    mysqli_stmt_close($stmt);
    return (int) $max;
}

// Caller owns the transaction. Lock the company first, including first-time setup.
function oecrm_invoice_sequence_locked($conn, $companyId)
{
    $stmt = oecrm_invoice_schema_prepare($conn, 'SELECT id FROM companies WHERE id=? AND status=1 FOR UPDATE');
    mysqli_stmt_bind_param($stmt, 'i', $companyId);
    mysqli_stmt_execute($stmt);
    $company = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$company) throw new InvalidArgumentException('Please select an active company.');

    $stmt = oecrm_invoice_schema_prepare($conn, 'SELECT * FROM company_invoice_sequences WHERE company_id=? FOR UPDATE');
    mysqli_stmt_bind_param($stmt, 'i', $companyId);
    mysqli_stmt_execute($stmt);
    $sequence = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$sequence) {
        $stmt = oecrm_invoice_schema_prepare($conn, "SELECT setting_value FROM app_settings WHERE company_id=? AND setting_key='invoice_prefix' LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'i', $companyId);
        mysqli_stmt_execute($stmt);
        $setting = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        $prefix = (string) ($setting['setting_value'] ?? 'INV');
        if (strlen($prefix) > 40 || !preg_match('~^[A-Za-z0-9/_-]*$~D', $prefix)) $prefix = 'INV';
        $sequence = ['company_id' => $companyId, 'prefix' => $prefix, 'number_width' => 3, 'last_number' => 0];
        $stmt = oecrm_invoice_schema_prepare($conn, 'INSERT INTO company_invoice_sequences(company_id,prefix) VALUES(?,?)');
        mysqli_stmt_bind_param($stmt, 'is', $companyId, $prefix);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    $sequence['last_number'] = max((int) $sequence['last_number'], oecrm_invoice_number_max($conn, $companyId, $sequence['prefix']));
    return $sequence;
}

function oecrm_invoice_sequence_preview($conn, $companyId)
{
    mysqli_begin_transaction($conn);
    try {
        $sequence = oecrm_invoice_sequence_locked($conn, $companyId);
        $sequence['next_invoice_number'] = oecrm_invoice_number_format($sequence, $sequence['last_number'] + 1);
        $sequence['last_invoice_number'] = oecrm_invoice_number_format($sequence, $sequence['last_number']);
        mysqli_commit($conn);
        return $sequence;
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        throw $e;
    }
}

function oecrm_invoice_sequence_save($conn, $companyId, $value)
{
    $new = oecrm_invoice_number_parse($value);
    mysqli_begin_transaction($conn);
    try {
        $old = oecrm_invoice_sequence_locked($conn, $companyId);
        $minimum = oecrm_invoice_number_max($conn, $companyId, $new['prefix']);
        if (strcasecmp($old['prefix'], $new['prefix']) === 0) $minimum = max($minimum, (int) $old['last_number']);
        if ($new['last_number'] < $minimum) {
            throw new InvalidArgumentException('This would reuse an earlier invoice number. Enter a number at least ' . oecrm_invoice_number_format($new, $minimum) . ' or use a new prefix.');
        }
        $stmt = oecrm_invoice_schema_prepare($conn, 'UPDATE company_invoice_sequences SET prefix=?,number_width=?,last_number=? WHERE company_id=?');
        mysqli_stmt_bind_param($stmt, 'siii', $new['prefix'], $new['number_width'], $new['last_number'], $companyId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_commit($conn);
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        throw $e;
    }
}

// Reserve within the same transaction as the invoice and its line items.
function oecrm_invoice_number_allocate($conn, $companyId)
{
    $sequence = oecrm_invoice_sequence_locked($conn, $companyId);
    $next = (int) $sequence['last_number'] + 1;
    $number = oecrm_invoice_number_format($sequence, $next);
    $stmt = oecrm_invoice_schema_prepare($conn, 'UPDATE company_invoice_sequences SET last_number=? WHERE company_id=?');
    mysqli_stmt_bind_param($stmt, 'ii', $next, $companyId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $number;
}
// Legacy invoice header, line items and sequence commit together.
function oecrm_create_numbered_legacy_invoice($conn, $companyId, $input)
{
    $date = trim((string) ($input['invoice_date'] ?? ''));
    $person = trim((string) ($input['person_name'] ?? ''));
    $project = (int) ($input['project_Id'] ?? 0);
    $parsed = DateTime::createFromFormat('!Y-m-d', $date);
    if (!$parsed || $parsed->format('Y-m-d') !== $date || $person === '' || strlen($person) > 255) {
        throw new InvalidArgumentException('A valid invoice date and person name are required.');
    }
    $items = [];
    $total = 0;
    foreach (($input['perticulars'] ?? []) as $index => $description) {
        $description = trim((string) $description);
        $amount = trim((string) ($input['amounts'][$index] ?? ''));
        $bank = (int) ($input['bank_id'][$index] ?? 0);
        $country = (int) ($input['country_id'][$index] ?? 0);
        if ($description === '' || strlen($description) > 255 || !preg_match('/^[0-9]{1,12}(\.[0-9]{1,2})?$/D', $amount) || $bank <= 0 || $country <= 0) {
            throw new InvalidArgumentException('Each invoice item needs a description, valid amount, bank account and currency.');
        }
        $items[] = [$description, $amount, $bank, $country];
        $total += (float) $amount;
    }
    if (!$items) throw new InvalidArgumentException('At least one invoice item is required.');
    $stmt = oecrm_invoice_schema_prepare($conn, "SELECT id FROM projectstbl WHERE id=? AND company_id=? AND status IN ('pending','inprogress')");
    mysqli_stmt_bind_param($stmt, 'ii', $project, $companyId);
    mysqli_stmt_execute($stmt);
    $validProject = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$validProject) throw new InvalidArgumentException('Please select a project belonging to the active company.');

    $client = oecrm_invoice_project_client($conn, $project, $companyId);
    $tax = oecrm_invoice_tax_calculate($total, $input, $client['is_indian_client']);
    mysqli_begin_transaction($conn);
    try {
        $number = oecrm_invoice_number_allocate($conn, $companyId);
        $stmt = oecrm_invoice_schema_prepare($conn, 'INSERT INTO invoiceTbl(company_id,invoice_date,project_Id,person_name,total_amount,invoice_no) VALUES(?,?,?,?,?,?)');
        mysqli_stmt_bind_param($stmt, 'isisds', $companyId, $date, $project, $person, $total, $number);
        mysqli_stmt_execute($stmt);
        $id = (int) mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        $stmt = oecrm_invoice_schema_prepare($conn, 'INSERT INTO invoice_details(invoice_id,perticular,amount,bank_id,country_id) VALUES(?,?,?,?,?)');
        foreach ($items as $item) {
            mysqli_stmt_bind_param($stmt, 'issii', $id, $item[0], $item[1], $item[2], $item[3]);
            mysqli_stmt_execute($stmt);
        }
        mysqli_stmt_close($stmt);
        oecrm_invoice_tax_save($conn, $id, $tax);
        mysqli_commit($conn);
        return ['id' => $id, 'number' => $number];
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        throw $e;
    }
}