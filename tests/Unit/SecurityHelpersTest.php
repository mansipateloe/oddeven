<?php

return [
    'test_employee_login_replaces_previous_super_admin_identity' => function (): void {
        oecrm_test_reset_state();
        $_SESSION = ['adminId' => 1, 'is_admin' => '1', 'adminName' => 'Admin',
            'admin_access_role' => 5, 'company_id' => 99, 'csrf_token' => 'old-token',
            '_oecrm_navigation' => ['old' => ['values' => ['id' => 123]]]];
        oecrm_initialize_authenticated_session();
        $_SESSION['employeeId'] = 8;
        oecrm_test_assert_false(oecrm_admin_session_is_valid());
        oecrm_test_assert_false(oecrm_is_super_admin());
        oecrm_test_assert_false(oecrm_can(null, 'employees'));
        oecrm_test_assert_false(oecrm_legacy_can(null, 'employees', 5));
        foreach (['adminId', 'is_admin', 'adminName', 'admin_access_role', 'company_id', 'csrf_token', '_oecrm_navigation'] as $key) {
            oecrm_test_assert_false(isset($_SESSION[$key]), 'Previous session value survived: ' . $key);
        }
    },

    'test_admin_login_replaces_employee_identity_and_role' => function (): void {
        oecrm_test_reset_state();
        $_SESSION = ['employeeId' => 8, 'admin_access_role' => 5, 'company_id' => 99];
        oecrm_initialize_authenticated_session();
        $_SESSION['adminId'] = 1;
        $_SESSION['is_admin'] = '1';
        oecrm_test_assert_true(oecrm_admin_session_is_valid());
        oecrm_test_assert_true(oecrm_is_super_admin());
        oecrm_test_assert_true(oecrm_can(null, 'employees'));
        oecrm_test_assert_false(isset($_SESSION['employeeId']));
        oecrm_test_assert_false(isset($_SESSION['admin_access_role']));
        oecrm_test_assert_false(isset($_SESSION['company_id']));
    },

    'test_mixed_employee_and_admin_session_never_grants_admin_permissions' => function (): void {
        oecrm_test_reset_state();
        $_SESSION = ['employeeId' => 8, 'adminId' => 1, 'is_admin' => '1', 'admin_access_role' => 5];
        oecrm_test_assert_false(oecrm_admin_session_is_valid());
        oecrm_test_assert_false(oecrm_is_super_admin());
        oecrm_test_assert_false(oecrm_can(null, 'employees'));
        oecrm_test_assert_false(oecrm_legacy_can(null, 'employees', 5));
    },

    'test_admin_session_requires_explicit_admin_or_delegated_role' => function (): void {
        oecrm_test_reset_state();
        $_SESSION['adminId'] = 8;
        oecrm_test_assert_false(oecrm_admin_session_is_valid());
        $_SESSION['is_admin'] = '0';
        oecrm_test_assert_false(oecrm_admin_session_is_valid());
        $_SESSION['admin_access_role'] = 5;
        oecrm_test_assert_true(oecrm_admin_session_is_valid());
        oecrm_test_assert_false(oecrm_is_super_admin());
    },

    'test_oecrm_navigation_actor_key_includes_portal_and_actor_id' => function (): void {
        oecrm_test_reset_state();
        $_SESSION['adminId'] = 15;
        oecrm_test_assert_same('admin:15', oecrm_navigation_actor_key('admin'));
        $_SESSION['employeeId'] = 8;
        oecrm_test_assert_same('employee:8', oecrm_navigation_actor_key('employee'));
    },

    'test_oecrm_csrf_token_generates_hex_token' => function (): void {
        oecrm_test_reset_state();
        $token = oecrm_csrf_token();
        oecrm_test_assert_same(64, strlen($token));
        oecrm_test_assert_true((bool) preg_match('/^[a-f0-9]{64}$/', $token));
    },

    'test_oecrm_csrf_field_embeds_current_token' => function (): void {
        oecrm_test_reset_state();
        $_SESSION['csrf_token'] = 'abc123';
        $field = oecrm_csrf_field();
        oecrm_test_assert_contains('name="csrf_token"', $field);
        oecrm_test_assert_contains('value="abc123"', $field);
    },

    'test_oecrm_verify_csrf_accepts_matching_token' => function (): void {
        oecrm_test_reset_state();
        $_SESSION['csrf_token'] = 'match-me';
        oecrm_test_assert_true(oecrm_verify_csrf('match-me'));
        oecrm_test_assert_false(oecrm_verify_csrf('wrong'));
    },

    'test_oecrm_int_param_returns_integer_values_for_numeric_input' => function (): void {
        oecrm_test_assert_same(12, oecrm_int_param(['id' => '12'], 'id'));
    },

    'test_oecrm_password_hash_and_verify_supports_current_and_legacy_hashes' => function (): void {
        $hash = oecrm_password_hash('secret123');
        oecrm_test_assert_true(oecrm_password_verify('secret123', $hash));
        oecrm_test_assert_false(oecrm_password_verify('wrong', $hash));
        oecrm_test_assert_true(oecrm_password_verify('legacy-pass', md5('legacy-pass')));
        oecrm_test_assert_false(oecrm_password_verify('legacy-pass', ''));
    },

    'test_oecrm_session_guard_initializes_session_on_first_request' => function (): void {
        oecrm_test_reset_state();
        $_SERVER['HTTP_USER_AGENT'] = 'Test Agent';
        oecrm_test_assert_true(oecrm_session_guard());
        oecrm_test_assert_true(isset($_SESSION['_oecrm_started_at']));
        oecrm_test_assert_true(isset($_SESSION['_oecrm_last_activity']));
        oecrm_test_assert_true(isset($_SESSION['_oecrm_rotated_at']));
        oecrm_test_assert_true(isset($_SESSION['_oecrm_user_agent']));
    },

    'test_oecrm_session_guard_rejects_expired_session' => function (): void {
        oecrm_test_reset_state();
        $_SERVER['HTTP_USER_AGENT'] = 'Test Agent';
        $_SESSION['_oecrm_started_at'] = time() - OECRM_SESSION_LIFETIME_SECONDS - 1;
        $_SESSION['_oecrm_last_activity'] = time() - OECRM_SESSION_LIFETIME_SECONDS - 1;
        $_SESSION['_oecrm_rotated_at'] = time() - OECRM_SESSION_LIFETIME_SECONDS - 1;
        $_SESSION['_oecrm_user_agent'] = hash('sha256', 'Test Agent');
        oecrm_test_assert_false(oecrm_session_guard());
    },
];
