<?php

return [
    'test_convertTri_formats_three_digit_chunks' => function (): void {
        oecrm_test_assert_same(' Three Hundred Forty Two', convertTri(342, 0));
        oecrm_test_assert_same('', convertTri(0, 0));
    },

    'test_convertNum_formats_zero_positive_and_negative_numbers' => function (): void {
        oecrm_test_assert_same('zero', convertNum(0));
        oecrm_test_assert_same(' Nineteen', convertNum(19));
        oecrm_test_assert_same(' Ten Thousand One Hundred Two', convertNum(10102));
        oecrm_test_assert_same('negative Five', convertNum(-5));
    },

    'test_super_unique_removes_duplicate_values_recursively' => function (): void {
        $input = [1, 2, 1, ['red', 'blue', 'red'], ['red', 'blue', 'red'], ['green']];
        $result = super_unique($input);
        oecrm_test_assert_same([1, 2, ['red', 'blue'], ['green']], array_values($result));
    },

    'test_get_time_arr_interval_returns_expected_schedule' => function (): void {
        $times = get_time_arr_interval(15);
        oecrm_test_assert_same(96, count($times));
        oecrm_test_assert_same('12:00 AM', $times[0]);
        oecrm_test_assert_same('12:15 AM', $times[1]);
        oecrm_test_assert_same('12:00 PM', $times[48]);
        oecrm_test_assert_same('11:45 PM', $times[95]);
    },

    'test_SetTimeFormatforEdit_pads_single_digit_time_parts' => function (): void {
        oecrm_test_assert_same('09:05:03', SetTimeFormatforEdit('9:5:3'));
        oecrm_test_assert_same('12:30', SetTimeFormatforEdit('12:30'));
    },

    'test_expense_status_helpers_return_expected_labels' => function (): void {
        oecrm_test_assert_same('btn btn-warning', get_exp_status_class(0));
        oecrm_test_assert_same('btn btn-success', get_exp_status_class(1));
        oecrm_test_assert_same('btn btn-danger', get_exp_status_class(2));
        oecrm_test_assert_same('btn btn-4', get_exp_status_class(3));
        oecrm_test_assert_same('Pending', get_exp_status1(0));
        oecrm_test_assert_same('Approve', get_exp_status1(1));
        oecrm_test_assert_same('Reject', get_exp_status1(2));
        oecrm_test_assert_same("<span class='label label-warning'>Pending</span>", get_exp_status(0));
        oecrm_test_assert_same("<span class='label label-success'>Approve</span>", get_exp_status(1));
        oecrm_test_assert_same("<span class='label label-danger'>Reject</span>", get_exp_status(2));
    },

    'test_order_status_helpers_return_expected_labels' => function (): void {
        oecrm_test_assert_same('btn btn-warning', get_order_status_class(0));
        oecrm_test_assert_same('btn btn-success', get_order_status_class(1));
        oecrm_test_assert_same('Open', get_order_status1(0));
        oecrm_test_assert_same('Completed', get_order_status1(1));
        oecrm_test_assert_same("<span class='label label-warning'>Open</span>", get_order_status(0));
        oecrm_test_assert_same("<span class='label label-success'>Completed</span>", get_order_status(1));
    },

    'test_get_lead_status_returns_all_status_labels' => function (): void {
        oecrm_test_assert_same("<span class='label label-default'>Low</span>", get_lead_status(0));
        oecrm_test_assert_same("<span class='label label-warning'>Medium</span>", get_lead_status(1));
        oecrm_test_assert_same("<span class='label label-primary'>High</span>", get_lead_status(2));
        oecrm_test_assert_same("<span class='label label-success'>Done</span>", get_lead_status(3));
        oecrm_test_assert_same("<span class='label label-danger'>Close</span>", get_lead_status(4));
    },
];

