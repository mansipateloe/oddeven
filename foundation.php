<?php
require_once __DIR__ . '/security.php';

function oecrm_current_company_id($conn)
{
    if (!empty($_SESSION['company_id'])) {
        return (int) $_SESSION['company_id'];
    }

    $companyId = 0;
    if (!empty($_SESSION['adminId'])) {
        $stmt = mysqli_prepare($conn, 'SELECT company_id FROM admins WHERE id = ? LIMIT 1');
        if ($stmt) {
            $adminId = (int) $_SESSION['adminId'];
            mysqli_stmt_bind_param($stmt, 'i', $adminId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $companyId);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }
    } elseif (!empty($_SESSION['employeeId'])) {
        $stmt = mysqli_prepare($conn, 'SELECT company_id FROM employeestbl WHERE id = ? LIMIT 1');
        if ($stmt) {
            $employeeId = (int) $_SESSION['employeeId'];
            mysqli_stmt_bind_param($stmt, 'i', $employeeId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $companyId);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    if (!$companyId) {
        $result = mysqli_query($conn, 'SELECT id FROM companies WHERE status = 1 ORDER BY id LIMIT 1');
        if ($result && ($row = mysqli_fetch_assoc($result))) {
            $companyId = (int) $row['id'];
        }
    }

    $_SESSION['company_id'] = $companyId;
    return $companyId;
}

function oecrm_is_super_admin()
{
    return !empty($_SESSION['is_admin']) && (int) $_SESSION['is_admin'] === 1;
}

function oecrm_can($conn, $module, $action = 'view')
{
    if (oecrm_is_super_admin()) {
        return true;
    }

    $roleId = isset($_SESSION['admin_access_role']) ? (int) $_SESSION['admin_access_role'] : 0;
    if (!$roleId) {
        return false;
    }

    $companyId = oecrm_current_company_id($conn);
    $stmt = mysqli_prepare($conn, 'SELECT rp.allowed FROM role_permissions rp INNER JOIN permissions p ON p.id = rp.permission_id WHERE rp.role_id = ? AND p.module_key = ? AND p.action_key = ? AND rp.company_id IN (0, ?) ORDER BY rp.company_id DESC LIMIT 1');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'issi', $roleId, $module, $action, $companyId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $allowed);
        if (mysqli_stmt_fetch($stmt)) {
            mysqli_stmt_close($stmt);
            return (bool) $allowed;
        }
        mysqli_stmt_close($stmt);
    }

    return false;
}

function oecrm_legacy_permission_target($legacyName)
{
    $legacyName = strtolower(trim((string) $legacyName));
    $action = 'view';
    foreach (['add_' => 'create', 'update_' => 'edit', 'view_' => 'view', 'delete_' => 'delete'] as $prefix => $mappedAction) {
        if (strpos($legacyName, $prefix) === 0) {
            $legacyName = substr($legacyName, strlen($prefix));
            $action = $mappedAction;
            break;
        }
    }

    $modules = [
        'lead' => 'clients',
        'project' => 'projects',
        'employee' => 'employees',
        'leave' => 'leave_requests',
        'salary' => 'payroll',
        'domain_hosting' => 'subscriptions',
        'settings' => 'settings',
        'invocie' => 'finance',
        'invoice' => 'finance',
        'deposit' => 'finance',
        'expense' => 'finance',
        'project_expense' => 'finance',
    ];

    return [$modules[$legacyName] ?? $legacyName, $action];
}

function oecrm_legacy_can($conn, $legacyName, $roleId = 0)
{
    if (oecrm_is_super_admin()) {
        return true;
    }

    [$module, $action] = oecrm_legacy_permission_target($legacyName);
    $roleId = (int) ($roleId ?: ($_SESSION['admin_access_role'] ?? 0));
    if (!$roleId) {
        return false;
    }

    $companyId = oecrm_current_company_id($conn);
    $stmt = mysqli_prepare($conn, 'SELECT rp.allowed FROM role_permissions rp INNER JOIN permissions p ON p.id=rp.permission_id WHERE rp.role_id=? AND p.module_key=? AND p.action_key=? AND rp.company_id IN (0,?) ORDER BY rp.company_id DESC LIMIT 1');
    if (!$stmt) {
        return false;
    }
    mysqli_stmt_bind_param($stmt, 'issi', $roleId, $module, $action, $companyId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $allowed);
    $granted = mysqli_stmt_fetch($stmt) ? (bool) $allowed : false;
    mysqli_stmt_close($stmt);
    return $granted;
}

function oecrm_require_permission($conn, $module, $action = 'view')
{
    if (!oecrm_can($conn, $module, $action)) {
        http_response_code(403);
        exit('You do not have permission to perform this action.');
    }
}

function oecrm_activity_client()
{
    $agent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);
    $browser = 'Other';
    if (stripos($agent, 'Edg/') !== false) $browser = 'Edge';
    elseif (stripos($agent, 'Chrome/') !== false) $browser = 'Chrome';
    elseif (stripos($agent, 'Firefox/') !== false) $browser = 'Firefox';
    elseif (stripos($agent, 'Safari/') !== false) $browser = 'Safari';
    $platform = 'Other';
    foreach (['Windows','Android','iPhone','iPad','Macintosh','Linux'] as $candidate) {
        if (stripos($agent, $candidate) !== false) { $platform = $candidate === 'Macintosh' ? 'macOS' : $candidate; break; }
    }
    $device = preg_match('/Mobile|Android|iPhone|iPad/i', $agent) ? (stripos($agent, 'iPad') !== false ? 'tablet' : 'mobile') : 'desktop';
    return ['agent'=>$agent,'browser'=>$browser,'platform'=>$platform,'device'=>$device];
}

function oecrm_activity_risk($conn, $companyId, $actorType, $actorId, $action, $module, $ip)
{
    $reasons = [];
    $hour = (int) date('G');
    if (($hour < 6 || $hour >= 23) && in_array($action, ['login','failed_login','export','delete','purge','approve','lock'], true)) $reasons[] = 'Unusual hour';
    if ($action === 'failed_login') {
        $safeIp = mysqli_real_escape_string($conn, $ip);
        $recent = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM audit_logs WHERE ip_address='$safeIp' AND action='failed_login' AND created_at>=DATE_SUB(NOW(),INTERVAL 15 MINUTE)"));
        if ((int)($recent['total'] ?? 0) >= 3) $reasons[] = 'Repeated login failures';
    }
    if ($action === 'login' && $actorId > 0) {
        $stmt = mysqli_prepare($conn, 'SELECT ip_address FROM audit_logs WHERE company_id=? AND actor_type=? AND actor_id=? AND action="login" ORDER BY id DESC LIMIT 1');
        mysqli_stmt_bind_param($stmt, 'isi', $companyId, $actorType, $actorId);mysqli_stmt_execute($stmt);$prior=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));mysqli_stmt_close($stmt);
        if ($prior && $prior['ip_address'] && $prior['ip_address'] !== $ip) $reasons[] = 'New IP address';
    }
    if (in_array($action, ['purge','delete','manage_permissions'], true)) $reasons[] = 'Sensitive administrative action';
    return ['level'=>count($reasons)>=2?'suspicious':(count($reasons)===1?'review':'normal'),'reasons'=>implode(', ',$reasons)];
}

function oecrm_write_audit($conn, $companyId, $actorType, $actorId, $employeeId, $module, $action, $entityType = null, $entityId = null, $description = null, $oldValues = null, $newValues = null)
{
    $oldJson = $oldValues === null ? null : json_encode($oldValues, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $newJson = $newValues === null ? null : json_encode($newValues, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';$client=oecrm_activity_client();$method=$_SERVER['REQUEST_METHOD']??'';$uri=substr($_SERVER['REQUEST_URI']??'',0,500);$sessionKey=session_id();
    $risk=oecrm_activity_risk($conn,$companyId,$actorType,$actorId,$action,$module,$ip);$entityId=$entityId===null?null:(string)$entityId;
    $stmt=mysqli_prepare($conn,'INSERT INTO audit_logs(company_id,actor_type,actor_id,employee_id,session_key,action,module_key,entity_type,entity_id,description,old_values,new_values,ip_address,user_agent,browser_name,device_type,platform_name,risk_level,risk_reasons,request_method,request_uri) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    if(!$stmt)return false;
    mysqli_stmt_bind_param($stmt,'isiisssssssssssssssss',$companyId,$actorType,$actorId,$employeeId,$sessionKey,$action,$module,$entityType,$entityId,$description,$oldJson,$newJson,$ip,$client['agent'],$client['browser'],$client['device'],$client['platform'],$risk['level'],$risk['reasons'],$method,$uri);
    $ok=mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);return $ok;
}

function oecrm_audit($conn, $module, $action, $entityType = null, $entityId = null, $description = null, $oldValues = null, $newValues = null)
{
    $companyId=oecrm_current_company_id($conn);$actorType=!empty($_SESSION['adminId'])?'admin':(!empty($_SESSION['employeeId'])?'employee':'system');$actorId=!empty($_SESSION['adminId'])?(int)$_SESSION['adminId']:(!empty($_SESSION['employeeId'])?(int)$_SESSION['employeeId']:0);
    $employeeId=$actorType==='employee'?$actorId:null;if($actorType==='admin'&&!empty($_SESSION['is_admin'])&&(int)$_SESSION['is_admin']===0)$employeeId=$actorId;
    if(!$employeeId && $entityId!==null){$targetId=(int)$entityId;$lookup=null;if(in_array($entityType,['employee','employee_profile','employee_document','employee_contract','employee_increment'],true))$employeeId=$entityType==='employee'?$targetId:null;elseif($entityType==='leave_request')$lookup='SELECT employee_id FROM leave_requests WHERE id=?';elseif(in_array($entityType,['payroll_item','salary_structure'],true))$lookup='SELECT employee_id FROM '.$entityType.'s WHERE id=?';elseif($entityType==='attendance_session')$lookup='SELECT employee_id FROM attendance_sessions WHERE id=?';elseif($entityType==='shift_assignment')$lookup='SELECT employee_id FROM employee_shift_assignments WHERE id=?';if($lookup){$s=mysqli_prepare($conn,$lookup);if($s){mysqli_stmt_bind_param($s,'i',$targetId);mysqli_stmt_execute($s);mysqli_stmt_bind_result($s,$resolvedEmployee);if(mysqli_stmt_fetch($s))$employeeId=(int)$resolvedEmployee;mysqli_stmt_close($s);}}}
    if(!$employeeId && is_array($newValues) && !empty($newValues['employee_id']))$employeeId=(int)$newValues['employee_id'];
    return oecrm_write_audit($conn,$companyId,$actorType,$actorId,$employeeId,$module,$action,$entityType,$entityId,$description,$oldValues,$newValues);
}

function oecrm_assign_task_qa_reviewers($conn, $taskId, $assignedBy = 0)
{
    $taskId = (int) $taskId;
    $assignedBy = (int) $assignedBy;
    $task = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT id,company_id,CAST(projectId AS UNSIGNED) project_id FROM tasktbl WHERE id=' . $taskId));
    if (!$task) return 0;

    $existing = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT COUNT(*) total
        FROM task_assignees ta JOIN employeestbl e ON e.id=ta.employee_id
        WHERE ta.task_id=' . $taskId . ' AND ta.is_primary=0 AND e.status=0
        AND (e.designation LIKE "%QA%" OR e.designation LIKE "%Quality Assurance%")'));
    if ((int) ($existing['total'] ?? 0) > 0) return (int) $existing['total'];

    $projectId = (int) $task['project_id'];
    $companyId = (int) $task['company_id'];
    $qaResult = mysqli_query($conn, 'SELECT DISTINCT e.id
        FROM project_team_members tm JOIN employeestbl e ON e.id=tm.employee_id
        WHERE tm.project_id=' . $projectId . ' AND tm.left_at IS NULL AND e.status=0
        AND (e.designation LIKE "%QA%" OR e.designation LIKE "%Quality Assurance%")
        ORDER BY e.id');
    if (!$qaResult || mysqli_num_rows($qaResult) === 0) {
        $qaResult = mysqli_query($conn, 'SELECT e.id FROM employeestbl e
            WHERE e.company_id=' . $companyId . ' AND e.status=0
            AND (e.designation LIKE "%QA%" OR e.designation LIKE "%Quality Assurance%")
            ORDER BY e.id');
    }

    $insert = mysqli_prepare($conn, 'INSERT IGNORE INTO task_assignees(task_id,employee_id,is_primary,assigned_by) VALUES(?,?,0,?)');
    $assigned = 0;
    while ($qaResult && ($qa = mysqli_fetch_assoc($qaResult))) {
        $qaId = (int) $qa['id'];
        mysqli_stmt_bind_param($insert, 'iii', $taskId, $qaId, $assignedBy);
        mysqli_stmt_execute($insert);
        $assigned += mysqli_stmt_affected_rows($insert) > 0 ? 1 : 0;
    }
    mysqli_stmt_close($insert);
    return $assigned;
}

function oecrm_auth_audit($conn, $actorType, $actorId, $companyId, $action, $description, $employeeId = null, $newValues = null)
{
    return oecrm_write_audit($conn,(int)$companyId,$actorType,(int)$actorId,$employeeId,'authentication',$action,$actorType,$actorId,$description,null,$newValues);
}
function oecrm_h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function oecrm_vault_key($conn)
{
    $result = mysqli_query($conn, "SELECT setting_value FROM app_settings WHERE company_id=0 AND setting_key='vault_key' LIMIT 1");
    $row = $result ? mysqli_fetch_assoc($result) : null;
    if (!empty($row['setting_value'])) return base64_decode($row['setting_value'], true);
    $key = random_bytes(32);
    $encoded = mysqli_real_escape_string($conn, base64_encode($key));
    mysqli_query($conn, "INSERT INTO app_settings(company_id,setting_key,setting_value,is_encrypted) VALUES(0,'vault_key','$encoded',1)");
    return $key;
}

function oecrm_encrypt_secret($value)
{
    global $conn;
    if ($value === '') return null;
    $iv = random_bytes(12);
    $tag = '';
    $cipher = openssl_encrypt($value, 'aes-256-gcm', oecrm_vault_key($conn), OPENSSL_RAW_DATA, $iv, $tag);
    if ($cipher === false) throw new RuntimeException('Credential encryption failed.');
    return base64_encode($iv . $tag . $cipher);
}

function oecrm_decrypt_secret($value)
{
    global $conn;
    $raw = base64_decode((string)$value, true);
    if ($raw === false || strlen($raw) < 29) return null;
    return openssl_decrypt(substr($raw, 28), 'aes-256-gcm', oecrm_vault_key($conn), OPENSSL_RAW_DATA, substr($raw, 0, 12), substr($raw, 12, 16));
}

