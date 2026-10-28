<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Infrastructure\CouchDB\CouchDbClient;

$client = new CouchDbClient(
    (string) getenv('COUCHDB_URL'),
    (string) getenv('COUCHDB_USER'),
    (string) getenv('COUCHDB_PASSWORD')
);

$db = 'shopquan_ao_sql_migration_test';

// Cấp mật khẩu cho staff:NV002 (username: staff, role: staff)
$res = $client->request('GET', $db . '/staff%3ANV002');
if ($res->statusCode === 200) {
    $doc = $res->json();
    $doc['auth']['password_hash'] = password_hash('Staff@123456', PASSWORD_DEFAULT);
    $doc['auth']['requires_password_reset'] = false;
    $resPut = $client->request('PUT', $db . '/staff%3ANV002', $doc);
    echo "Set staff (staff:NV002) in {$db} => HTTP " . $resPut->statusCode . "\n";
}

// Cấp mật khẩu cho customer:KH002 và customer:KH003
foreach (['customer%3AKH002', 'customer%3AKH003'] as $id) {
    $res = $client->request('GET', $db . '/' . $id);
    if ($res->statusCode === 200) {
        $doc = $res->json();
        $doc['auth']['password_hash'] = password_hash('Demo@123456', PASSWORD_DEFAULT);
        $doc['auth']['requires_password_reset'] = false;
        $client->request('PUT', $db . '/' . $id, $doc);
        echo "Set {$doc['auth']['username']} in {$db} OK\n";
    }
}
