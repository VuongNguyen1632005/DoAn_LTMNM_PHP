<?php

declare(strict_types=1);

use App\Infrastructure\CouchDB\CouchDbClient;

require dirname(__DIR__) . '/vendor/autoload.php';

/** @return array<string, mixed> */
function importProductsCsv(string $database, string $csvPath): array
{
    if (preg_match('/^[a-z][a-z0-9_$()+\/-]*_(test|scratch)$/', $database) !== 1) throw new RuntimeException('CSV import writes are restricted to *_test or *_scratch.');
    if (!is_file($csvPath) || is_link($csvPath)) throw new RuntimeException('CSV input file is missing or is a symbolic link.');
    $client = new CouchDbClient((string) getenv('COUCHDB_URL'), (string) getenv('COUCHDB_USER'), (string) getenv('COUCHDB_PASSWORD'));
    $validatorResponse = $client->request('GET', rawurlencode($database) . '/_design/domain_validation');
    if ($validatorResponse->statusCode !== 200 || !is_string($validatorResponse->json()['validate_doc_update'] ?? null)) throw new RuntimeException('Target database must have the full domain_validation design document installed.');
    $handle = fopen($csvPath, 'rb');
    if ($handle === false) throw new RuntimeException('Could not open CSV file.');
    try {
        $headers = fgetcsv($handle, null, ',', '"', '');
        if (!is_array($headers)) throw new RuntimeException('CSV has no header row.');
        $headers = array_map(static fn (string $value): string => trim($value), $headers);
        foreach (['sku', 'name', 'category', 'price', 'stock'] as $required) if (!in_array($required, $headers, true)) throw new RuntimeException('CSV is missing column ' . $required . '.');
        $documents = [];
        $lines = [];
        $lineNumber = 1;
        $parseErrors = [];
        while (($row = fgetcsv($handle, null, ',', '"', '')) !== false) {
            ++$lineNumber;
            if ($row === [null] || $row === []) continue;
            if (count($row) !== count($headers)) { $parseErrors[] = ['line' => $lineNumber, 'error' => 'column_count_mismatch']; continue; }
            $input = array_combine($headers, array_map(static fn (string|null $value): string => trim((string) $value), $row));
            if (!is_array($input) || $input['sku'] === '' || $input['name'] === '' || $input['category'] === '' || !is_numeric($input['price']) || filter_var($input['stock'], FILTER_VALIDATE_INT) === false) {
                $parseErrors[] = ['line' => $lineNumber, 'error' => 'required_fields_or_numeric_values_invalid'];
                continue;
            }
            $sku = strtoupper($input['sku']);
            if (preg_match('/^[A-Z0-9][A-Z0-9_-]{1,63}$/', $sku) !== 1) { $parseErrors[] = ['line' => $lineNumber, 'sku' => $sku, 'error' => 'invalid_sku']; continue; }
            $id = 'product:CSV-' . $sku;
            $documents[] = ['_id' => $id, 'type' => 'product', 'schema_version' => 2, 'legacy_id' => 'CSV-' . $sku, 'name' => $input['name'], 'description' => '', 'category' => $input['category'], 'images' => [], 'variants' => [['variant_id' => 'CSV-' . $sku . '-A', 'size' => 'M', 'price' => (float) $input['price'], 'stock' => (int) $input['stock'], 'active' => true]], 'active' => true];
            $lines[] = ['line' => $lineNumber, 'id' => $id];
        }
    } finally {
        fclose($handle);
    }
    $bulkResults = [];
    if ($documents !== []) {
        $response = $client->request('POST', rawurlencode($database) . '/_bulk_docs', ['docs' => $documents]);
        if ($response->statusCode < 200 || $response->statusCode >= 300) throw new RuntimeException('_bulk_docs returned HTTP ' . $response->statusCode . ': ' . $response->body);
        $bulkResults = $response->json();
        if (!is_array($bulkResults) || count($bulkResults) !== count($documents)) throw new RuntimeException('_bulk_docs returned an incomplete row result list.');
    }
    $successes = [];
    $errors = $parseErrors;
    foreach ($bulkResults as $index => $result) {
        if (is_array($result) && ($result['ok'] ?? false) === true) $successes[] = ['line' => $lines[$index]['line'], 'id' => $result['id'], 'rev' => $result['rev'] ?? null];
        else $errors[] = ['line' => $lines[$index]['line'], 'id' => $lines[$index]['id'], 'error' => is_array($result) ? ($result['error'] ?? 'unknown') : 'invalid_result', 'reason' => is_array($result) ? ($result['reason'] ?? '') : ''];
    }
    return ['database' => $database, 'source_file' => basename($csvPath), 'rows_submitted_to_bulk_docs' => count($documents), 'imported_count' => count($successes), 'errors' => $errors, 'successes' => $successes, 'status' => $errors === [] ? 'PASS' : 'PASS_WITH_ROW_ERRORS'];
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    try {
        $database = (string) (getenv('COUCHDB_DATABASE') ?: '');
        $csvPath = $argv[1] ?? '';
        $result = importProductsCsv($database, $csvPath);
        fwrite(STDOUT, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL);
        exit(0);
    } catch (Throwable $exception) {
        fwrite(STDERR, json_encode(['status' => 'FAIL', 'error' => $exception->getMessage()], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL);
        exit(1);
    }
}
