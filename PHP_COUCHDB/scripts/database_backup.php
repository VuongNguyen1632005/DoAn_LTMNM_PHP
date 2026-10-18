<?php
declare(strict_types=1);

use App\Infrastructure\CouchDB\CouchDbClient;
use App\Infrastructure\CouchDB\CouchDbResponse;

require dirname(__DIR__) . '/vendor/autoload.php';

const BACKUP_PAGE_SIZE = 500;

function failBackup(string $message): never
{
    fwrite(STDERR, $message . PHP_EOL);
    exit(1);
}

function couchClient(): CouchDbClient
{
    return new CouchDbClient(
        getenv('COUCHDB_URL') ?: '',
        getenv('COUCHDB_USER') ?: '',
        getenv('COUCHDB_PASSWORD') ?: '',
    );
}

function backupJson(CouchDbResponse $response, string $operation): array
{
    if ($response->statusCode < 200 || $response->statusCode >= 300) {
        throw new RuntimeException($operation . ' failed with CouchDB HTTP ' . $response->statusCode . '.');
    }
    try {
        return $response->json();
    } catch (RuntimeException $exception) {
        throw new RuntimeException($operation . ' returned invalid JSON.', previous: $exception);
    }
}

function safeDatabaseName(string $database): void
{
    if (!preg_match('/^[a-z][a-z0-9_$()+\/-]*_test$/', $database)) {
        failBackup('Backup and restore are restricted to database names ending in _test.');
    }
}

/**
 * @param callable(string): CouchDbResponse $readPage
 * @return list<array<string, mixed>>
 */
function collectBackupDocuments(callable $readPage, string $database, int $expectedCount, int $pageSize = BACKUP_PAGE_SIZE): array
{
    if ($expectedCount < 0 || $pageSize < 1) throw new RuntimeException('Invalid backup document count or page size.');
    $documents = [];
    $seen = [];
    $startKey = null;
    while (true) {
        $query = ['include_docs' => 'true', 'attachments' => 'true', 'limit' => $pageSize];
        if ($startKey !== null) {
            $query['startkey'] = json_encode($startKey, JSON_THROW_ON_ERROR);
            $query['startkey_docid'] = $startKey;
            $query['skip'] = '1';
        }
        $path = rawurlencode($database) . '/_all_docs?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        $page = backupJson($readPage($path), 'Reading backup page');
        $rows = $page['rows'] ?? null;
        if (!is_array($rows) || !array_is_list($rows) || count($rows) > $pageSize
            || !is_int($page['total_rows'] ?? null) || $page['total_rows'] !== $expectedCount) {
            throw new RuntimeException('CouchDB backup page has an invalid count or rows list.');
        }
        foreach ($rows as $row) {
            $id = is_array($row) ? ($row['id'] ?? null) : null;
            $document = is_array($row) ? ($row['doc'] ?? null) : null;
            if (!is_string($id) || $id === '' || !is_array($document)
                || ($document['_id'] ?? null) !== $id || isset($seen[$id])) {
                throw new RuntimeException('CouchDB returned a missing, duplicate or incomplete document.');
            }
            $seen[$id] = true;
            $documents[] = $document;
        }
        if (count($rows) < $pageSize) break;
        $last = $rows[array_key_last($rows)]['id'];
        if ($last === $startKey) throw new RuntimeException('CouchDB backup pagination did not advance.');
        $startKey = $last;
    }
    if (count($documents) !== $expectedCount) {
        throw new RuntimeException('Backup document count does not match CouchDB.');
    }
    return $documents;
}

function publishBackupFile(string $directory, string $name, string $contents): int
{
    if (is_link($directory)) throw new RuntimeException('var/backups must be a real directory.');
    if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) {
        throw new RuntimeException('Could not create var/backups.');
    }
    $path = $directory . '/' . $name;
    if (file_exists($path) || is_link($path)) throw new RuntimeException('Backup file already exists; refusing to overwrite it.');
    $temporary = tempnam($directory, '.backup-');
    if ($temporary === false) throw new RuntimeException('Could not create a temporary backup file.');
    try {
        @chmod($temporary, 0600);
        $written = file_put_contents($temporary, $contents, LOCK_EX);
        if ($written !== strlen($contents)) throw new RuntimeException('Could not write the complete backup file.');
        $check = file_get_contents($temporary);
        if (!is_string($check) || $check !== $contents) throw new RuntimeException('Backup file verification failed.');
        // A hard link publishes the complete file only if the destination does not exist.
        if (!@link($temporary, $path)) {
            throw new RuntimeException(file_exists($path) || is_link($path)
                ? 'Backup file already exists; refusing to overwrite it.'
                : 'Could not publish backup file without overwriting an existing file.');
        }
        return $written;
    } finally {
        @unlink($temporary);
    }
}

function backupDatabase(): never
{
    global $argv;
    $database = getenv('COUCHDB_DATABASE') ?: '';
    safeDatabaseName($database);
    $name = $argv[2] ?? ('couchdb-' . gmdate('Ymd-His') . '.json');
    if (!preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]*\.json$/', $name)) {
        failBackup('Provide a simple .json filename; directory traversal is not allowed.');
    }

    $directory = dirname(__DIR__) . '/var/backups';
    if (file_exists($directory . '/' . $name) || is_link($directory . '/' . $name)) {
        failBackup('Backup file already exists; refusing to overwrite it.');
    }
    $client = couchClient();
    $sourcePath = rawurlencode($database);
    $before = backupJson($client->request('GET', $sourcePath), 'Checking source database');
    if (!is_int($before['doc_count'] ?? null) || !isset($before['update_seq'])) {
        throw new RuntimeException('CouchDB source database info is incomplete.');
    }
    $documents = collectBackupDocuments(
        static fn (string $path): CouchDbResponse => $client->request('GET', $path),
        $database,
        $before['doc_count'],
    );
    $after = backupJson($client->request('GET', $sourcePath), 'Rechecking source database');
    if (($after['doc_count'] ?? null) !== $before['doc_count']
        || ($after['update_seq'] ?? null) !== $before['update_seq']) {
        throw new RuntimeException('Source database changed during backup; no backup file was written.');
    }
    $payload = [
        'format_version' => 1,
        'source_database' => $database,
        'created_at' => gmdate(DATE_ATOM),
        'document_count' => count($documents),
        'documents' => $documents,
    ];
    $encoded = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL;
    $bytes = publishBackupFile($directory, $name, $encoded);
    fwrite(STDOUT, 'Backup database: ' . $database . PHP_EOL);
    fwrite(STDOUT, 'Documents exported: ' . count($documents) . PHP_EOL);
    fwrite(STDOUT, 'Output: var/backups/' . $name . PHP_EOL);
    fwrite(STDOUT, 'File size: ' . $bytes . ' bytes' . PHP_EOL);
    fwrite(STDOUT, 'JSON validation: PASS' . PHP_EOL);
    fwrite(STDOUT, 'Backup: PASS' . PHP_EOL);
    exit(0);
}

/** @return array{source:string, documents:list<array<string, mixed>>} */
function validateRestorePayload(array $payload, string $target): array
{
    $source = $payload['source_database'] ?? null;
    $documents = $payload['documents'] ?? null;
    if (($payload['format_version'] ?? null) !== 1 || !is_string($source)
        || !preg_match('/^[a-z][a-z0-9_$()+\/-]*_test$/', $source)
        || $source === $target || !is_string($payload['created_at'] ?? null)
        || !is_array($documents) || !array_is_list($documents) || $documents === []
        || !is_int($payload['document_count'] ?? null)
        || $payload['document_count'] !== count($documents)) {
        throw new RuntimeException('Backup format, source database or document count is invalid.');
    }
    $seen = [];
    foreach ($documents as $document) {
        $id = is_array($document) ? ($document['_id'] ?? null) : null;
        if (!is_string($id) || $id === '' || isset($seen[$id])) {
            throw new RuntimeException('Backup contains a missing or duplicate document ID.');
        }
        if (($document['_deleted'] ?? false) === true) {
            throw new RuntimeException('Backup contains a deleted document; logical restore cannot import tombstones.');
        }
        $seen[$id] = true;
    }
    return ['source' => $source, 'documents' => $documents];
}

/** @param array<string, mixed> $document @return array<string, mixed> */
function prepareRestoredDocument(array $document): array
{
    unset($document['_rev'], $document['_revisions'], $document['_conflicts'],
        $document['_deleted_conflicts'], $document['_local_seq']);
    return $document;
}

/** @param list<array<string, mixed>> $batch */
function checkRestoreBulkResult(CouchDbResponse $response, array $batch): int
{
    $results = backupJson($response, 'Restoring document batch');
    if (!array_is_list($results) || count($results) !== count($batch)) {
        throw new RuntimeException('Restore returned an incomplete batch result.');
    }
    foreach ($results as $index => $result) {
        $id = $batch[$index]['_id'];
        $error = is_array($result) ? ($result['error'] ?? null) : null;
        if (!is_array($result) || $error !== null || ($result['id'] ?? null) !== $id
            || ($result['ok'] ?? false) !== true || !is_string($result['rev'] ?? null)
            || $result['rev'] === '') {
            $type = is_string($error) ? preg_replace('/[^A-Za-z0-9_-]/', '', $error) : 'invalid_result';
            throw new RuntimeException('Document ' . $id . ' failed (' . $type . ').');
        }
    }
    return count($batch);
}

function restoreDatabase(): never
{
    global $argv;
    $name = $argv[2] ?? '';
    $target = $argv[3] ?? '';
    if (!preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]*\.json$/', $name)) failBackup('Usage: composer db:restore -- <backup.json> <target_test_database>');
    safeDatabaseName($target);
    $path = dirname(__DIR__) . '/var/backups/' . $name;
    if (!is_file($path) || is_link($path)) failBackup('Backup file was not found in var/backups.');
    try {
        $payload = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        failBackup('Backup file contains invalid JSON.');
    }
    $validated = validateRestorePayload($payload, $target);
    $documents = $validated['documents'];

    $client = couchClient();
    $databasePath = rawurlencode($target);
    $exists = $client->request('GET', $databasePath);
    if ($exists->statusCode === 200) failBackup('Restore target already exists; refusing to overwrite any database.');
    if ($exists->statusCode !== 404) failBackup('Could not safely check restore target (HTTP ' . $exists->statusCode . ').');
    $imported = 0;
    try {
        backupJson($client->request('PUT', $databasePath), 'Creating restore target');
        $designs = [];
        $business = [];
        $validators = [];
        foreach ($documents as $document) {
            $prepared = prepareRestoredDocument($document);
            if (str_starts_with($prepared['_id'], '_design/')
                && isset($prepared['validate_doc_update'])) $validators[] = $prepared;
            elseif (str_starts_with($prepared['_id'], '_design/')) $designs[] = $prepared;
            else $business[] = $prepared;
        }
        // Existing tracked orders do not satisfy the validator's new-document transition rule.
        // Import their exact content first, then install the backed-up validator for future writes.
        foreach ([$designs, $business, $validators] as $group) {
            foreach (array_chunk($group, 40) as $batch) {
                $response = $client->request('POST', $databasePath . '/_bulk_docs', ['docs' => $batch]);
                $imported += checkRestoreBulkResult($response, $batch);
            }
        }
        $info = backupJson($client->request('GET', $databasePath), 'Verifying restored database');
        if (($info['doc_count'] ?? null) !== count($documents)) {
            throw new RuntimeException('Restored document count does not match backup.');
        }
    } catch (Throwable $exception) {
        throw new RuntimeException('Restore failed during target creation or import; target may contain partial import. '
            . $exception->getMessage(), previous: $exception);
    }
    fwrite(STDOUT, 'Restore source: var/backups/' . $name . PHP_EOL);
    fwrite(STDOUT, 'Target database: ' . $target . PHP_EOL);
    fwrite(STDOUT, 'Backup documents: ' . count($documents) . PHP_EOL);
    fwrite(STDOUT, 'Imported: ' . $imported . PHP_EOL);
    fwrite(STDOUT, 'Failed: 0' . PHP_EOL);
    fwrite(STDOUT, 'Document count parity: PASS' . PHP_EOL);
    fwrite(STDOUT, 'Restore import: PASS' . PHP_EOL);
    exit(0);
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    try {
        $action = $argv[1] ?? '';
        if ($action === 'backup') backupDatabase();
        if ($action === 'restore') restoreDatabase();
        failBackup('Usage: php scripts/database_backup.php backup [filename.json] | restore <backup.json> <target_test_database>');
    } catch (Throwable $exception) {
        failBackup(($action === 'restore' ? 'Restore' : 'Backup') . ' command failed: ' . $exception->getMessage());
    }
}
