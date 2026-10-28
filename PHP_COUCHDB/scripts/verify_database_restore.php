<?php

declare(strict_types=1);

use App\Infrastructure\CouchDB\CouchDbClient;
use App\Infrastructure\CouchDB\CouchDbResponse;

require __DIR__ . '/database_backup.php';

function restoreAssert(bool $condition, string $message): void
{
    if (!$condition) throw new RuntimeException($message);
}

function canonicalRestoreValue(mixed $value): mixed
{
    if (!is_array($value)) return $value;
    if (!array_is_list($value)) ksort($value, SORT_STRING);
    foreach ($value as $key => $item) $value[$key] = canonicalRestoreValue($item);
    return $value;
}

/** @param array<string, mixed> $document */
function comparableRestoreDocument(array $document): string
{
    $document = prepareRestoredDocument($document);
    if (is_array($document['_attachments'] ?? null)) {
        foreach ($document['_attachments'] as &$attachment) {
            if (is_array($attachment)) unset($attachment['revpos']);
        }
        unset($attachment);
    }
    return json_encode(canonicalRestoreValue($document), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
}

/** @param list<array<string, mixed>> $expected @param list<array<string, mixed>> $actual @return array{missing:list<string>, extra:list<string>, different:list<string>, expected_count:int, actual_count:int, pass:bool} */
function documentParityReport(array $expected, array $actual): array
{
    $byId = [];
    foreach ($actual as $document) $byId[$document['_id']] = $document;
    $expectedById = [];
    foreach ($expected as $document) $expectedById[$document['_id']] = $document;
    $missing = array_values(array_diff(array_keys($expectedById), array_keys($byId)));
    $extra = array_values(array_diff(array_keys($byId), array_keys($expectedById)));
    $different = [];
    foreach ($expected as $document) {
        $id = $document['_id'];
        if (isset($byId[$id]) && hash('sha256', comparableRestoreDocument($document)) !== hash('sha256', comparableRestoreDocument($byId[$id]))) $different[] = $id;
    }
    sort($missing, SORT_STRING); sort($extra, SORT_STRING); sort($different, SORT_STRING);
    $pass = $missing === [] && $extra === [] && $different === [] && count($byId) === count($actual);
    return ['missing' => $missing, 'extra' => $extra, 'different' => $different, 'expected_count' => count($expected), 'actual_count' => count($actual), 'pass' => $pass];
}

/** @return array<string, string> */
function indexSignatures(CouchDbClient $client, string $database): array
{
    $response = backupJson($client->request('GET', rawurlencode($database) . '/_index'), 'Reading Mango indexes');
    restoreAssert(is_array($response['indexes'] ?? null), 'Mango index list is missing.');
    $signatures = [];
    foreach ($response['indexes'] as $index) {
        restoreAssert(is_array($index) && is_string($index['name'] ?? null), 'Mango index entry is invalid.');
        $name = $index['name'];
        $signatures[$name] = json_encode(canonicalRestoreValue([
            'ddoc' => $index['ddoc'] ?? null,
            'type' => $index['type'] ?? null,
            'def' => $index['def'] ?? null,
        ]), JSON_THROW_ON_ERROR);
    }
    ksort($signatures, SORT_STRING);
    return $signatures;
}

/** @param array<string, mixed> $selector */
function verifyMango(CouchDbClient $client, string $target, string $index, array $selector, string $expectedId, bool $sortByDate = false): int
{
    $request = [
        'selector' => $selector,
        'use_index' => ['_design/catalog_indexes', $index],
        'fields' => ['_id'],
        'limit' => 100,
    ];
    if ($sortByDate) $request['sort'] = [['ordered_at' => 'desc']];
    $path = rawurlencode($target);
    $explain = backupJson($client->request('POST', $path . '/_explain', $request), 'Explaining Mango query');
    restoreAssert(($explain['index']['name'] ?? null) === $index, 'Mango query did not select index ' . $index . '.');
    $result = backupJson($client->request('POST', $path . '/_find', $request), 'Running Mango query');
    restoreAssert(is_array($result['docs'] ?? null), 'Mango query returned no docs list.');
    $ids = array_column($result['docs'], '_id');
    restoreAssert(in_array($expectedId, $ids, true), 'Mango query did not find expected order ' . $expectedId . '.');
    return count($ids);
}

try {
    $name = $argv[1] ?? '';
    $target = $argv[2] ?? '';
    restoreAssert(is_string($name) && preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]*\.json$/', $name) === 1,
        'Provide a simple backup filename.');
    safeDatabaseName($target);
    $path = dirname(__DIR__) . '/var/backups/' . $name;
    restoreAssert(is_file($path) && !is_link($path) && filesize($path) > 0, 'Backup file is missing or empty.');
    $payload = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    restoreAssert(is_array($payload), 'Backup JSON object is invalid.');
    $validated = validateRestorePayload($payload, $target);
    $expected = $validated['documents'];
    $source = $validated['source'];
    $client = couchClient();
    $sourceInfo = backupJson($client->request('GET', rawurlencode($source)), 'Reading source database');
    $targetInfo = backupJson($client->request('GET', rawurlencode($target)), 'Reading restore target');
    restoreAssert(($sourceInfo['doc_count'] ?? null) === count($expected), 'Source count differs from backup.');
    restoreAssert(($targetInfo['doc_count'] ?? null) === count($expected), 'Target count differs from backup.');
    $reader = static fn (string $path): CouchDbResponse => $client->request('GET', $path);
    $sourceDocuments = collectBackupDocuments($reader, $source, count($expected));
    $targetDocuments = collectBackupDocuments($reader, $target, count($expected));
    $sourceParity = documentParityReport($expected, $sourceDocuments);
    $targetParity = documentParityReport($expected, $targetDocuments);
    restoreAssert($sourceParity['pass'], 'Source document hash parity failed: ' . json_encode($sourceParity, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    restoreAssert($targetParity['pass'], 'Target document hash parity failed: ' . json_encode($targetParity, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    foreach ($targetDocuments as $document) {
        restoreAssert(is_string($document['_rev'] ?? null) && str_starts_with($document['_rev'], '1-'),
            'Target document does not have a new first-generation revision: ' . $document['_id'] . '.');
    }

    $designIds = array_values(array_filter(array_column($expected, '_id'),
        static fn (string $id): bool => str_starts_with($id, '_design/')));
    foreach ($designIds as $id) {
        $response = $client->request('GET', rawurlencode($target) . '/' . $id);
        restoreAssert($response->statusCode === 200, 'Restored design document is missing: ' . $id . '.');
    }
    $sourceIndexes = indexSignatures($client, $source);
    $targetIndexes = indexSignatures($client, $target);
    restoreAssert($sourceIndexes === $targetIndexes, 'Source and target Mango indexes differ.');
    $sourceSecurity = backupJson($client->request('GET', rawurlencode($source) . '/_security'), 'Reading source security');
    $targetSecurity = backupJson($client->request('GET', rawurlencode($target) . '/_security'), 'Reading target security');
    $securityExpected = $validated['security'] ?? null;
    $securityPass = $securityExpected === null || json_encode(canonicalRestoreValue($sourceSecurity), JSON_THROW_ON_ERROR) === json_encode(canonicalRestoreValue($securityExpected), JSON_THROW_ON_ERROR);
    $securityPass = $securityPass && json_encode(canonicalRestoreValue($targetSecurity), JSON_THROW_ON_ERROR) === json_encode(canonicalRestoreValue($securityExpected ?? $sourceSecurity), JSON_THROW_ON_ERROR);
    restoreAssert($securityPass, 'Source, backup, and target _security objects differ.');

    $orders = array_values(array_filter($expected, static fn (array $document): bool => ($document['type'] ?? null) === 'order'));
    restoreAssert($orders !== [], 'Backup contains no order for Mango verification.');
    $products = array_values(array_filter($expected, static fn (array $document): bool => ($document['type'] ?? null) === 'product'));
    restoreAssert($products !== [] && ($products[0]['schema_version'] ?? null) === 2
        && is_array($products[0]['variants'] ?? null) && is_array($products[0]['images'] ?? null),
        'Sample product fields are incomplete.');
    $statusOrder = current(array_filter($orders, static fn (array $order): bool => is_string($order['status'] ?? null)));
    $customerOrder = current(array_filter($orders, static fn (array $order): bool => is_string($order['customer']['customer_id'] ?? null)
        && $order['customer']['customer_id'] !== ''));
    $trackingOrder = current(array_filter($orders, static fn (array $order): bool => is_string($order['delivery_tracking']['tracking_code'] ?? null)
        && $order['delivery_tracking']['tracking_code'] !== ''));
    restoreAssert(is_array($statusOrder) && is_array($customerOrder) && is_array($trackingOrder),
        'Backup lacks a status, customer or tracking order for Mango verification.');
    restoreAssert(($statusOrder['schema_version'] ?? null) === 2
        && is_array($statusOrder['items'] ?? null) && is_array($statusOrder['totals'] ?? null)
        && is_array($statusOrder['payment'] ?? null) && is_array($statusOrder['status_history'] ?? null)
        && is_array($trackingOrder['delivery_tracking']['history'] ?? null),
        'Sample order or delivery fields are incomplete.');
    $statusMatches = verifyMango($client, $target, 'admin_orders',
        ['type' => ['$eq' => 'order'], 'status' => ['$eq' => $statusOrder['status']]], $statusOrder['_id'], true);
    $customerMatches = verifyMango($client, $target, 'customer_orders',
        ['type' => ['$eq' => 'order'], 'customer.customer_id' => ['$eq' => $customerOrder['customer']['customer_id']]], $customerOrder['_id'], true);
    $trackingMatches = verifyMango($client, $target, 'delivery_tracking_code',
        ['type' => ['$eq' => 'order'], 'delivery_tracking.tracking_code' => ['$eq' => $trackingOrder['delivery_tracking']['tracking_code']]], $trackingOrder['_id']);

    $validatorId = '_design/domain_validation';
    restoreAssert(in_array($validatorId, $designIds, true), 'Backup lacks the expected validator.');
    $validator = backupJson($client->request('GET', rawurlencode($target) . '/' . $validatorId), 'Reading validator');
    restoreAssert(is_string($validator['validate_doc_update'] ?? null), 'Restored validator is incomplete.');
    $probeId = 'order:restore-validator-probe-' . bin2hex(random_bytes(6));
    $probe = prepareRestoredDocument($statusOrder);
    $probe['_id'] = $probeId;
    $probe['status'] = '__invalid_restore_status__';
    $rejected = $client->request('PUT', rawurlencode($target) . '/' . rawurlencode($probeId), $probe);
    $error = $rejected->json();
    restoreAssert($rejected->statusCode === 403 && ($error['error'] ?? null) === 'forbidden',
        'Restored validator did not reject an invalid order status.');
    restoreAssert($client->request('GET', rawurlencode($target) . '/' . rawurlencode($probeId))->statusCode === 404,
        'Rejected validator probe unexpectedly exists.');
    $afterProbe = backupJson($client->request('GET', rawurlencode($target)), 'Rechecking restore target');
    restoreAssert(($afterProbe['doc_count'] ?? null) === count($expected), 'Validator probe changed the target document count.');

    $types = [];
    foreach ($expected as $document) if (is_string($document['type'] ?? null)) $types[$document['type']] = true;
    ksort($types, SORT_STRING);
    fwrite(STDOUT, 'Source database: ' . $source . PHP_EOL);
    fwrite(STDOUT, 'Target database: ' . $target . PHP_EOL);
    fwrite(STDOUT, 'Backup documents: ' . count($expected) . PHP_EOL);
    fwrite(STDOUT, 'Restored documents: ' . count($targetDocuments) . PHP_EOL);
    fwrite(STDOUT, 'Design documents: ' . count($designIds) . ' (' . implode(', ', $designIds) . ')' . PHP_EOL);
    fwrite(STDOUT, 'Business types: ' . implode(', ', array_keys($types)) . PHP_EOL);
    fwrite(STDOUT, 'Sample IDs: ' . $products[0]['_id'] . ', ' . $statusOrder['_id'] . ', ' . $trackingOrder['_id'] . PHP_EOL);
    fwrite(STDOUT, 'Mango order status/customer/tracking: ' . $statusMatches . '/' . $customerMatches . '/' . $trackingMatches . ' result(s), expected IDs found' . PHP_EOL);
    fwrite(STDOUT, 'Mango indexes: ' . count($targetIndexes) . ', source/target parity PASS' . PHP_EOL);
    fwrite(STDOUT, 'Validator rejection: HTTP 403 PASS' . PHP_EOL);
    fwrite(STDOUT, 'Document count parity: PASS' . PHP_EOL);
    fwrite(STDOUT, 'Content SHA-256 parity (normalized JSON, _rev excluded): PASS' . PHP_EOL);
    fwrite(STDOUT, 'Missing/extra/different documents: ' . json_encode(['source' => $sourceParity, 'target' => $targetParity], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL);
    fwrite(STDOUT, 'Database _security parity: PASS' . PHP_EOL);
    fwrite(STDOUT, 'New target revisions: PASS' . PHP_EOL);
    fwrite(STDOUT, 'Source content parity: PASS' . PHP_EOL);
    fwrite(STDOUT, 'Restore verification: PASS' . PHP_EOL);
} catch (Throwable $exception) {
    failBackup('Restore verification failed: ' . $exception->getMessage());
}
