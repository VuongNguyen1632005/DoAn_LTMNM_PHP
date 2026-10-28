<?php

declare(strict_types=1);

use App\Infrastructure\CouchDB\CouchDbClient;

require dirname(__DIR__) . '/vendor/autoload.php';

$configuredDatabase = getenv('COUCHDB_DATABASE') ?: '';
if (!preg_match('/^retail_admin_http_[a-f0-9]{8}_test$/', $configuredDatabase)) {
    fwrite(STDERR, "Refusing to run without a unique retail_admin_http_*_test database name in COUCHDB_DATABASE.\n");
    exit(2);
}
$database = $configuredDatabase;
$client = new CouchDbClient(
    getenv('COUCHDB_URL') ?: 'http://couchdb:5984',
    getenv('COUCHDB_USER') ?: '',
    getenv('COUCHDB_PASSWORD') ?: '',
);
$dbPath = rawurlencode($database);
$projectRoot = dirname(__DIR__);
$nonce = bin2hex(random_bytes(4));
$passwords = ['manager' => 'ManagerRuntime!91', 'staff' => 'StaffRuntime!91', 'customer' => 'CustomerRuntime!91'];
$emails = [
    'manager' => 'mgr-' . $nonce . '@example.invalid',
    'staff' => 'staff-' . $nonce . '@example.invalid',
    'customer' => 'customer-' . $nonce . '@example.invalid',
];
$ids = [
    'manager' => 'staff:HTTPMGR' . strtoupper($nonce),
    'staff' => 'staff:HTTPSTAFF' . strtoupper($nonce),
    'customer' => 'customer:HTTPCUS' . strtoupper($nonce),
];
$backupDirectory = $projectRoot . '/var/backups';
$existingBackups = array_fill_keys(array_map('basename', glob($backupDirectory . '/*.json') ?: []), true);
$server = null;
$databaseCreated = false;
$cookieJars = [];
$logPath = null;
$probeJar = null;
$checks = [];
$assert = static function (bool $condition, string $message) use (&$checks): void {
    $checks[] = ['name' => $message, 'result' => $condition ? 'PASS' : 'FAIL'];
    if (!$condition) throw new RuntimeException('FAIL: ' . $message);
};
$request = static function (string $url, string $jar, string $method = 'GET', ?array $form = null): array {
    $curl = curl_init($url);
    if ($curl === false) throw new RuntimeException('Could not initialize HTTP test client.');
    $options = [CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true, CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_COOKIEJAR => $jar, CURLOPT_COOKIEFILE => $jar, CURLOPT_TIMEOUT => 15];
    if ($method !== 'GET') {
        $options[CURLOPT_CUSTOMREQUEST] = $method;
        if ($form !== null) $options[CURLOPT_POSTFIELDS] = http_build_query($form);
    }
    curl_setopt_array($curl, $options);
    $raw = curl_exec($curl);
    $error = curl_error($curl);
    $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    $headerSize = (int) curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    curl_close($curl);
    if (!is_string($raw)) throw new RuntimeException('HTTP test request failed: ' . $error);
    return ['status' => $status, 'headers' => substr($raw, 0, $headerSize), 'body' => substr($raw, $headerSize)];
};
$csrf = static function (string $html): string {
    if (preg_match('/name="csrf_token"\s+value="([a-f0-9]+)"/', $html, $matches) !== 1) {
        throw new RuntimeException('Could not extract the CSRF token from the rendered form.');
    }
    return $matches[1];
};
$login = static function (string $baseUrl, string $jar, string $email, string $password) use ($request, $csrf): array {
    $form = $request($baseUrl . '/login', $jar);
    $token = $csrf($form['body']);
    return $request($baseUrl . '/login', $jar, 'POST', ['csrf_token' => $token, 'email' => $email, 'password' => $password]);
};
$doc = static function (string $kind, string $id, string $email, string $password, string $nonce): array {
    $type = $kind === 'customer' ? 'customer' : 'staff';
    return [
        '_id' => $id,
        'type' => $type,
        'schema_version' => 2,
        'legacy_id' => strtoupper(str_replace(':', '', $id)),
        'auth' => ['username' => $email, 'password_hash' => password_hash($password, PASSWORD_DEFAULT), 'requires_password_reset' => false],
        'profile' => ['name' => 'Runtime ' . ucfirst($kind) . ' ' . $nonce, 'email' => $email, 'phone' => '+84 900 000 000'],
        'active' => true,
        'meta' => ['source' => 'HTTP integration test', 'created_at' => gmdate('c')],
        ...($type === 'staff' ? ['role' => $kind === 'manager' ? 'manager' : 'staff'] : []),
    ];
};

try {
    $created = $client->request('PUT', $dbPath);
    if (!in_array($created->statusCode, [201, 202], true)) throw new RuntimeException('Could not create test database (HTTP ' . $created->statusCode . ').');
    $databaseCreated = true;
    $validator = json_decode((string) file_get_contents($projectRoot . '/database/couchdb/design-docs/domain_validation.json'), true, 512, JSON_THROW_ON_ERROR);
    $putValidator = $client->request('PUT', $dbPath . '/_design/domain_validation', $validator);
    if (!in_array($putValidator->statusCode, [201, 202], true)) throw new RuntimeException('Could not install validator (HTTP ' . $putValidator->statusCode . ').');
    $index = $client->request('POST', $dbPath . '/_index', [
        'index' => ['fields' => [['type' => 'asc'], ['auth.username' => 'asc']]],
        'ddoc' => 'catalog_indexes', 'name' => 'account_usernames', 'type' => 'json',
    ]);
    if ($index->statusCode < 200 || $index->statusCode >= 300) throw new RuntimeException('Could not create account index (HTTP ' . $index->statusCode . ').');
    foreach (['manager', 'staff', 'customer'] as $kind) {
        $put = $client->request('PUT', $dbPath . '/' . rawurlencode($ids[$kind]), $doc($kind, $ids[$kind], $emails[$kind], $passwords[$kind], $nonce));
        if (!in_array($put->statusCode, [201, 202], true)) throw new RuntimeException('Could not seed ' . $kind . ' fixture (HTTP ' . $put->statusCode . '): ' . $put->body);
    }

    $port = random_int(20000, 45000);
    $baseUrl = 'http://127.0.0.1:' . $port;
    $serverEnv = getenv();
    $serverEnv['COUCHDB_DATABASE'] = $database;
    $logPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'php-admin-http-' . $nonce . '.log';
    $server = proc_open([PHP_BINARY, '-S', '127.0.0.1:' . $port, '-t', $projectRoot . '/public', $projectRoot . '/public/index.php'],
        [0 => ['file', '/dev/null', 'r'], 1 => ['file', $logPath, 'a'], 2 => ['file', $logPath, 'a']], $serverPipes, $projectRoot, $serverEnv);
    if (!is_resource($server)) throw new RuntimeException('Could not start isolated PHP HTTP server.');
    $probeJar = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'php-admin-http-probe-' . $nonce . '.cookie';
    for ($attempt = 0; $attempt < 40; $attempt++) {
        try { $probe = $request($baseUrl . '/login', $probeJar); if ($probe['status'] === 200) break; }
        catch (Throwable) { usleep(100000); }
        usleep(100000);
    }
    if (($probe['status'] ?? null) !== 200) throw new RuntimeException('Isolated PHP HTTP server did not become ready.');

    foreach (['manager', 'staff', 'customer'] as $kind) {
        $jar = tempnam(sys_get_temp_dir(), 'admin-http-cookie-');
        if ($jar === false) throw new RuntimeException('Could not create session cookie jar.');
        $cookieJars[] = $jar;
        $sessions[$kind] = $jar;
    }

    $managerLogin = $login($baseUrl, $sessions['manager'], $emails['manager'], $passwords['manager']);
    $assert($managerLogin['status'] === 303, 'manager can log in over HTTP');
    $list = $request($baseUrl . '/admin/users?q=' . rawurlencode('Runtime Customer ' . $nonce) . '&type=customer', $sessions['manager']);
    $assert($list['status'] === 200 && str_contains($list['body'], $emails['customer']) && !str_contains($list['body'], $emails['staff']), 'manager search and customer filter return matching account only');
    $detail = $request($baseUrl . '/admin/users/' . rawurlencode($ids['staff']), $sessions['manager']);
    $assert($detail['status'] === 200 && str_contains($detail['body'], $emails['staff']) && !str_contains($detail['body'], 'password_hash'), 'manager can view safe account detail');

    $editForm = $request($baseUrl . '/admin/users/' . rawurlencode($ids['staff']) . '/edit', $sessions['manager']);
    $token = $csrf($editForm['body']);
    if (preg_match('/name="_rev"\s+value="([^"]+)"/', $editForm['body'], $revisionMatch) !== 1) throw new RuntimeException('Edit form omitted CouchDB revision.');
    $oldRevision = html_entity_decode($revisionMatch[1], ENT_QUOTES | ENT_HTML5);
    $edit = $request($baseUrl . '/admin/users/' . rawurlencode($ids['staff']) . '/edit', $sessions['manager'], 'POST', [
        'csrf_token' => $token, '_rev' => $oldRevision, 'name' => 'Edited HTTP Staff ' . $nonce, 'phone' => '+84 911 222 333',
    ]);
    $assert($edit['status'] === 303, 'manager can edit profile with current CouchDB revision');
    $saved = $client->request('GET', $dbPath . '/' . rawurlencode($ids['staff']))->json();
    $assert(($saved['profile']['name'] ?? '') === 'Edited HTTP Staff ' . $nonce, 'profile edit was persisted');
    $stale = $request($baseUrl . '/admin/users/' . rawurlencode($ids['staff']) . '/edit', $sessions['manager'], 'POST', [
        'csrf_token' => $token, '_rev' => $oldRevision, 'name' => 'Stale overwrite must fail', 'phone' => '',
    ]);
    $assert($stale['status'] === 409 && str_contains($stale['body'], 'đã được cập nhật ở nơi khác'), 'stale revision returns HTTP 409 with conflict message');
    $afterStale = $client->request('GET', $dbPath . '/' . rawurlencode($ids['staff']))->json();
    $assert(($afterStale['profile']['name'] ?? '') === 'Edited HTTP Staff ' . $nonce, 'stale edit does not overwrite latest profile');

    $csrfPage = $request($baseUrl . '/admin/users/' . rawurlencode($ids['customer']), $sessions['manager']);
    $statusToken = $csrf($csrfPage['body']);
    $lock = $request($baseUrl . '/admin/users/' . rawurlencode($ids['customer']) . '/status', $sessions['manager'], 'POST', ['csrf_token' => $statusToken, 'active' => '0']);
    $locked = $client->request('GET', $dbPath . '/' . rawurlencode($ids['customer']))->json();
    $assert($lock['status'] === 303 && ($locked['active'] ?? true) === false, 'manager can lock account');
    $unlock = $request($baseUrl . '/admin/users/' . rawurlencode($ids['customer']) . '/status', $sessions['manager'], 'POST', ['csrf_token' => $statusToken, 'active' => '1']);
    $unlocked = $client->request('GET', $dbPath . '/' . rawurlencode($ids['customer']))->json();
    $assert($unlock['status'] === 303 && ($unlocked['active'] ?? false) === true, 'manager can unlock account');
    $noCsrf = $request($baseUrl . '/admin/users/' . rawurlencode($ids['customer']) . '/status', $sessions['manager'], 'POST', ['active' => '0']);
    $unchanged = $client->request('GET', $dbPath . '/' . rawurlencode($ids['customer']))->json();
    $assert($noCsrf['status'] === 303 && ($unchanged['active'] ?? false) === true, 'missing CSRF token does not change account status');

    $newForm = $request($baseUrl . '/admin/users/new', $sessions['manager']);
    $newToken = $csrf($newForm['body']);
    $newEmail = 'created-' . $nonce . '@example.invalid';
    $createStaff = $request($baseUrl . '/admin/users/new', $sessions['manager'], 'POST', [
        'csrf_token' => $newToken, 'name' => 'Created HTTP Staff', 'email' => $newEmail,
        'phone' => '+84 933 444 555', 'password' => 'CreatedRuntime!91', 'password_confirmation' => 'CreatedRuntime!91',
    ]);
    $createdStaff = $client->request('POST', $dbPath . '/_find', ['selector' => ['type' => 'staff', 'auth.username' => $newEmail], 'limit' => 2])->json()['docs'] ?? [];
    $assert($createStaff['status'] === 303 && count($createdStaff) === 1 && ($createdStaff[0]['role'] ?? null) === 'staff', 'manager can create active Staff account');

    $backupPage = $request($baseUrl . '/admin/backup', $sessions['manager']);
    $backupToken = $csrf($backupPage['body']);
    $beforeNoCsrf = array_fill_keys(array_map('basename', glob($backupDirectory . '/*.json') ?: []), true);
    $backupNoCsrf = $request($baseUrl . '/admin/backup/create', $sessions['manager'], 'POST', []);
    $assert($backupNoCsrf['status'] === 403, 'backup creation rejects missing CSRF token');
    $createBackup = $request($baseUrl . '/admin/backup/create', $sessions['manager'], 'POST', ['csrf_token' => $backupToken]);
    $backupPageAfter = $request($baseUrl . '/admin/backup', $sessions['manager']);
    preg_match_all('/backup-manager-[A-Za-z0-9._-]+\.json/', $backupPageAfter['body'], $matches);
    $newBackupNames = array_values(array_unique(array_filter($matches[0] ?? [], static fn (string $name): bool => !isset($beforeNoCsrf[$name]))));
    $assert($createBackup['status'] === 303 && count($newBackupNames) === 1, 'manager can create a uniquely named backup');
    $download = $request($baseUrl . '/admin/backup/download/' . rawurlencode($newBackupNames[0]), $sessions['manager']);
    $payload = json_decode($download['body'], true);
    $assert($download['status'] === 200 && str_contains($download['headers'], 'Content-Disposition: attachment; filename="' . $newBackupNames[0] . '"')
        && ($payload['source_database'] ?? null) === $database && ($payload['document_count'] ?? 0) > 0, 'manager can securely download generated backup JSON');
    $traversal = $request($baseUrl . '/admin/backup/download/%2e%2e', $sessions['manager']);
    $assert(in_array($traversal['status'], [400, 404], true), 'backup download rejects a traversal-style filename');

    foreach (['staff', 'customer'] as $kind) {
        $loginResponse = $login($baseUrl, $sessions[$kind], $emails[$kind], $passwords[$kind]);
        $assert($loginResponse['status'] === 303, $kind . ' can log in over HTTP');
        foreach (['/admin/users', '/admin/backup'] as $path) {
            $denied = $request($baseUrl . $path, $sessions[$kind]);
            $assert($denied['status'] === 403, $kind . ' is denied ' . $path);
        }
        $postDenied = $request($baseUrl . '/admin/backup/create', $sessions[$kind], 'POST', ['csrf_token' => 'invalid']);
        $assert($postDenied['status'] === 403, $kind . ' cannot create backup');
        $downloadDenied = $request($baseUrl . '/admin/backup/download/' . rawurlencode($newBackupNames[0]), $sessions[$kind]);
        $assert($downloadDenied['status'] === 403, $kind . ' cannot download backup');
    }

    $result = ['database' => $database, 'checks' => $checks, 'check_count' => count($checks), 'status' => 'PASS'];
    $evidenceDirectory = $projectRoot . '/docs/phase4';
    if (!is_dir($evidenceDirectory)) mkdir($evidenceDirectory, 0775, true);
    file_put_contents($evidenceDirectory . '/admin_users_http_evidence.json', json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL);
    fwrite(STDOUT, 'PASS: ' . count($checks) . ' HTTP integration checks. Database: ' . $database . PHP_EOL);
} catch (Throwable $exception) {
    $result = ['database' => $database, 'checks' => $checks, 'check_count' => count($checks), 'status' => 'FAIL', 'error' => $exception->getMessage()];
    $evidenceDirectory = $projectRoot . '/docs/phase4';
    if (!is_dir($evidenceDirectory)) mkdir($evidenceDirectory, 0775, true);
    file_put_contents($evidenceDirectory . '/admin_users_http_evidence.json', json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL);
    fwrite(STDERR, 'FAIL: ' . $exception->getMessage() . PHP_EOL);
    exitCode:
    $exitCode = 1;
} finally {
    if (is_resource($server)) { proc_terminate($server); proc_close($server); }
    foreach ($cookieJars as $jar) @unlink($jar);
    if (is_string($probeJar)) @unlink($probeJar);
    if (is_string($logPath)) @unlink($logPath);
    foreach (glob($backupDirectory . '/*.json') ?: [] as $path) {
        if (!isset($existingBackups[basename($path)]) && str_starts_with(basename($path), 'backup-manager-')) @unlink($path);
    }
    if ($databaseCreated) {
        $deleted = $client->request('DELETE', $dbPath);
        if (!in_array($deleted->statusCode, [200, 202, 404], true)) fwrite(STDERR, 'WARNING: scratch database cleanup returned HTTP ' . $deleted->statusCode . PHP_EOL);
    }
}
if (isset($exitCode)) exit($exitCode);
