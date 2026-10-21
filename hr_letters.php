<?php
function oecrm_letter_types()
{
    return [
        "offer" => "Offer Letter",
        "appointment" => "Appointment Letter",
        "internship" => "Internship Letter",
        "probation_extension" => "Probation Extension Letter",
        "confirmation" => "Confirmation Letter",
        "promotion" => "Promotion Letter",
        "transfer" => "Transfer Letter",
        "salary_revision" => "Salary Revision Letter",
        "increment" => "Increment Letter",
        "appreciation" => "Appreciation Letter",
        "warning" => "Warning Letter",
        "show_cause" => "Show Cause Notice",
        "performance_improvement" => "Performance Improvement Plan",
        "experience" => "Experience Letter",
        "relieving" => "Relieving Letter",
        "resignation_acceptance" => "Resignation Acceptance Letter",
        "termination" => "Termination Letter",
        "full_final_settlement" => "Full & Final Settlement Letter",
    ];
}
function oecrm_letter_presets()
{
    $sign =
        "<p>Sincerely,<br><strong>{{company_name}}</strong><br>Human Resources</p>";
    return [
        "offer" => [
            "subject" => "Employment Offer - {{company_name}}",
            "body" =>
                "<p>Dear {{employee_name}},</p><p>We are pleased to offer you employment with {{company_name}} as <strong>{{designation}}</strong>. Your proposed joining date is {{joining_date}}.</p><p>This offer is subject to company joining formalities and policies.</p>" .
                $sign,
        ],
        "appointment" => [
            "subject" => "Appointment as {{designation}}",
            "body" =>
                "<p>Dear {{employee_name}},</p><p>You are appointed as <strong>{{designation}}</strong> with {{company_name}} effective {{joining_date}}. Your employment is governed by company policies and service conditions.</p>" .
                $sign,
        ],
        "internship" => [
            "subject" => "Internship Engagement Letter",
            "body" =>
                "<p>Dear {{employee_name}},</p><p>We are pleased to engage you as an intern with {{company_name}} from {{joining_date}}. You will comply with company confidentiality and conduct requirements.</p>" .
                $sign,
        ],
        "probation_extension" => [
            "subject" => "Extension of Probation Period",
            "body" =>
                "<p>Dear {{employee_name}},</p><p>Your probation period is extended until <strong>{{effective_date}}</strong> for additional performance assessment. Your progress will be reviewed during this period.</p>" .
                $sign,
        ],
        "confirmation" => [
            "subject" => "Confirmation of Employment",
            "body" =>
                "<p>Dear {{employee_name}},</p><p>We are pleased to confirm your employment as <strong>{{designation}}</strong> effective {{effective_date}}. All other employment terms remain unchanged.</p>" .
                $sign,
        ],
        "promotion" => [
            "subject" => "Promotion to {{new_designation}}",
            "body" =>
                "<p>Dear {{employee_name}},</p><p>In recognition of your contribution, you are promoted to <strong>{{new_designation}}</strong> effective {{effective_date}}.</p>" .
                $sign,
        ],
        "transfer" => [
            "subject" => "Employee Transfer Letter",
            "body" =>
                "<p>Dear {{employee_name}},</p><p>You are transferred to the new role or location communicated by management effective {{effective_date}}. Reporting details will be shared separately.</p>" .
                $sign,
        ],
        "salary_revision" => [
            "subject" => "Salary Revision Effective {{effective_date}}",
            "body" =>
                "<p>Dear {{employee_name}},</p><p>Your compensation has been revised to <strong>{{revised_salary}}</strong> effective {{effective_date}}. The revision will reflect in the applicable payroll cycle.</p>" .
                $sign,
        ],
        "increment" => [
            "subject" => "Annual Increment Letter",
            "body" =>
                "<p>Dear {{employee_name}},</p><p>We are pleased to grant you an increment, revising your compensation to <strong>{{revised_salary}}</strong> effective {{effective_date}}.</p>" .
                $sign,
        ],
        "appreciation" => [
            "subject" => "Letter of Appreciation",
            "body" =>
                "<p>Dear {{employee_name}},</p><p>We sincerely appreciate your contribution, commitment and professional conduct. Your efforts have positively supported the team and {{company_name}}.</p>" .
                $sign,
        ],
        "warning" => [
            "subject" => "Formal Warning Letter",
            "body" =>
                "<p>Dear {{employee_name}},</p><p>This is a formal warning regarding <strong>{{reason}}</strong>. Immediate corrective action is expected. Repetition may result in further disciplinary action.</p>" .
                $sign,
        ],
        "show_cause" => [
            "subject" => "Show Cause Notice",
            "body" =>
                "<p>Dear {{employee_name}},</p><p>You are required to provide a written explanation regarding <strong>{{reason}}</strong> within the timeline communicated by Human Resources.</p>" .
                $sign,
        ],
        "performance_improvement" => [
            "subject" => "Performance Improvement Plan",
            "body" =>
                "<p>Dear {{employee_name}},</p><p>A performance improvement plan begins effective {{effective_date}}. Areas requiring improvement: {{reason}}. Your manager will review progress and provide guidance.</p>" .
                $sign,
        ],
        "experience" => [
            "subject" => "Experience Certificate - {{employee_name}}",
            "body" =>
                "<p>To Whom It May Concern,</p><p>This certifies that <strong>{{employee_name}}</strong> ({{employee_code}}) worked with {{company_name}} as {{designation}} from {{joining_date}} to {{last_working_date}}.</p><p>We wish them success.</p>" .
                $sign,
        ],
        "relieving" => [
            "subject" => "Relieving Letter",
            "body" =>
                "<p>Dear {{employee_name}},</p><p>You are relieved from your duties with {{company_name}} at the close of business on {{last_working_date}}, subject to completion of clearance formalities.</p>" .
                $sign,
        ],
        "resignation_acceptance" => [
            "subject" => "Acceptance of Resignation",
            "body" =>
                "<p>Dear {{employee_name}},</p><p>Your resignation has been accepted. Your last working date is <strong>{{last_working_date}}</strong>. Please complete handover and clearance.</p>" .
                $sign,
        ],
        "termination" => [
            "subject" => "Termination of Employment",
            "body" =>
                "<p>Dear {{employee_name}},</p><p>Your employment with {{company_name}} is terminated effective {{effective_date}} due to {{reason}}. Please complete company property return and clearance.</p>" .
                $sign,
        ],
        "full_final_settlement" => [
            "subject" => "Full and Final Settlement",
            "body" =>
                "<p>Dear {{employee_name}},</p><p>This confirms processing of your full and final settlement following your last working date {{last_working_date}}, subject to final clearance and statutory deductions.</p>" .
                $sign,
        ],
    ];
}
function oecrm_seed_letter_templates($conn, $companyId, $actor)
{
    $types = oecrm_letter_types();
    $presets = oecrm_letter_presets();
    $check = mysqli_prepare(
        $conn,
        "SELECT id FROM hr_letter_templates WHERE company_id=? AND letter_type=? LIMIT 1"
    );
    $insert = mysqli_prepare(
        $conn,
        "INSERT INTO hr_letter_templates(company_id,letter_type,name,subject,body_html,is_default,status,created_by) VALUES(?,?,?,?,?,1,1,?)"
    );
    foreach ($types as $key => $label) {
        mysqli_stmt_bind_param($check, "is", $companyId, $key);
        mysqli_stmt_execute($check);
        if (mysqli_fetch_assoc(mysqli_stmt_get_result($check))) {
            continue;
        }
        $name = "Standard " . $label;
        $subject = $presets[$key]["subject"];
        $body = $presets[$key]["body"];
        mysqli_stmt_bind_param(
            $insert,
            "issssi",
            $companyId,
            $key,
            $name,
            $subject,
            $body,
            $actor
        );
        mysqli_stmt_execute($insert);
    }
    mysqli_stmt_close($check);
    mysqli_stmt_close($insert);
}
function oecrm_letter_tokens($employee, $company, $extra = [])
{
    $values = [
        "{{employee_name}}" => $employee["name"] ?? "",
        "{{employee_code}}" => $employee["employeeCode"] ?? "",
        "{{designation}}" => $employee["designation"] ?? "",
        "{{joining_date}}" => !empty($employee["joiningDate"])
            ? date("d M Y", strtotime($employee["joiningDate"]))
            : "",
        "{{employee_email}}" => $employee["companyEmail"] ?? "",
        "{{employee_address}}" => $employee["address"] ?? "",
        "{{company_name}}" => $company["display_name"] ?? "",
        "{{company_legal_name}}" => $company["legal_name"] ?? "",
        "{{company_address}}" => $company["address"] ?? "",
        "{{company_email}}" => $company["email"] ?? "",
        "{{company_phone}}" => $company["phone"] ?? "",
        "{{issue_date}}" => date("d M Y"),
        "{{current_date}}" => date("d M Y"),
    ];
    foreach ($extra as $key => $value) {
        $values["{{" . $key . "}}"] = $value;
    }
    return $values;
}
function oecrm_render_letter($html, $employee, $company, $extra = [])
{
    return strtr($html, oecrm_letter_tokens($employee, $company, $extra));
}

function oecrm_letter_dataplaceholder($data){
    $placeholders = [
            "{{employee_name}}" => "Employee Name",
            "{{employee_code}}" => "Employee Code",
            "{{designation}}" => "Designation",
            "{{joining_date}}" => "Joining Date",
            "{{employee_email}}" => "Employee Email",
            "{{employee_address}}" => "Employee Address",
            "{{company_name}}" => "Company Name",
            "{{company_legal_name}}" => "Company Legal Name",
            "{{company_address}}" => "Company Address",
            "{{company_email}}" => "Company Email",
            "{{company_phone}}" => "Company Phone Number",
            "{{issue_date}}" => "Issue Date",
            "{{current_date}}" => "Current Date"
    ];

    return $data;
}   
function oecrm_letter_reference($conn, $companyId, $type)
{
    $prefix = strtoupper(substr(preg_replace("/[^a-z]/", "", $type), 0, 3));
    $base = $prefix . "-" . date("Ym") . "-";
    $stmt = mysqli_prepare(
        $conn,
        'SELECT reference_no FROM hr_letters WHERE company_id=? AND reference_no LIKE CONCAT(?,"%") ORDER BY id DESC LIMIT 1'
    );
    mysqli_stmt_bind_param($stmt, "is", $companyId, $base);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    $next = $row ? (int) substr($row["reference_no"], -4) + 1 : 1;
    return $base . str_pad($next, 4, "0", STR_PAD_LEFT);
}

function oecrm_letter_print_styles()
{
    return <<<'HTML'
<style>
@page { size: A4; margin: 18mm 17mm; }
* { box-sizing: border-box; }
body { margin: 0; background: #eef1f6; color: #202939; font-family: Arial, sans-serif; }
.toolbar { max-width: 210mm; margin: 18px auto; padding: 0 12px; display: flex; gap: 10px; justify-content: flex-end; }
.toolbar a, .toolbar button { border: 0; background: #344054; color: #fff; padding: 10px 16px; border-radius: 6px; text-decoration: none; font: inherit; cursor: pointer; }
.letter { width: 210mm; min-height: 297mm; margin: 0 auto 24px; padding: 20mm 17mm; background: #fff; box-shadow: 0 8px 30px rgba(35,42,63,.12); }
.letter-head { display: grid; grid-template-columns: minmax(0,1fr) 52mm; gap: 8mm; border-bottom: 2px solid #344054; padding-bottom: 6mm; margin-bottom: 8mm; break-inside: avoid; }
.letter-logo { display: block; width: auto; height: auto; max-width: 60mm; max-height: 22mm; margin-bottom: 4mm; object-fit: contain; }
.letter-head h1 { margin: 0 0 3mm; font-size: 18pt; line-height: 1.3; color: #182230; }
.letter-head > div { min-width: 0; font-size: 10pt; line-height: 1.6; overflow-wrap: anywhere; }
.letter-meta { text-align: right; color: #475467; }
.letter-meta strong { color: #202939; }
.letter-body { font-size: 11pt; line-height: 1.7; overflow-wrap: anywhere; }
.letter-body h2 { font-size: 14pt; line-height: 1.45; margin: 0 0 6mm; color: #182230; }
.letter-body h3 { font-size: 12pt; margin: 6mm 0 3mm; }
.letter-body p { margin: 0 0 4mm; orphans: 3; widows: 3; }
.letter-body ul, .letter-body ol { margin: 0 0 4mm; padding-left: 7mm; }
.letter-body li { padding-left: 1mm; margin-bottom: 2mm; }
.letter-body table { width: 100%; border-collapse: collapse; margin: 5mm 0; table-layout: fixed; }
.letter-body th, .letter-body td { padding: 3mm; border: 1px solid #d0d5dd; text-align: left; vertical-align: top; overflow-wrap: anywhere; }
.letter-body th { background: #f2f4f7; }
.letter-body tr, .letter-body .signature { break-inside: avoid; }
.letter-body img { max-width: 100%; height: auto; }
.letter-footer { margin-top: 10mm; border-top: 1px solid #d0d5dd; padding-top: 4mm; font-size: 9pt; line-height: 1.6; color: #667085; break-inside: avoid; overflow-wrap: anywhere; }
@media screen and (max-width: 820px) { .letter { width: calc(100% - 24px); min-height: auto; padding: 24px; } .letter-head { grid-template-columns: 1fr; gap: 16px; } .letter-meta { text-align: left; } }
@media print { html, body { margin: 0; background: #fff; } .toolbar { display: none !important; } .letter { margin: 0; padding: 0; width: auto; min-height: 0; box-shadow: none; } .letter-head { grid-template-columns: minmax(0,1fr) 52mm; } .letter-meta { text-align: right; } a { color: inherit; text-decoration: none; } }
</style>
HTML;
}
// status=-1 marks deleted templates. Keep the row so letter references and the
// seeder's record of intentionally removed defaults remain intact.
function oecrm_delete_letter_template($conn, $companyId, $id)
{
    if ($id <= 0) throw new InvalidArgumentException('Please select a valid template.');
    mysqli_begin_transaction($conn);
    try {
        $stmt = mysqli_prepare($conn, 'SELECT * FROM hr_letter_templates WHERE id=? AND company_id=? AND status>=0 FOR UPDATE');
        mysqli_stmt_bind_param($stmt, 'ii', $id, $companyId);
        mysqli_stmt_execute($stmt);
        $template = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        if (!$template) throw new InvalidArgumentException('Template not found or already deleted.');
        $stmt = mysqli_prepare($conn, 'UPDATE hr_letter_templates SET status=-1,is_default=0 WHERE id=? AND company_id=? AND status>=0');
        mysqli_stmt_bind_param($stmt, 'ii', $id, $companyId);
        if (!mysqli_stmt_execute($stmt)) throw new RuntimeException(mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        mysqli_commit($conn);
        return $template;
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        throw $e;
    }
}