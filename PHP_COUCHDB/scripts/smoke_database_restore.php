<?php

declare(strict_types=1);

use App\Infrastructure\CouchDB\CouchDbResponse;

require __DIR__ . '/database_backup.php';

function restoreSmokeAssert(bool $condition, string $message): void
{
    if (!$condition) throw new RuntimeException($message);
}

/** @return array{0:int,1:string,2:string} */
function runRestoreSmoke(string $filename, string $target): array
{
    $process = proc_open(
        [PHP_BINARY, __DIR__ . '/database_backup.php', 'restore', $filename, $target],
        [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
        $pipes,
        dirname(__DIR__),
    );
    if (!is_resource($process)) throw new RuntimeException('Could not start restore command.');
    fclose($pipes[0]);
    $stdout = (string) stream_get_contents($pipes[1]);
    $stderr = (string) stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    return [proc_close($process), $stdout, $stderr];
}

$filename = $argv[1] ?? '';
$target = $argv[2] ?? '';
if (!is_string($filename) || !is_string($target)
    || preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]*\.json$/', $filename) !== 1) {
    failBackup('Usage: composer db:restore-smoke -- <backup.json> <existing_restore_test_database>');
}
safeDatabaseName($target);
$directory = dirname(__DIR__) . '/var/backups';
$temporaryFiles = [];
$failure = null;
try {
    $path = $directory . '/' . $filename;
    restoreSmokeAssert(is_file($path), 'Verified backup file is missing.');
    $backup = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    restoreSmokeAssert(is_array($backup), 'Backup is invalid.');
    validateRestorePayload($backup, $target);
    $client = couchClient();
    $before = backupJson($client->request('GET', rawurlencode($target)), 'Checking restore target');
    restoreSmokeAssert(($before['doc_count'] ?? null) === $backup['document_count'], 'Target count differs from backup.');

    $missing = 'restore-smoke-missing-' . bin2hex(random_bytes(6)) . '.json';
    [$code, , $error] = runRestoreSmoke($missing, $target);
    restoreSmokeAssert($code !== 0 && str_contains($error, 'not found'), 'Missing backup file was accepted.');

    $minimal = [
        'format_version' => 1,
        'source_database' => $backup['source_database'],
        'created_at' => $backup['created_at'],
        'document_count' => 1,
        'documents' => [['_id' => 'product:restore-smoke', 'type' => 'product']],
    ];
    $fixtures = [
        'invalid-json' => ['{broken json', 'invalid JSON'],
        'empty-documents' => [json_encode(array_replace($minimal, ['documents' => [], 'document_count' => 0]), JSON_THROW_ON_ERROR), 'format'],
        'duplicate-id' => [json_encode(array_replace($minimal, [
            'documents' => [$minimal['documents'][0], $minimal['documents'][0]], 'document_count' => 2,
        ]), JSON_THROW_ON_ERROR), 'duplicate'],
        'wrong-count' => [json_encode(array_replace($minimal, ['document_count' => 2]), JSON_THROW_ON_ERROR), 'document count'],
    ];
    foreach ($fixtures as $case => [$contents, $expectedError]) {
        $name = 'restore-smoke-' . $case . '-' . bin2hex(random_bytes(6)) . '.json';
        $fixturePath = $directory . '/' . $name;
        $temporaryFiles[] = $fixturePath;
        restoreSmokeAssert(file_put_contents($fixturePath, $contents, LOCK_EX) === strlen($contents),
            'Could not write restore smoke fixture.');
        [$code, $stdout, $error] = runRestoreSmoke($name, $target);
        restoreSmokeAssert($code !== 0 && !str_contains($stdout, 'Restore import: PASS')
            && str_contains($error, $expectedError),
            'Invalid backup was accepted: ' . $case . '.');
    }

    [$code, $stdout, $error] = runRestoreSmoke($filename, $target);
    restoreSmokeAssert($code !== 0 && !str_contains($stdout, 'Restore import: PASS')
        && str_contains($error, 'already exists'),
        'Existing target was overwritten.');

    $sample = ['_id' => 'product:bulk-smoke'];
    $failed = false;
    try {
        checkRestoreBulkResult(new CouchDbResponse(201,
            '[{"id":"product:bulk-smoke","error":"forbidden","reason":"test"}]'), [$sample]);
    } catch (RuntimeException $exception) {
        $failed = str_contains($exception->getMessage(), 'product:bulk-smoke')
            && str_contains($exception->getMessage(), 'forbidden');
    }
    restoreSmokeAssert($failed, 'A per-document bulk error was accepted or not identified.');
    $prepared = prepareRestoredDocument(['_id' => 'product:bulk-smoke', '_rev' => '9-old',
        '_revisions' => ['start' => 9], '_conflicts' => ['old'], 'type' => 'product']);
    restoreSmokeAssert(!array_key_exists('_rev', $prepared) && !array_key_exists('_revisions', $prepared)
        && !array_key_exists('_conflicts', $prepared)
        && ($prepared['type'] ?? null) === 'product', 'Old revision metadata was reused.');
    $after = backupJson($client->request('GET', rawurlencode($target)), 'Rechecking restore target');
    restoreSmokeAssert(($after['doc_count'] ?? null) === $before['doc_count']
        && ($after['update_seq'] ?? null) === ($before['update_seq'] ?? null),
        'Negative restore tests changed the existing target.');
    fwrite(STDOUT, 'PASS: missing/invalid/empty/duplicate/count-mismatch backup rejected, existing target protected, bulk document error caught, old revision metadata removed.' . PHP_EOL);
} catch (Throwable $exception) {
    $failure = $exception->getMessage();
} finally {
    foreach ($temporaryFiles as $path) if (is_file($path)) @unlink($path);
}
if ($failure !== null) failBackup('Restore smoke test failed: ' . $failure);
