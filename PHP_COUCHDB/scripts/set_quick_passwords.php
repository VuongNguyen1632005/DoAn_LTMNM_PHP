<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Infrastructure\CouchDB\CouchDbClient;

$client = new CouchDbClient(
    (string) getenv('COUCHDB_URL'),
    (string) getenv('COUCHDB_USER'),
    (string) getenv('COUCHDB_PASSWORD')
);

// 1. Cập nhật mật khẩu trong database hiện tại: shopquan_ao_sql_migration_test
$db1 = 'shopquan_ao_sql_migration_test';

$res = $client->request('GET', $db1 . '/staff%3ANV001');
if ($res->statusCode === 200) {
    $doc = $res->json();
    $doc['auth']['password_hash'] = password_hash('Admin@123456', PASSWORD_DEFAULT);
    $doc['auth']['requires_password_reset'] = false;
    $client->request('PUT', $db1 . '/staff%3ANV001', $doc);
    echo "Set admin (staff:NV001) in {$db1} OK\n";
}

$res = $client->request('GET', $db1 . '/customer%3AKH001');
if ($res->statusCode === 200) {
    $doc = $res->json();
    $doc['auth']['password_hash'] = password_hash('Demo@123456', PASSWORD_DEFAULT);
    $doc['auth']['requires_password_reset'] = false;
    $client->request('PUT', $db1 . '/customer%3AKH001', $doc);
    echo "Set demo1@gmail.com (customer:KH001) in {$db1} OK\n";
}

// Thêm luôn tài khoản customer1@example.test vào shopquan_ao_sql_migration_test nếu chưa có
$res = $client->request('GET', $db1 . '/customer%3AP06-001');
if ($res->statusCode !== 200) {
    $newCustomer = [
        '_id' => 'customer:P06-001',
        'type' => 'customer',
        'schema_version' => 2,
        'legacy_id' => 'P06-001',
        'auth' => [
            'username' => 'customer1@example.test',
            'password_hash' => password_hash('Demo@123456', PASSWORD_DEFAULT),
            'requires_password_reset' => false
        ],
        'profile' => [
            'name' => 'An Nguyễn',
            'phone' => '0900000001',
            'email' => 'customer1@example.test',
            'addresses' => []
        ],
        'active' => true
    ];
    $client->request('PUT', $db1 . '/customer%3AP06-001', $newCustomer);
    echo "Created customer1@example.test in {$db1} OK\n";
} else {
    $doc = $res->json();
    $doc['auth']['password_hash'] = password_hash('Demo@123456', PASSWORD_DEFAULT);
    $doc['auth']['requires_password_reset'] = false;
    $client->request('PUT', $db1 . '/customer%3AP06-001', $doc);
    echo "Updated customer1@example.test in {$db1} OK\n";
}

// 2. Cập nhật mật khẩu trong database: shopquan_ao_phase06_demo_test
$db2 = 'shopquan_ao_phase06_demo_test';

$res = $client->request('GET', $db2 . '/customer%3AP06-001');
if ($res->statusCode === 200) {
    $doc = $res->json();
    $doc['auth']['password_hash'] = password_hash('Demo@123456', PASSWORD_DEFAULT);
    $doc['auth']['requires_password_reset'] = false;
    $client->request('PUT', $db2 . '/customer%3AP06-001', $doc);
    echo "Set customer1@example.test in {$db2} OK\n";
}

$res = $client->request('GET', $db2 . '/staff%3AP06-002');
if ($res->statusCode === 200) {
    $doc = $res->json();
    $doc['auth']['password_hash'] = password_hash('Admin@123456', PASSWORD_DEFAULT);
    $doc['auth']['requires_password_reset'] = false;
    $client->request('PUT', $db2 . '/staff%3AP06-002', $doc);
    echo "Set manager.demo@example.test in {$db2} OK\n";
}
