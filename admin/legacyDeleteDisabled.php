<?php
require_once __DIR__ . '/dbconnect.php';
require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';

oecrm_require_admin_login();
oecrm_require_csrf();
oecrm_require_permission($conn, $legacyPermissionModule, $legacyPermissionAction ?? 'edit');

$_SESSION['legacy_action_flash'] = $legacyMessage;
oecrm_audit($conn, $legacyPermissionModule, 'legacy_delete_blocked', $legacyEntityType ?? null, oecrm_int_param($_GET, 'delete'), 'Legacy destructive action blocked');
header('Location: ' . $legacyRedirect);
exit;
