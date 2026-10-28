<?php

declare(strict_types=1);

use App\Auth\AccountRepository;
use App\Auth\AccountService;
use App\Auth\AuthException;
use App\Auth\UserAdministrationService;
use App\Infrastructure\CouchDB\CouchDbClient;

$database = getenv('COUCHDB_DATABASE') ?: '';
if (preg_match('/^(?:user_admin_feature_test|user_admin_feature_[A-Za-z0-9_-]+_test)$/', $database) !== 1) {
    fwrite(STDERR, "Refusing to run: set COUCHDB_DATABASE to a dedicated user_admin_feature_*_test database.\n");
    exit(2);
}
require dirname(__DIR__) . '/vendor/autoload.php';

$client = new CouchDbClient(
    getenv('COUCHDB_URL') ?: 'http://couchdb:5984',
    getenv('COUCHDB_USER') ?: '',
    getenv('COUCHDB_PASSWORD') ?: '',
);
$dbPath = rawurlencode($database);
$created = $client->request('PUT', $dbPath);
if (!in_array($created->statusCode, [201, 202, 412], true)) {
    throw new RuntimeException('Could not create the isolated user administration test database (HTTP ' . $created->statusCode . ').');
}
$databaseCreated = in_array($created->statusCode, [201, 202], true);
$indexList = $client->request('GET', $dbPath . '/_index');
if ($indexList->statusCode < 200 || $indexList->statusCode >= 300) {
    throw new RuntimeException('Could not inspect indexes in the isolated test database (HTTP ' . $indexList->statusCode . ').');
}
$hasAccountIndex = false;
foreach (($indexList->json()['indexes'] ?? []) as $existingIndex) {
    if (is_array($existingIndex)
        && ($existingIndex['ddoc'] ?? null) === '_design/catalog_indexes'
        && ($existingIndex['name'] ?? null) === 'account_usernames') {
        $hasAccountIndex = true;
        break;
    }
}
if (!$hasAccountIndex) {
    $index = $client->request('POST', $dbPath . '/_index', [
        'index' => ['fields' => [['type' => 'asc'], ['auth.username' => 'asc']]],
        'ddoc' => 'catalog_indexes',
        'name' => 'account_usernames',
        'type' => 'json',
    ]);
    if ($index->statusCode < 200 || $index->statusCode >= 300) {
        throw new RuntimeException('Could not ensure the account username index (HTTP ' . $index->statusCode . ').');
    }
}

$accounts = new AccountRepository($client, $database);
$auth = new AccountService($accounts);
$administration = new UserAdministrationService($accounts);
$nonce = bin2hex(random_bytes(5));
$password = 'AdminUserTest!9';
$ids = [
    'manager' => 'staff:USERADMINMGR' . strtoupper($nonce),
    'other_manager' => 'staff:USERADMINMGR2' . strtoupper($nonce),
    'staff' => 'staff:USERADMINSTAFF' . strtoupper($nonce),
    'customer' => 'customer:USERADMINCUS' . strtoupper($nonce),
];
$make = static function (string $id, string $type, string $role, string $suffix) use ($nonce, $password): array {
    $email = $nonce . $suffix . '@example.invalid';
    return [
        '_id' => $id,
        'type' => $type,
        'schema_version' => 2,
        'legacy_id' => strtoupper(str_replace(':', '', $id)),
        'auth' => ['username' => $email, 'password_hash' => password_hash($password, PASSWORD_DEFAULT), 'requires_password_reset' => false],
        'profile' => ['name' => 'Smoke ' . $suffix, 'email' => $email, 'phone' => ''],
        'active' => true,
        'meta' => ['source' => 'user administration smoke test', 'created_at' => gmdate('c')],
        ...($type === 'staff' ? ['role' => $role] : []),
    ];
};
$fixtures = [
    $make($ids['manager'], 'staff', 'manager', 'manager'),
    $make($ids['other_manager'], 'staff', 'manager', 'othermanager'),
    $make($ids['staff'], 'staff', 'staff', 'staff'),
    $make($ids['customer'], 'customer', '', 'customer'),
];
$createdIds = [];
$checks = 0;
$assert = static function (bool $condition, string $message) use (&$checks): void {
    if (!$condition) throw new RuntimeException('FAIL: ' . $message);
    $checks++;
};
try {
    foreach ($fixtures as $fixture) {
        $accounts->create($fixture);
        $createdIds[] = $fixture['_id'];
    }

    $manager = $auth->authenticate($fixtures[0]['auth']['username'], $password);
    $assert(($manager['role'] ?? null) === 'manager', 'manager fixture authenticates');
    $newStaffEmail = $nonce . 'createdstaff@example.invalid';
    $newStaff = $auth->createStaff('Created Staff', $newStaffEmail, '', $password, $password, 'smoke-manager');
    $createdIds[] = $newStaff['_id'];
    $assert(($newStaff['type'] ?? null) === 'staff' && ($newStaff['role'] ?? null) === 'staff' && ($newStaff['active'] ?? null) === true, 'new staff defaults to active staff role');
    $assert(($auth->authenticate($newStaffEmail, $password)['role'] ?? null) === 'staff', 'new staff credentials authenticate');
    $duplicateRejected = false;
    try {
        $auth->createStaff('Duplicate Staff', $newStaffEmail, '', $password, $password, 'smoke-manager');
    } catch (AuthException) {
        $duplicateRejected = true;
    }
    $assert($duplicateRejected, 'duplicate staff username is rejected');
    $rows = $administration->list(['type' => 'customer', 'status' => 'all', 'q' => 'customer']);
    $customerRow = array_values(array_filter($rows, static fn (array $row): bool => $row['id'] === $ids['customer']))[0] ?? null;
    $assert(is_array($customerRow), 'customer type and search filters return the fixture');
    $assert(!array_key_exists('auth', $customerRow) && !array_key_exists('password_hash', $customerRow), 'list projection does not contain credentials');
    $detail = $administration->detail($ids['customer']);
    $beforeEdit = $accounts->accountById($ids['staff']);
    if (!is_array($beforeEdit)) throw new RuntimeException('Could not load the profile edit fixture.');
    $edited = $administration->updateProfile($ids['staff'], (string) $beforeEdit['_rev'], 'Edited Smoke Staff', '+84 912 345 678', 'smoke-manager');
    $afterEdit = $accounts->accountById($ids['staff']);
    $assert(($edited['name'] ?? null) === 'Edited Smoke Staff' && ($edited['phone'] ?? null) === '+84 912 345 678', 'manager can update profile fields');
    $assert(is_array($afterEdit) && $afterEdit['auth']['username'] === $beforeEdit['auth']['username'] && $afterEdit['auth']['password_hash'] === $beforeEdit['auth']['password_hash'] && $afterEdit['role'] === $beforeEdit['role'], 'profile edit preserves username, password hash, and role');
    $assert(is_array($detail) && !array_key_exists('password_hash', $detail) && !array_key_exists('auth', $detail), 'detail projection does not contain credentials');

    $administration->setActive($ids['customer'], false, $ids['manager'], 'smoke-manager');
    $lockedLoginRejected = false;
    try {
        $auth->authenticate($fixtures[3]['auth']['username'], $password);
    } catch (AuthException) {
        $lockedLoginRejected = true;
    }
    $assert($lockedLoginRejected, 'locked customer cannot authenticate');
    $administration->setActive($ids['customer'], true, $ids['manager'], 'smoke-manager');
    $assert(($auth->authenticate($fixtures[3]['auth']['username'], $password)['active'] ?? false) === true, 'unlocked customer authenticates');

    $selfLockRejected = false;
    try {
        $administration->setActive($ids['manager'], false, $ids['manager'], 'smoke-manager');
    } catch (RuntimeException) {
        $selfLockRejected = true;
    }
    $assert($selfLockRejected, 'manager cannot lock self');
    $otherManagerLockRejected = false;
    try {
        $administration->setActive($ids['other_manager'], false, $ids['manager'], 'smoke-manager');
    } catch (RuntimeException) {
        $otherManagerLockRejected = true;
    }
    $assert($otherManagerLockRejected, 'manager accounts stay active');

    $staleA = $accounts->accountById($ids['staff']);
    $staleB = $accounts->accountById($ids['staff']);
    if (!is_array($staleA) || !is_array($staleB)) throw new RuntimeException('Could not load the revision conflict fixture.');
    $staleA['meta']['smoke_revision_writer'] = 'first';
    $accounts->saveAdministrationChange($staleA);
    $staleB['meta']['smoke_revision_writer'] = 'second';
    $revisionConflictRejected = false;
    try {
        $accounts->saveAdministrationChange($staleB);
    } catch (RuntimeException) {
        $revisionConflictRejected = true;
    }
    $assert($revisionConflictRejected, 'stale CouchDB revision is rejected');
    fwrite(STDOUT, 'PASS: ' . $checks . " isolated user-administration checks. Database: " . $database . "\n");
} finally {
    foreach ($createdIds as $id) {
        $account = $accounts->accountById($id);
        if ($account === null) continue;
        $deleted = $client->request('DELETE', $dbPath . '/' . rawurlencode($id) . '?rev=' . rawurlencode((string) $account['_rev']));
        if (!in_array($deleted->statusCode, [200, 202, 404], true)) {
            fwrite(STDERR, 'Warning: test fixture cleanup failed for ' . $id . ' (HTTP ' . $deleted->statusCode . ').' . "\n");
        }
    }
    if ($databaseCreated) {
        $deleted = $client->request('DELETE', $dbPath);
        if (!in_array($deleted->statusCode, [200, 202, 404], true)) {
            fwrite(STDERR, 'Warning: test database cleanup failed (HTTP ' . $deleted->statusCode . ').' . "\n");
        }
    }
}
