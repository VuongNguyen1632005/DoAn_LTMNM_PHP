<?php

declare(strict_types=1);

require __DIR__ . '/fauxton_feature_common.php';

$client = featureClient();
$source = (string) (getenv('DEMO_DATABASE') ?: 'retail_order_delivery_demo');
$target = 'retail_demo_replica_scratch';
$created = false;
$evidence = ['feature' => 'replication', 'source' => $source, 'target' => $target, 'cleanup' => false];
$ok = false;
try {
    if (!str_ends_with($source, '_demo')) throw new RuntimeException('Replication source must be the seeded *_demo database.');
    featureCreateDb($client, $target);
    $created = true;
    $replicationFilter = ['_id' => ['$ne' => '_design/domain_validation']];
    $oneShot = featureReplicate($client, $source, $target, false, false, $replicationFilter);
    // Installing the validator only after source documents arrive avoids replaying historical
    // valid revisions as new writes, which the append-only transition validator correctly rejects.
    $validator = featureJson($client, 'GET', rawurlencode($source) . '/_design/domain_validation');
    unset($validator['_rev']);
    $validatorInstall = featureJson($client, 'PUT', rawurlencode($target) . '/_design/domain_validation', $validator, [201]);
    $sourceDocs = featureDocs($client, $source);
    $targetDocs = featureDocs($client, $target);
    $sourceHashes = featureContentHashes($sourceDocs);
    $targetHashes = featureContentHashes($targetDocs);
    $mismatch = [];
    foreach (array_unique(array_merge(array_keys($sourceHashes), array_keys($targetHashes))) as $id) if (($sourceHashes[$id] ?? null) !== ($targetHashes[$id] ?? null)) $mismatch[] = $id;
    $sourceInfo = featureJson($client, 'GET', rawurlencode($source));
    $targetInfo = featureJson($client, 'GET', rawurlencode($target));
    $continuousStart = featureReplicate($client, $source, $target, true, false, $replicationFilter);
    usleep(750000);
    $activeTasks = featureJson($client, 'GET', '_active_tasks');
    $schedulerJobs = featureJson($client, 'GET', '_scheduler/jobs');
    $continuousCancel = featureReplicate($client, $source, $target, true, true, $replicationFilter);
    usleep(300000);
    $activeTasksAfterCancel = featureJson($client, 'GET', '_active_tasks');
    $evidence += ['replication_filter' => $replicationFilter, 'validator_installed_after_replication' => $validatorInstall['ok'] ?? false, 'one_shot_replication' => $oneShot, 'counts' => ['source' => $sourceInfo['doc_count'] ?? null, 'target' => $targetInfo['doc_count'] ?? null, 'source_all_docs' => count($sourceDocs), 'target_all_docs' => count($targetDocs)], 'normalized_hash_mismatch_ids' => $mismatch, 'continuous_start' => $continuousStart, 'active_tasks_during_continuous' => $activeTasks, 'scheduler_jobs_during_continuous' => $schedulerJobs, 'continuous_cancel' => $continuousCancel, 'active_tasks_after_cancel' => $activeTasksAfterCancel];
    $ok = ($sourceInfo['doc_count'] ?? null) === ($targetInfo['doc_count'] ?? null) && $mismatch === [] && count($sourceDocs) === count($targetDocs);
} catch (Throwable $exception) {
    $evidence['error'] = $exception->getMessage();
} finally {
    if ($created) $evidence['cleanup'] = featureDeleteDb($client, $target);
    if (!$evidence['cleanup']) $ok = false;
}
$evidence['status'] = $ok ? 'PASS' : 'FAIL';
featureOutput($evidence, $ok);
