<?php

declare(strict_types=1);

require __DIR__ . '/fauxton_feature_common.php';

$client = featureClient();
$database = 'retail_attachment_scratch';
$created = false;
$evidence = ['feature' => 'attachment', 'database' => $database, 'cleanup' => false];
$ok = false;
try {
    featureCreateDb($client, $database);
    $created = true;
    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jpS8AAAAASUVORK5CYII=', true);
    if (!is_string($png) || !str_starts_with($png, "\x89PNG\r\n\x1a\n")) throw new RuntimeException('Could not generate the 1×1 PNG fixture.');
    $expectedMd5 = base64_encode(md5($png, true));
    $docId = 'product:attachment-demo';
    $createdDoc = featureJson($client, 'PUT', rawurlencode($database) . '/' . rawurlencode($docId), [
        '_id' => $docId,
        'type' => 'product',
        'schema_version' => 2,
        'legacy_id' => 'ATTACHMENT-DEMO',
        'name' => 'Scratch attachment product',
        'category' => 'demo',
        'images' => [],
        'variants' => [['variant_id' => 'ATTACHMENT-DEMO-A', 'size' => 'M', 'price' => 1, 'stock' => 1, 'active' => true]],
        'active' => true,
        '_attachments' => ['tiny.png' => ['content_type' => 'image/png', 'data' => base64_encode($png)]],
    ], [201]);
    $read = featureJson($client, 'GET', rawurlencode($database) . '/' . rawurlencode($docId) . '?attachments=true');
    $attachment = $read['_attachments']['tiny.png'] ?? [];
    $readBytes = base64_decode((string) ($attachment['data'] ?? ''), true);
    $digest = (string) ($attachment['digest'] ?? '');
    $attachmentEndpoint = rawurlencode($database) . '/' . rawurlencode($docId) . '/tiny.png';
    $deleteResponse = $client->request('DELETE', $attachmentEndpoint . '?rev=' . rawurlencode((string) $read['_rev']));
    if (!in_array($deleteResponse->statusCode, [200, 202], true)) throw new RuntimeException('Attachment delete returned HTTP ' . $deleteResponse->statusCode . ': ' . $deleteResponse->body);
    $after = featureJson($client, 'GET', rawurlencode($database) . '/' . rawurlencode($docId));
    $evidence += ['doc_revision' => $createdDoc['rev'], 'attachment_length_bytes' => strlen($png), 'attachment_content_type' => $attachment['content_type'] ?? null, 'expected_md5_base64' => $expectedMd5, 'couchdb_digest' => $digest, 'readback_md5_base64' => is_string($readBytes) ? base64_encode(md5($readBytes, true)) : null, 'delete_http_status' => $deleteResponse->statusCode, 'attachment_present_after_delete' => isset($after['_attachments']['tiny.png'])];
    $ok = is_string($readBytes) && hash_equals($png, $readBytes) && $digest === 'md5-' . $expectedMd5 && !isset($after['_attachments']['tiny.png']);
} catch (Throwable $exception) {
    $evidence['error'] = $exception->getMessage();
} finally {
    if ($created) $evidence['cleanup'] = featureDeleteDb($client, $database);
    if (!$evidence['cleanup']) $ok = false;
}
$evidence['status'] = $ok ? 'PASS' : 'FAIL';
featureOutput($evidence, $ok);
