<?php

declare(strict_types=1);

require __DIR__ . '/fauxton_feature_common.php';

$client = featureClient();
$database = 'retail_compaction_scratch';
$created = false;
$evidence = ['feature' => 'compaction_delete', 'database' => $database, 'cleanup' => false];
$ok = false;
try {
    featureCreateDb($client, $database);
    $created = true;
    $docId = 'revision-heavy-demo';
    $doc = ['_id' => $docId, 'type' => 'compaction_demo', 'revision_number' => 0, 'payload' => str_repeat('x', 2048)];
    $write = featureJson($client, 'PUT', rawurlencode($database) . '/' . rawurlencode($docId), $doc, [201]);
    $doc['_rev'] = $write['rev'];
    for ($revision = 1; $revision <= 50; ++$revision) {
        $doc['revision_number'] = $revision;
        $doc['payload'] = str_repeat(chr(65 + ($revision % 26)), 2048) . ':' . $revision;
        $write = featureJson($client, 'PUT', rawurlencode($database) . '/' . rawurlencode($docId), $doc, [201]);
        $doc['_rev'] = $write['rev'];
    }
    $before = featureJson($client, 'GET', rawurlencode($database));
    $compact = $client->request('POST', rawurlencode($database) . '/_compact', ['ok' => true]);
    if (!in_array($compact->statusCode, [202, 200], true)) throw new RuntimeException('POST _compact returned HTTP ' . $compact->statusCode . ': ' . $compact->body);
    $after = null;
    $attempts = 0;
    do {
        usleep(250000);
        $after = featureJson($client, 'GET', rawurlencode($database));
        ++$attempts;
    } while (($after['compact_running'] ?? false) === true && $attempts < 80);
    if (($after['compact_running'] ?? false) === true) throw new RuntimeException('Database compaction was still running after 20 seconds.');
    $delete = $client->request('DELETE', rawurlencode($database));
    if (!in_array($delete->statusCode, [200, 202], true)) throw new RuntimeException('Database delete returned HTTP ' . $delete->statusCode . ': ' . $delete->body);
    $created = false;
    $deletedCheck = $client->request('GET', rawurlencode($database));
    $sizeFields = static fn (array $info): array => [
        'doc_count' => $info['doc_count'] ?? null,
        'data_size' => $info['data_size'] ?? $info['sizes']['active'] ?? null,
        'data_size_source' => array_key_exists('data_size', $info) ? 'data_size' : (isset($info['sizes']['active']) ? 'sizes.active' : null),
        'disk_size' => $info['disk_size'] ?? $info['sizes']['file'] ?? null,
        'disk_size_source' => array_key_exists('disk_size', $info) ? 'disk_size' : (isset($info['sizes']['file']) ? 'sizes.file' : null),
        'runtime_sizes' => $info['sizes'] ?? null,
    ];
    $evidence += ['revisions_written' => 51, 'compaction_http' => $compact->statusCode, 'compaction_poll_count' => $attempts, 'before' => $sizeFields($before), 'after_compaction' => $sizeFields($after), 'delete_http' => $delete->statusCode, 'deleted_database_http' => $deletedCheck->statusCode];
    $ok = $deletedCheck->statusCode === 404 && ($before['doc_count'] ?? null) === 1 && ($after['doc_count'] ?? null) === 1;
} catch (Throwable $exception) {
    $evidence['error'] = $exception->getMessage();
} finally {
    if ($created) $evidence['cleanup'] = featureDeleteDb($client, $database);
    else $evidence['cleanup'] = true;
    if (!$evidence['cleanup']) $ok = false;
}
$evidence['status'] = $ok ? 'PASS' : 'FAIL';
featureOutput($evidence, $ok);
