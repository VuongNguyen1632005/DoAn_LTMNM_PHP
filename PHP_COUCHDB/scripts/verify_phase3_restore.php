<?php

declare(strict_types=1);

require __DIR__ . '/fauxton_feature_common.php';

function runRestoreCommand(array $command): array
{
    $process = proc_open($command, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, dirname(__DIR__));
    if (!is_resource($process)) throw new RuntimeException('Could not start restore verification subprocess.');
    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]); fclose($pipes[2]);
    return ['exit_code' => proc_close($process), 'stdout' => trim((string) $stdout), 'stderr' => trim((string) $stderr)];
}

$client = featureClient();
$target = 'retail_phase3_restore_test';
$backup = 'phase3-evidence-20261028.json';
$created = false;
$evidence = ['feature' => 'json_backup_restore', 'backup' => 'var/backups/' . $backup, 'target' => $target, 'cleanup' => false];
$ok = false;
try {
    if (is_file(dirname(__DIR__) . '/var/backups/' . $backup) === false) throw new RuntimeException('Expected E4 backup file is missing.');
    $exists = $client->request('GET', rawurlencode($target));
    if ($exists->statusCode !== 404) throw new RuntimeException('Restore test target already exists or cannot be inspected.');
    $restore = runRestoreCommand([PHP_BINARY, __DIR__ . '/database_backup.php', 'restore', $backup, $target]);
    $created = $client->request('GET', rawurlencode($target))->statusCode === 200;
    if ($restore['exit_code'] !== 0) throw new RuntimeException('Restore command failed: ' . $restore['stderr'] . ' ' . $restore['stdout']);
    $verify = runRestoreCommand([PHP_BINARY, __DIR__ . '/verify_database_restore.php', $backup, $target]);
    $evidence += ['restore_command' => $restore, 'restore_verification' => $verify];
    $ok = $verify['exit_code'] === 0;
} catch (Throwable $exception) {
    $evidence['error'] = $exception->getMessage();
} finally {
    if ($created) $evidence['cleanup'] = featureDeleteDb($client, $target);
    else $evidence['cleanup'] = true;
    if (!$evidence['cleanup']) $ok = false;
}
$evidence['status'] = $ok ? 'PASS' : 'FAIL';
$evidence['generated_at_utc'] = gmdate('Y-m-d\TH:i:s\Z');
$directory = dirname(__DIR__) . '/docs/phase3';
file_put_contents($directory . '/backup_restore_evidence.json', json_encode($evidence, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL, LOCK_EX);
fwrite(STDOUT, json_encode($evidence, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL);
exit($ok ? 0 : 1);
