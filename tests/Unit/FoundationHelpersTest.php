<?php

return [
    'test_oecrm_h_escapes_html_special_characters' => function (): void {
        oecrm_test_assert_same('&lt;script&gt;&quot; &amp; &#039;', oecrm_h('<script>" & \''));
    },

    'test_oecrm_is_super_admin_detects_admin_flag' => function (): void {
        oecrm_test_reset_state();
        $_SESSION['is_admin'] = 1;
        oecrm_test_assert_true(oecrm_is_super_admin());
        $_SESSION['is_admin'] = 0;
        oecrm_test_assert_false(oecrm_is_super_admin());
    },

    'test_oecrm_current_company_id_uses_session_value_first' => function (): void {
        oecrm_test_reset_state();
        $_SESSION['company_id'] = 42;
        oecrm_test_assert_same(42, oecrm_current_company_id(null));
    },

    'test_oecrm_legacy_permission_target_maps_legacy_prefixes' => function (): void {
        oecrm_test_assert_same(['projects', 'create'], oecrm_legacy_permission_target('add_project'));
        oecrm_test_assert_same(['clients', 'delete'], oecrm_legacy_permission_target('delete_lead'));
        oecrm_test_assert_same(['finance', 'view'], oecrm_legacy_permission_target('invocie'));
        oecrm_test_assert_same(['custom', 'view'], oecrm_legacy_permission_target('custom'));
    },

    'test_oecrm_activity_client_detects_browser_platform_and_device' => function (): void {
        oecrm_test_reset_state();
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36';
        $client = oecrm_activity_client();
        oecrm_test_assert_same('Chrome', $client['browser']);
        oecrm_test_assert_same('Windows', $client['platform']);
        oecrm_test_assert_same('desktop', $client['device']);
    },

    'test_oecrm_activity_client_detects_mobile_safari' => function (): void {
        oecrm_test_reset_state();
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1';
        $client = oecrm_activity_client();
        oecrm_test_assert_same('Safari', $client['browser']);
        oecrm_test_assert_same('iPhone', $client['platform']);
        oecrm_test_assert_same('mobile', $client['device']);
    },
];

