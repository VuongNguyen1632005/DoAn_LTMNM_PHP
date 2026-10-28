<?php

declare(strict_types=1);

require __DIR__ . '/fauxton_feature_common.php';

$client = featureClient();
$left = 'retail_conflict_left_scratch';
$right = 'retail_conflict_right_scratch';
$created = [];
$evidence = ['feature' => 'replication_conflict', 'databases' => [$left, $right], 'cleanup' => []];
$ok = false;
try {
    featureCreateDb($client, $left); $created[] = $left;
    featureCreateDb($client, $right); $created[] = $right;
    $id = 'conflict:shared-demo-doc';
    $base = featureJson($client, 'PUT', rawurlencode($left) . '/' . rawurlencode($id), ['_id' => $id, 'type' => 'conflict_demo', 'value' => 'base'], [201]);
    $baseReplication = featureReplicate($client, $left, $right);
    $leftDoc = featureJson($client, 'GET', rawurlencode($left) . '/' . rawurlencode($id));
    $rightDoc = featureJson($client, 'GET', rawurlencode($right) . '/' . rawurlencode($id));
    $leftDoc['value'] = 'edited-on-left';
    $rightDoc['value'] = 'edited-on-right';
    $leftWrite = featureJson($client, 'PUT', rawurlencode($left) . '/' . rawurlencode($id), $leftDoc, [200, 201]);
    $rightWrite = featureJson($client, 'PUT', rawurlencode($right) . '/' . rawurlencode($id), $rightDoc, [200, 201]);
    $conflictReplication = featureReplicate($client, $left, $right);
    $conflicted = featureJson($client, 'GET', rawurlencode($right) . '/' . rawurlencode($id) . '?conflicts=true');
    $losers = $conflicted['_conflicts'] ?? [];
    if (count($losers) !== 1) throw new RuntimeException('Expected exactly one losing revision after divergent replication; observed ' . count($losers) . '.');
    $winner = ['rev' => $conflicted['_rev'], 'value' => $conflicted['value']];
    $loser = (string) $losers[0];
    $deleteLoser = featureJson($client, 'PUT', rawurlencode($right) . '/' . rawurlencode($id), ['_id' => $id, '_rev' => $loser, '_deleted' => true], [200, 201, 202]);
    $resolved = featureJson($client, 'GET', rawurlencode($right) . '/' . rawurlencode($id) . '?conflicts=true');
    $evidence += ['base_revision' => $base['rev'], 'base_replication' => $baseReplication, 'left_edit_revision' => $leftWrite['rev'], 'right_edit_revision' => $rightWrite['rev'], 'conflict_replication' => $conflictReplication, 'conflicted_document' => ['winner' => $winner, 'losing_revisions' => $losers], 'deleted_loser' => ['revision' => $loser, 'response' => $deleteLoser], 'resolved_document' => ['rev' => $resolved['_rev'], 'value' => $resolved['value'], 'conflicts' => $resolved['_conflicts'] ?? []], 'winner_rule' => 'CouchDB selects a deterministic winner from revision-tree ordering; this script records the actual winner from GET ?conflicts=true and tombstones the other branch. The winner value is measured output, not hard-coded.'];
    $ok = !isset($resolved['_conflicts']) && $resolved['value'] === $winner['value'];
} catch (Throwable $exception) {
    $evidence['error'] = $exception->getMessage();
} finally {
    $evidence['cleanup'] = [];
    foreach (array_reverse($created) as $database) $evidence['cleanup'][$database] = featureDeleteDb($client, $database);
    if (in_array(false, $evidence['cleanup'], true)) $ok = false;
}
$evidence['status'] = $ok ? 'PASS' : 'FAIL';
featureOutput($evidence, $ok);
