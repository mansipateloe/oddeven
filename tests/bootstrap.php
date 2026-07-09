<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../security.php';
require_once __DIR__ . '/../foundation.php';
require_once __DIR__ . '/../admin/functions.php';

function oecrm_test_reset_state(): void
{
    $_SESSION = [];
    $_GET = [];
    $_POST = [];
    $_COOKIE = [];
    $_FILES = [];
    $_SERVER['HTTP_USER_AGENT'] = '';
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    $_SERVER['REQUEST_METHOD'] = 'GET';
}

function oecrm_test_assert_same($expected, $actual, string $message = ''): void
{
    if ($expected !== $actual) {
        $detail = $message !== '' ? $message . ' ' : '';
        throw new RuntimeException($detail . 'Expected ' . var_export($expected, true) . ' but got ' . var_export($actual, true) . '.');
    }
}

function oecrm_test_assert_true($actual, string $message = ''): void
{
    oecrm_test_assert_same(true, (bool) $actual, $message);
}

function oecrm_test_assert_false($actual, string $message = ''): void
{
    oecrm_test_assert_same(false, (bool) $actual, $message);
}

function oecrm_test_assert_contains(string $needle, string $haystack, string $message = ''): void
{
    if (strpos($haystack, $needle) === false) {
        $detail = $message !== '' ? $message . ' ' : '';
        throw new RuntimeException($detail . "Expected string to contain '{$needle}'.");
    }
}

