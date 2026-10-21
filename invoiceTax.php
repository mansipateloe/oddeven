<?php
// Windows MySQL ignores table-name case; Linux deployments may distinguish it.
function oecrm_invoice_actual_table($conn)
{
    static $name = null;
    if ($name !== null) return $name;
    $result = mysqli_query($conn, 'SHOW TABLES');
    $match = null;
    while ($row = mysqli_fetch_row($result)) {
        if ($row[0] === 'invoiceTbl') {
            $match = $row[0];
            break;
        }
        if (strcasecmp($row[0], 'invoiceTbl') === 0) $match = $row[0];
    }
    mysqli_free_result($result);
    if ($match === null) throw new RuntimeException('Invoice source table was not found.', 1146);
    return $name = $match;
}

function oecrm_invoice_schema_sql($conn, $sql)
{
    $pattern = '/(?<![a-z0-9_])(?:(?<![a-z0-9_])' . chr(96) . ')?invoicetbl' . chr(96) . '?(?![a-z0-9_])/i';
    if (!preg_match($pattern, $sql)) return $sql;
    $table = chr(96) . str_replace(chr(96), chr(96) . chr(96), oecrm_invoice_actual_table($conn)) . chr(96);
    return preg_replace($pattern, $table, $sql);
}

function oecrm_invoice_schema_query($conn, $sql)
{
    return mysqli_query($conn, oecrm_invoice_schema_sql($conn, $sql));
}

function oecrm_invoice_schema_prepare($conn, $sql)
{
    return mysqli_prepare($conn, oecrm_invoice_schema_sql($conn, $sql));
}

function oecrm_invoice_schema_error($context, $step, $e)
{
    error_log($context . ' failed at ' . $step . ': ' . $e->getMessage());
    $code = (int) $e->getCode();
    if (in_array($code, [1044, 1142, 1143, 1227], true)) {
        $detail = 'The database user does not have permission for this operation.';
    } elseif ($code === 1146) {
        $detail = 'A required table was not found in the selected database.';
    } elseif ($code === 1064 || $code === 1067 || $code === 1293) {
        $detail = 'The production database rejected the schema definition.';
    } else {
        $detail = 'Please check the PHP error log for the underlying error.';
    }
    http_response_code(500);
    exit(htmlspecialchars($context . ' failed at ' . $step . ' (error ' . $code . '). ' . $detail, ENT_QUOTES, 'UTF-8'));
}

function oecrm_invoice_tax_schema($conn)
{
    static $ready = false;
    if ($ready) return;
    $step = 'reading invoice tax columns';
    try {
        $definitions = [
            'subtotal_amount' => 'DECIMAL(14,2) NULL DEFAULT NULL',
            'tax_mode' => "VARCHAR(16) NOT NULL DEFAULT 'none'",
            'cgst_percent' => 'DECIMAL(5,2) NOT NULL DEFAULT 0',
            'sgst_percent' => 'DECIMAL(5,2) NOT NULL DEFAULT 0',
            'igst_percent' => 'DECIMAL(5,2) NOT NULL DEFAULT 0',
            'cgst_amount' => 'DECIMAL(14,2) NOT NULL DEFAULT 0',
            'sgst_amount' => 'DECIMAL(14,2) NOT NULL DEFAULT 0',
            'igst_amount' => 'DECIMAL(14,2) NOT NULL DEFAULT 0',
        ];
        $columns = oecrm_invoice_schema_query($conn, 'SHOW COLUMNS FROM invoiceTbl');
        $existing = [];
        while ($column = mysqli_fetch_assoc($columns)) $existing[$column['Field']] = true;
        mysqli_free_result($columns);
        foreach ($definitions as $name => $definition) {
            if (isset($existing[$name])) continue;
            $step = 'adding invoice column ' . $name;
            try {
                if (!oecrm_invoice_schema_query($conn, "ALTER TABLE invoiceTbl ADD COLUMN $name $definition") && mysqli_errno($conn) !== 1060) {
                    throw new RuntimeException(mysqli_error($conn));
                }
            } catch (mysqli_sql_exception $e) {
                if ((int) $e->getCode() !== 1060) throw $e;
            }
        }
        $ready = true;
    } catch (Throwable $e) {
        oecrm_invoice_schema_error('Invoice tax initialization', $step, $e);
    }
}

function oecrm_invoice_project_client($conn, $projectId, $companyId)
{
    $stmt = oecrm_invoice_schema_prepare($conn, 'SELECT p.id,p.customerName,c.display_name,c.country
        FROM projectstbl p LEFT JOIN clients c ON c.id=p.client_id AND c.company_id=p.company_id
        WHERE p.id=? AND p.company_id=? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'ii', $projectId, $companyId);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$row) throw new InvalidArgumentException('Please select a project belonging to the active company.');
    $row['country'] = trim((string) ($row['country'] ?? ''));
    $row['is_indian_client'] = in_array(strtoupper($row['country']), ['INDIA', 'IN', 'IND', 'BHARAT'], true);
    return $row;
}

function oecrm_invoice_tax_calculate($subtotal, $input, $isIndianClient)
{
    $tax = [
        'subtotal_amount' => round($subtotal, 2), 'tax_mode' => 'none',
        'cgst_percent' => 0, 'sgst_percent' => 0, 'igst_percent' => 0,
        'cgst_amount' => 0, 'sgst_amount' => 0, 'igst_amount' => 0,
    ];
    $mode = (string) ($input['tax_mode'] ?? 'none');
    if ($isIndianClient && $mode !== 'none') {
        if (!in_array($mode, ['cgst_sgst', 'igst'], true)) throw new InvalidArgumentException('Please select a valid GST type.');
        $tax['tax_mode'] = $mode;
        $types = $mode === 'igst' ? ['igst'] : ['cgst', 'sgst'];
        foreach ($types as $type) {
            $rate = trim((string) ($input[$type . '_percent'] ?? '0'));
            if (!preg_match('/^[0-9]{1,3}(\.[0-9]{1,2})?$/D', $rate) || (float) $rate > 100) {
                throw new InvalidArgumentException('Tax rates must be between 0 and 100 with up to 2 decimal places.');
            }
            $tax[$type . '_percent'] = (float) $rate;
            $tax[$type . '_amount'] = round($tax['subtotal_amount'] * (float) $rate / 100, 2);
        }
        if ($tax['cgst_percent'] !== $tax['sgst_percent']) throw new InvalidArgumentException('CGST and SGST rates must match.');
    }
    $tax['total_amount'] = round($tax['subtotal_amount'] + $tax['cgst_amount'] + $tax['sgst_amount'] + $tax['igst_amount'], 2);
    if ($tax['subtotal_amount'] < 0 || $tax['total_amount'] > 999999999999.99) {
        throw new InvalidArgumentException('Invoice total is outside the supported amount range.');
    }
    return $tax;
}

function oecrm_invoice_tax_save($conn, $id, $tax)
{
    $stmt = oecrm_invoice_schema_prepare($conn, 'UPDATE invoiceTbl SET subtotal_amount=?,tax_mode=?,cgst_percent=?,sgst_percent=?,igst_percent=?,cgst_amount=?,sgst_amount=?,igst_amount=?,total_amount=? WHERE invoice_id=?');
    mysqli_stmt_bind_param($stmt, 'dsdddddddi', $tax['subtotal_amount'], $tax['tax_mode'], $tax['cgst_percent'], $tax['sgst_percent'], $tax['igst_percent'], $tax['cgst_amount'], $tax['sgst_amount'], $tax['igst_amount'], $tax['total_amount'], $id);
    if (!mysqli_stmt_execute($stmt)) throw new RuntimeException(mysqli_stmt_error($stmt));
    mysqli_stmt_close($stmt);
}

function oecrm_invoice_tax_fields($invoice = [])
{
    $mode = $invoice['tax_mode'] ?? 'cgst_sgst';
    $hasSavedGst = !empty($invoice) && in_array($mode, ['cgst_sgst', 'igst'], true);
    ?>
    <div id="invoice-tax-section" class="row" data-saved-gst="<?php echo $hasSavedGst ? '1' : '0'; ?>" style="<?php echo $hasSavedGst ? '' : 'display:none;'; ?>">
        <div class="col-lg-3"><div class="form-group">
            <label for="invoice-tax-mode">GST Type</label>
            <select id="invoice-tax-mode" name="tax_mode" class="form-control">
                <?php foreach (['none' => 'No GST', 'cgst_sgst' => 'CGST + SGST', 'igst' => 'IGST'] as $value => $label): ?>
                    <option value="<?php echo $value; ?>" <?php echo $mode === $value ? 'selected' : ''; ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </div></div>
        <?php foreach (['cgst' => 'CGST', 'sgst' => 'SGST', 'igst' => 'IGST'] as $type => $label): ?>
            <div class="col-lg-3 invoice-tax-rate" data-tax-type="<?php echo $type; ?>"><div class="form-group">
                <label for="invoice-<?php echo $type; ?>"><?php echo $label; ?> (%)</label>
                <input id="invoice-<?php echo $type; ?>" type="number" name="<?php echo $type; ?>_percent" class="form-control" min="0" max="100" step="0.01" value="<?php echo oecrm_h($invoice[$type . '_percent'] ?? '0'); ?>">
            </div></div>
        <?php endforeach; ?>
    </div>
    <div class="row"><div class="col-lg-12">
        <p id="invoice-tax-message" class="help-block"></p>
        <p>Subtotal: <strong id="invoice-subtotal">0.00</strong>
            <span id="invoice-tax-summary" style="display:none;"> &nbsp; GST: <strong id="invoice-tax-total">0.00</strong></span>
            &nbsp; Total: <strong id="invoice-grand-total">0.00</strong>
        </p>
    </div></div>
    <?php
}