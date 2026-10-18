<?php

declare(strict_types=1);

use App\Infrastructure\CouchDB\CouchDbClient;

require __DIR__ . '/database_backup.php';

try {
    $name = $argv[1] ?? '';
    if (!preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]*\.json$/', $name)) {
        throw new RuntimeException('Provide a simple backup .json filename.');
    }
    $database = (string) getenv('COUCHDB_DATABASE');
    safeDatabaseName($database);
    $path = dirname(__DIR__) . '/var/backups/' . $name;
    if (!is_file($path) || is_link($path) || filesize($path) < 1) {
        throw new RuntimeException('Backup file is missing or empty.');
    }
    $contents = file_get_contents($path);
    if (!is_string($contents)) throw new RuntimeException('Could not read backup file.');
    $backup = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
    $documents = $backup['documents'] ?? null;
    if (($backup['format_version'] ?? null) !== 1 || ($backup['source_database'] ?? null) !== $database
        || !is_string($backup['created_at'] ?? null)
        || !is_array($documents) || !array_is_list($documents)
        || !is_int($backup['document_count'] ?? null)
        || count($documents) !== $backup['document_count']) {
        throw new RuntimeException('Backup format, source database or document count is invalid.');
    }
    $ids = [];
    $types = [];
    foreach ($documents as $document) {
        $id = is_array($document) ? ($document['_id'] ?? null) : null;
        if (!is_string($id) || $id === '' || isset($ids[$id])) {
            throw new RuntimeException('Backup contains a missing or duplicate document ID.');
        }
        $ids[$id] = true;
        if (is_string($document['type'] ?? null)) $types[$document['type']] = true;
    }
    $client = new CouchDbClient(
        getenv('COUCHDB_URL') ?: '',
        getenv('COUCHDB_USER') ?: '',
        getenv('COUCHDB_PASSWORD') ?: '',
    );
    $databasePath = rawurlencode($database);
    $info = backupJson($client->request('GET', $databasePath), 'Checking source database');
    if (!is_int($info['doc_count'] ?? null) || $info['doc_count'] !== count($documents)) {
        throw new RuntimeException('Document count differs from the current CouchDB database.');
    }
    $design = $client->request('GET', $databasePath . '/_design/domain_validation');
    if ($design->statusCode === 200 && !isset($ids['_design/domain_validation'])) {
        throw new RuntimeException('Source design document is missing from backup.');
    }
    if (!in_array($design->statusCode, [200, 404], true)) {
        throw new RuntimeException('Could not check source design document (HTTP ' . $design->statusCode . ').');
    }
    ksort($types, SORT_STRING);
    fwrite(STDOUT, 'Source database: ' . $database . PHP_EOL);
    fwrite(STDOUT, 'Backup file: var/backups/' . $name . PHP_EOL);
    fwrite(STDOUT, 'File size: ' . filesize($path) . ' bytes' . PHP_EOL);
    fwrite(STDOUT, 'Documents in CouchDB: ' . $info['doc_count'] . PHP_EOL);
    fwrite(STDOUT, 'Documents in backup: ' . count($documents) . PHP_EOL);
    fwrite(STDOUT, 'Business types: ' . implode(', ', array_keys($types)) . PHP_EOL);
    fwrite(STDOUT, 'Design document included: ' . (isset($ids['_design/domain_validation']) ? 'YES' : 'NO') . PHP_EOL);
    fwrite(STDOUT, 'JSON validation: PASS' . PHP_EOL);
    fwrite(STDOUT, 'Document count parity: PASS' . PHP_EOL);
    fwrite(STDOUT, 'Backup verification: PASS' . PHP_EOL);
} catch (Throwable $exception) {
    failBackup('Backup verification failed: ' . $exception->getMessage());
}
