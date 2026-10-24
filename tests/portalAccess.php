<?php
// Run with PHP CLI. Uses an isolated server/session directory, never real login sessions.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$root = dirname(__DIR__);
$directory = sys_get_temp_dir() . '/oecrm-access-' . bin2hex(random_bytes(8));
mkdir($directory, 0700);
$server = null;
$files = [];
$failed = 0;

function access_assert($condition, $label)
{
    global $failed;
    echo ($condition ? '[PASS] ' : '[FAIL] ') . $label . PHP_EOL;
    if (!$condition) $failed++;
}

try {
    $scenarios = [
        'guest' => [],
        'employee' => ['employeeId' => 8],
        'mixed' => ['employeeId' => 8, 'adminId' => 1, 'is_admin' => '1', 'admin_access_role' => 5],
        'admin' => ['adminId' => 1, 'is_admin' => '1'],
        'delegated' => ['adminId' => 8, 'is_admin' => '0', 'admin_access_role' => 5],
        'malformed' => ['adminId' => 1],
        'expired' => ['adminId' => 1, 'is_admin' => '1', '_oecrm_started_at' => 1,
            '_oecrm_last_activity' => 1, '_oecrm_rotated_at' => 1,
            '_oecrm_user_agent' => hash('sha256', '')],
    ];
    $sessions = [];
    foreach ($scenarios as $name => $values) {
        $sessions[$name] = bin2hex(random_bytes(16));
        $path = $directory . '/sess_' . $sessions[$name];
        $files[] = $path;
        file_put_contents($path, serialize($values));
    }
    $router = $directory . '/router.php';
    $files[] = $router;
    // Only the isolated server has this probe; production has no session-seeding endpoint.
    file_put_contents($router, '<?php if (parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) === "/__admin_guard") {'
        . 'require ' . var_export($root . '/security.php', true) . ';'
        . 'oecrm_require_admin_login(); echo "PROTECTED_CONTENT"; return; } return false;');
    $socket = stream_socket_server('tcp://127.0.0.1:0', $error, $message);
    if (!$socket) throw new RuntimeException($message);
    $address = stream_socket_get_name($socket, false);
    fclose($socket);
    $log = $directory . '/server.log';
    $files[] = $log;
    $server = proc_open([PHP_BINARY, '-d', 'session.save_path=' . $directory,
        '-d', 'session.serialize_handler=php_serialize', '-S', $address, '-t', $root, $router],
        [0 => ['pipe', 'r'], 1 => ['file', $log, 'a'], 2 => ['file', $log, 'a']], $pipes);
    if (!is_resource($server)) throw new RuntimeException('Could not start isolated PHP server.');
    fclose($pipes[0]);
    $ready = false;
    for ($attempt = 0; $attempt < 50; $attempt++) {
        $connection = @stream_socket_client('tcp://' . $address, $error, $message, 0.1);
        if ($connection) { fclose($connection); $ready = true; break; }
        usleep(100000);
    }
    if (!$ready) throw new RuntimeException('Isolated PHP server did not start.');

    $request = function ($path, $scenario, $method = 'GET') use ($address, $sessions) {
        $context = stream_context_create(['http' => ['method' => $method,
            'header' => "Cookie: PHPSESSID=" . $sessions[$scenario] . "\r\nConnection: close\r\n",
            'ignore_errors' => true, 'follow_location' => 0, 'timeout' => 10]]);
        $body = file_get_contents('http://' . $address . $path, false, $context);
        return [$body, implode("\n", $http_response_header ?? [])];
    };
    foreach (['employee', 'mixed', 'malformed'] as $scenario) {
        [$body, $headers] = $request('/__admin_guard', $scenario);
        access_assert(strpos($headers, '403 Forbidden') !== false
            && strpos($body, 'Unauthorized User') !== false
            && strpos($body, 'PROTECTED_CONTENT') === false, $scenario . ' rejected by admin guard');
    }
    foreach (['admin', 'delegated'] as $scenario) {
        [$body, $headers] = $request('/__admin_guard', $scenario);
        access_assert(strpos($headers, '200 OK') !== false && $body === 'PROTECTED_CONTENT', $scenario . ' remains authorized');
        access_assert(stripos($headers, 'Cache-Control: no-store') !== false, $scenario . ' response cannot be cached');
    }
    foreach (['guest', 'expired'] as $scenario) {
        [$body, $headers] = $request('/__admin_guard', $scenario);
        access_assert(strpos($headers, '302 Found') !== false
            && strpos($headers, 'Location: index.php') !== false
            && strpos($body, 'PROTECTED_CONTENT') === false, $scenario . ' redirected to login');
    }
    foreach (['employee', 'mixed'] as $scenario) {
        foreach (['index.php', 'manageEmployee.php', 'manageCurrency.php', 'accessAccount.php',
            'loginLog.php', 'noticeCenter.php', 'noticeAttachment.php', 'check.php', 'validation.php'] as $page) {
            [$body, $headers] = $request('/admin/' . $page . '?id=1', $scenario);
            access_assert(strpos($headers, '403 Forbidden') !== false
                && strpos($body, 'Unauthorized User') !== false, $scenario . ' cannot open admin/' . $page . ' with copied parameters');
        }
        foreach (['noticeAction.php', 'employeeSave.php'] as $page) {
            [$body, $headers] = $request('/admin/' . $page, $scenario, 'POST');
            access_assert(strpos($headers, '403 Forbidden') !== false
                && strpos($body, 'Unauthorized User') !== false, $scenario . ' cannot POST admin/' . $page);
        }
    }
} catch (Throwable $exception) {
    $failed++;
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
} finally {
    if (is_resource($server)) { proc_terminate($server); proc_close($server); }
    foreach ($files as $path) { if (is_file($path)) unlink($path); }
    rmdir($directory);
}
exit($failed ? 1 : 0);
