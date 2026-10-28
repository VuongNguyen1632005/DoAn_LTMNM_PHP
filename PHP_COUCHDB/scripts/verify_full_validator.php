<?php

declare(strict_types=1);

use App\Infrastructure\CouchDB\CouchDbClient;

require dirname(__DIR__) . '/vendor/autoload.php';

$source = getenv('COUCHDB_DATABASE') ?: '';
if (!str_ends_with($source, '_test')) {
    fwrite(STDERR, "Refusing to run validator verification unless COUCHDB_DATABASE ends in _test.\n");
    exit(2);
}
foreach (['COUCHDB_URL', 'COUCHDB_USER', 'COUCHDB_PASSWORD'] as $key) {
    if (trim((string) getenv($key)) === '') throw new RuntimeException('Missing CouchDB configuration: ' . $key);
}
$client = new CouchDbClient((string) getenv('COUCHDB_URL'), (string) getenv('COUCHDB_USER'), (string) getenv('COUCHDB_PASSWORD'));
$database = 'retail_full_validator_' . bin2hex(random_bytes(4)) . '_test';
$path = rawurlencode($database);
$created = false;
$cases = [];
$now = gmdate('c');
$event = static fn (string $status, string $at): array => ['status' => $status, 'at' => $at, 'note' => 'Validator verification', 'updated_by' => 'validator-test'];
$baseOrder = static function (string $id, string $status = 'pending'): array {
    return [
        '_id' => $id, 'type' => 'order', 'schema_version' => 2, 'legacy_id' => strtoupper(str_replace(':', '-', $id)),
        'ordered_at' => gmdate('c'), 'status' => $status, 'customer' => null, 'guest_order' => true,
        'receiver' => ['name' => 'Validator Fixture', 'phone' => '0900000000', 'address' => 'Runtime test'],
        'items' => [['product_id' => 'product:fixture', 'variant_id' => 'FIXTURE-M', 'quantity' => 1, 'unit_price' => 100, 'line_total' => 100]],
        'totals' => ['total_quantity' => 1, 'subtotal' => 100, 'shipping_fee' => 0, 'discount_amount' => 0, 'grand_total' => 100],
        'payment' => ['method' => 'cod', 'status' => 'unpaid', 'paid' => false],
        'status_history' => [['status' => $status, 'at' => gmdate('c'), 'source' => 'validator-test']],
    ];
};
$send = static function (string $method, string $url, ?array $body = null) use ($client): array {
    $response = $client->request($method, $url, $body);
    return ['status' => $response->statusCode, 'body' => $response->body];
};
$makeTracking = static function (array $events, string $status): array {
    return ['tracking_code' => 'VAL-TRACK-01', 'status' => $status, 'estimated_delivery_date' => gmdate('Y-m-d', strtotime('+3 days')),
        'delivered_at' => null, 'history' => $events];
};
$putNew = static function (array $document) use ($send, $path): array {
    return $send('PUT', $path . '/' . rawurlencode($document['_id']), $document);
};
$read = static function (string $id) use ($send, $path): array {
    $response = $send('GET', $path . '/' . rawurlencode($id));
    if ($response['status'] !== 200) throw new RuntimeException('Could not read validator fixture ' . $id . '.');
    return json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);
};
$record = static function (string $name, array $request, array $response, int $expectedStatus, ?string $expectedReason = null) use (&$cases): void {
    $json = json_decode($response['body'], true);
    $pass = $response['status'] === $expectedStatus
        && ($expectedReason === null || (($json['reason'] ?? null) === $expectedReason));
    $cases[] = ['case' => $name, 'expected_http' => $expectedStatus, 'actual_http' => $response['status'],
        'expected_reason' => $expectedReason, 'response_body' => $response['body'], 'request' => $request, 'result' => $pass ? 'PASS' : 'FAIL'];
    if (!$pass) throw new RuntimeException($name . ' expected HTTP ' . $expectedStatus . ' but got HTTP ' . $response['status'] . ' (' . $response['body'] . ').');
};

try {
    $createdResponse = $client->request('PUT', $path);
    if ($createdResponse->statusCode !== 201) throw new RuntimeException('Scratch database creation returned HTTP ' . $createdResponse->statusCode . '.');
    $created = true;
    $validator = json_decode((string) file_get_contents(dirname(__DIR__) . '/database/couchdb/design-docs/domain_validation.json'), true, 512, JSON_THROW_ON_ERROR);
    if (!is_string($validator['validate_doc_update'] ?? null) || strlen($validator['validate_doc_update']) < 10000) {
        throw new RuntimeException('Full checked-in domain validator is missing or unexpectedly short.');
    }
    $validatorResponse = $client->request('PUT', $path . '/_design%2Fdomain_validation', $validator);
    if ($validatorResponse->statusCode !== 201) throw new RuntimeException('Full validator installation returned HTTP ' . $validatorResponse->statusCode . '.');

    $invalidStatus = $baseOrder('order:validator-status');
    $insert = $putNew($invalidStatus);
    if ($insert['status'] !== 201) throw new RuntimeException('Could not create invalid-status fixture base.');
    $invalidStatus['_rev'] = $read($invalidStatus['_id'])['_rev'];
    $invalidStatus['status'] = 'invalid_status';
    $record('invalid order status', $invalidStatus, $putNew($invalidStatus), 403, 'order requires a valid status, receiver, and non-empty items');

    $historyOrder = $baseOrder('order:validator-history', 'shipping');
    $historyOrder['delivery_tracking'] = $makeTracking([$event('created', $now)], 'created');
    $insert = $putNew($historyOrder);
    if ($insert['status'] !== 201) throw new RuntimeException('Could not create delivery-history fixture. ' . $insert['body']);
    $historyOrder = $read($historyOrder['_id']);
    $historyOrder['delivery_tracking'] = $makeTracking([$event('created', $now), $event('picked_up', gmdate('c', strtotime('+1 second')))], 'picked_up');
    $advanced = $putNew($historyOrder);
    if ($advanced['status'] !== 201) throw new RuntimeException('Could not advance delivery-history fixture. ' . $advanced['body']);
    $shortHistory = $read($historyOrder['_id']);
    $shortHistory['delivery_tracking'] = $makeTracking([$event('created', $now)], 'created');
    $record('delivery history cannot be shortened', $shortHistory, $putNew($shortHistory), 403, 'delivery history is append-only');

    $paidOrder = $baseOrder('order:validator-paid', 'delivered');
    $paidOrder['payment'] = ['method' => 'bank_transfer', 'status' => 'paid', 'paid' => true, 'paid_at' => $now];
    $insert = $putNew($paidOrder);
    if ($insert['status'] !== 201) throw new RuntimeException('Could not create paid-order fixture. ' . $insert['body']);
    $paidUpdate = $read($paidOrder['_id']);
    $paidUpdate['payment'] = ['method' => 'bank_transfer', 'status' => 'unpaid', 'paid' => false];
    $record('paid order cannot revert to unpaid', $paidUpdate, $putNew($paidUpdate), 403, 'paid orders cannot be marked unpaid');

    $transitionOrder = $baseOrder('order:validator-transition', 'shipping');
    $transitionOrder['delivery_tracking'] = $makeTracking([$event('created', $now)], 'created');
    $insert = $putNew($transitionOrder);
    if ($insert['status'] !== 201) throw new RuntimeException('Could not create delivery-transition fixture. ' . $insert['body']);
    $badTransition = $read($transitionOrder['_id']);
    $badTransition['delivery_tracking'] = $makeTracking([$event('created', $now), $event('in_transit', gmdate('c', strtotime('+1 second')))], 'in_transit');
    $record('invalid delivery transition', $badTransition, $putNew($badTransition), 403, 'delivery status transition is not allowed');

    $validOrder = $baseOrder('order:validator-valid', 'shipping');
    $validOrder['delivery_tracking'] = $makeTracking([$event('created', $now)], 'created');
    $createdValid = $putNew($validOrder);
    $record('valid initial tracked order', $validOrder, $createdValid, 201);
    $validUpdate = $read($validOrder['_id']);
    $validUpdate['delivery_tracking'] = $makeTracking([$event('created', $now), $event('picked_up', gmdate('c', strtotime('+1 second')))], 'picked_up');
    $record('valid delivery transition', $validUpdate, $putNew($validUpdate), 201);

    $result = ['database' => $database, 'validator_file' => 'database/couchdb/design-docs/domain_validation.json',
        'validator_bytes' => strlen($validator['validate_doc_update']), 'cases' => $cases, 'check_count' => count($cases), 'status' => 'PASS'];
    $output = dirname(__DIR__) . '/docs/phase4/validator_tests_evidence.json';
    if (!is_dir(dirname($output))) mkdir(dirname($output), 0775, true);
    file_put_contents($output, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL);
    fwrite(STDOUT, 'PASS: ' . count($cases) . ' full-validator cases. Database: ' . $database . PHP_EOL);
} catch (Throwable $exception) {
    $result = ['database' => $database, 'cases' => $cases, 'check_count' => count($cases), 'status' => 'FAIL', 'error' => $exception->getMessage()];
    $output = dirname(__DIR__) . '/docs/phase4/validator_tests_evidence.json';
    if (!is_dir(dirname($output))) mkdir(dirname($output), 0775, true);
    file_put_contents($output, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL);
    fwrite(STDERR, 'FAIL: ' . $exception->getMessage() . PHP_EOL);
    $exitCode = 1;
} finally {
    if ($created) {
        $deleted = $client->request('DELETE', $path);
        if (!in_array($deleted->statusCode, [200, 202, 404], true)) fwrite(STDERR, 'WARNING: validator scratch cleanup returned HTTP ' . $deleted->statusCode . PHP_EOL);
        elseif ($client->request('GET', $path)->statusCode !== 404) fwrite(STDERR, 'WARNING: validator scratch cleanup did not verify as HTTP 404.' . PHP_EOL);
    }
}
if (isset($exitCode)) exit($exitCode);
