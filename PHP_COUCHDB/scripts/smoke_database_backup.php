<?php

declare(strict_types=1);

use App\Infrastructure\CouchDB\CouchDbResponse;

require __DIR__ . '/database_backup.php';

function assertBackup(bool $condition, string $message): void
{
    if (!$condition) throw new RuntimeException($message);
}

/** @param array<string, string> $overrides @return array{0:int,1:string,2:string} */
function runBackup(array $arguments, array $overrides = []): array
{
    $environment = getenv();
    if (!is_array($environment)) throw new RuntimeException('Could not read process environment.');
    $environment = array_merge($environment, $overrides);
    $process = proc_open(
        [PHP_BINARY, __DIR__ . '/database_backup.php', 'backup', ...$arguments],
        [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
        $pipes,
        dirname(__DIR__),
        $environment,
    );
    if (!is_resource($process)) throw new RuntimeException('Could not start backup command.');
    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    return [proc_close($process), (string) $stdout, (string) $stderr];
}

function checkPagination(int $count): void
{
    $ids = [];
    for ($i = 0; $i < $count; $i++) $ids[] = 'product:test-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT);
    $requests = 0;
    $read = static function (string $path) use ($ids, $count, &$requests): CouchDbResponse {
        $requests++;
        parse_str((string) parse_url($path, PHP_URL_QUERY), $query);
        $start = 0;
        if (isset($query['startkey'])) {
            $key = json_decode((string) $query['startkey'], true, 512, JSON_THROW_ON_ERROR);
            $index = array_search($key, $ids, true);
            if ($index === false || ($query['startkey_docid'] ?? null) !== $key || ($query['skip'] ?? null) !== '1') {
                throw new RuntimeException('Pagination cursor is invalid.');
            }
            $start = $index + 1;
        }
        $limit = (int) ($query['limit'] ?? 0);
        $pageIds = array_slice($ids, $start, $limit);
        $rows = array_map(static fn (string $id): array => ['id' => $id, 'doc' => ['_id' => $id, 'type' => 'product']], $pageIds);
        return new CouchDbResponse(200, json_encode(['total_rows' => $count, 'rows' => $rows], JSON_THROW_ON_ERROR));
    };
    $documents = collectBackupDocuments($read, 'smoke_backup_test', $count);
    assertBackup(count($documents) === $count, 'Pagination lost documents.');
    assertBackup(array_column($documents, '_id') === $ids, 'Pagination duplicated or reordered documents.');
    assertBackup($requests === intdiv($count, BACKUP_PAGE_SIZE) + 1, 'Pagination made the wrong number of requests.');
}

$database = (string) getenv('COUCHDB_DATABASE');
safeDatabaseName($database);
$directory = dirname(__DIR__) . '/var/backups';
$filename = 'backup-smoke-' . bin2hex(random_bytes(8)) . '.json';
$path = $directory . '/' . $filename;
$failure = null;
try {
    assertBackup(!file_exists($path), 'Smoke backup filename already exists.');
    checkPagination(501);
    checkPagination(1000);
    checkPagination(1001);

    $client = couchClient();
    $before = backupJson($client->request('GET', rawurlencode($database)), 'Reading source database');
    assertBackup(is_int($before['doc_count'] ?? null), 'Database count is unavailable.');

    [$code, $output] = runBackup([$filename]);
    assertBackup($code === 0 && str_contains($output, 'Backup: PASS'), 'Valid database backup failed.');
    assertBackup(is_file($path) && filesize($path) > 0, 'Backup file is empty or missing.');
    $raw = file_get_contents($path);
    assertBackup(is_string($raw), 'Could not read backup file.');
    $payload = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    assertBackup(($payload['format_version'] ?? null) === 1 && ($payload['source_database'] ?? null) === $database,
        'Backup contract or source database is wrong.');
    $documents = $payload['documents'] ?? null;
    assertBackup(is_array($documents) && array_is_list($documents), 'Backup documents list is invalid.');
    assertBackup(count($documents) === $payload['document_count'] && count($documents) === $before['doc_count'],
        'Backup document count does not match CouchDB.');
    $ids = array_column($documents, '_id');
    assertBackup(count(array_unique($ids)) === count($ids), 'Backup has duplicate documents.');
    $types = array_values(array_unique(array_filter(array_column($documents, 'type'), 'is_string')));
    assertBackup(in_array('product', $types, true) && in_array('order', $types, true), 'Important business types are missing.');
    $design = $client->request('GET', rawurlencode($database) . '/_design/domain_validation');
    if ($design->statusCode === 200) {
        assertBackup(in_array('_design/domain_validation', $ids, true), 'Design document is missing from backup.');
    }

    [$code] = runBackup([$filename]);
    assertBackup($code !== 0 && file_get_contents($path) === $raw, 'Existing backup was overwritten.');
    [$code] = runBackup(['../invalid.json']);
    assertBackup($code !== 0, 'Invalid filename was accepted.');
    $missingName = 'missing-backup-' . bin2hex(random_bytes(5)) . '.json';
    [$code] = runBackup([$missingName], ['COUCHDB_DATABASE' => 'missing_backup_' . bin2hex(random_bytes(5)) . '_test']);
    assertBackup($code !== 0 && !file_exists($directory . '/' . $missingName), 'Missing database was accepted.');
    $wrongCredentialName = 'wrong-credentials-' . bin2hex(random_bytes(5)) . '.json';
    [$code] = runBackup([$wrongCredentialName],
        ['COUCHDB_PASSWORD' => 'intentionally-incorrect']);
    assertBackup($code !== 0 && !file_exists($directory . '/' . $wrongCredentialName), 'Invalid credentials were accepted.');
    $unavailableName = 'unavailable-' . bin2hex(random_bytes(5)) . '.json';
    [$code] = runBackup([$unavailableName], ['COUCHDB_URL' => 'http://127.0.0.1:1']);
    assertBackup($code !== 0 && !file_exists($directory . '/' . $unavailableName), 'Unavailable CouchDB was accepted.');
    $badPageRejected = false;
    try {
        collectBackupDocuments(
            static fn (string $path): CouchDbResponse => new CouchDbResponse(200, '{"total_rows":1,"rows":[{"id":"product:bad"}]}'),
            'smoke_backup_test', 1,
        );
    } catch (RuntimeException) {
        $badPageRejected = true;
    }
    assertBackup($badPageRejected, 'An incomplete CouchDB page was accepted.');
    foreach ([
        new CouchDbResponse(500, '{"error":"server_error"}'),
        new CouchDbResponse(200, '{invalid json'),
    ] as $badResponse) {
        $pageCalls = 0;
        $failed = false;
        try {
            collectBackupDocuments(
                static function (string $path) use (&$pageCalls, $badResponse): CouchDbResponse {
                    $pageCalls++;
                    if ($pageCalls > 1) return $badResponse;
                    $rows = [];
                    for ($i = 0; $i < BACKUP_PAGE_SIZE; $i++) {
                        $id = 'product:page-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT);
                        $rows[] = ['id' => $id, 'doc' => ['_id' => $id]];
                    }
                    return new CouchDbResponse(200, json_encode(['total_rows' => BACKUP_PAGE_SIZE + 1, 'rows' => $rows], JSON_THROW_ON_ERROR));
                },
                'smoke_backup_test', BACKUP_PAGE_SIZE + 1,
            );
        } catch (RuntimeException) {
            $failed = true;
        }
        assertBackup($failed && $pageCalls === 2, 'A failed later CouchDB page was accepted.');
    }

    $after = backupJson($client->request('GET', rawurlencode($database)), 'Rechecking source database');
    assertBackup(($after['doc_count'] ?? null) === $before['doc_count']
        && ($after['update_seq'] ?? null) === ($before['update_seq'] ?? null), 'Source database changed during smoke test.');
    fwrite(STDOUT, 'PASS: backup success, missing/unavailable database, invalid filename, existing-file protection, JSON/count/design parity, malformed and HTTP-failed later pages, wrong credentials and 501/1000/1001-document pagination.' . PHP_EOL);
} catch (Throwable $exception) {
    $failure = $exception->getMessage();
} finally {
    if (is_file($path)) @unlink($path);
}
if ($failure !== null) {
    fwrite(STDERR, 'FAIL: backup smoke test: ' . $failure . PHP_EOL);
    exit(1);
}
