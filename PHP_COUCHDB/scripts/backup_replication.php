<?php

declare(strict_types=1);

require __DIR__ . '/fauxton_feature_common.php';

$client = featureClient();
$source = (string) (getenv('DEMO_DATABASE') ?: 'retail_order_delivery_demo');
$target = 'retail_order_delivery_backup_scratch';
$created = false;
$evidence = ['feature' => 'backup_replication', 'source' => $source, 'target' => $target, 'cleanup' => false];
$ok = false;
try {
    if (!str_ends_with($source, '_demo')) throw new RuntimeException('Backup replication source must be *_demo.');
    featureCreateDb($client, $target);
    $created = true;
    $selector = ['_id' => ['$ne' => '_design/domain_validation']];
    $replication = featureReplicate($client, $source, $target, false, false, $selector);
    $validator = featureJson($client, 'GET', rawurlencode($source) . '/_design/domain_validation');
    unset($validator['_rev']);
    featureJson($client, 'PUT', rawurlencode($target) . '/_design/domain_validation', $validator, [201]);
    $security = featureJson($client, 'GET', rawurlencode($source) . '/_security');
    $securityWrite = $client->request('PUT', rawurlencode($target) . '/_security', $security);
    if (!in_array($securityWrite->statusCode, [200, 201], true)) throw new RuntimeException('Copying _security returned HTTP ' . $securityWrite->statusCode . '.');
    $sourceDocs = featureDocs($client, $source);
    $targetDocs = featureDocs($client, $target);
    $sourceHashes = featureContentHashes($sourceDocs);
    $targetHashes = featureContentHashes($targetDocs);
    $missing = array_values(array_diff(array_keys($sourceHashes), array_keys($targetHashes)));
    $extra = array_values(array_diff(array_keys($targetHashes), array_keys($sourceHashes)));
    $different = [];
    foreach (array_intersect(array_keys($sourceHashes), array_keys($targetHashes)) as $id) if ($sourceHashes[$id] !== $targetHashes[$id]) $different[] = $id;
    $targetSecurity = featureJson($client, 'GET', rawurlencode($target) . '/_security');
    $sourceInfo = featureJson($client, 'GET', rawurlencode($source));
    $targetInfo = featureJson($client, 'GET', rawurlencode($target));
    $evidence += ['replication_filter' => $selector, 'replication' => $replication, 'source_doc_count' => $sourceInfo['doc_count'] ?? null, 'target_doc_count' => $targetInfo['doc_count'] ?? null, 'source_document_count_including_design' => count($sourceDocs), 'target_document_count_including_design' => count($targetDocs), 'missing_ids' => $missing, 'extra_ids' => $extra, 'different_content_ids' => $different, 'security_matches' => featureHash($security) === featureHash($targetSecurity)];
    $ok = ($sourceInfo['doc_count'] ?? null) === ($targetInfo['doc_count'] ?? null) && $missing === [] && $extra === [] && $different === [] && featureHash($security) === featureHash($targetSecurity);
} catch (Throwable $exception) {
    $evidence['error'] = $exception->getMessage();
} finally {
    if ($created) $evidence['cleanup'] = featureDeleteDb($client, $target);
    if (!$evidence['cleanup']) $ok = false;
}
$evidence['status'] = $ok ? 'PASS' : 'FAIL';
featureOutput($evidence, $ok);
