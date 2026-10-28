<?php

declare(strict_types=1);

use App\Infrastructure\CouchDB\CouchDbClient;

require __DIR__ . '/fauxton_feature_common.php';

$client = featureClient();
$database = 'retail_csv_import_scratch';
$exportName = 'orders_phase3_20261028.csv';
$exportPath = dirname(__DIR__) . '/docs/phase3/exports/' . $exportName;
$created = false;
$evidence = ['feature' => 'csv_exchange', 'import_database' => $database, 'cleanup' => false];
$ok = false;
try {
    featureCreateDb($client, $database);
    $created = true;
    $validator = json_decode((string) file_get_contents(dirname(__DIR__) . '/database/couchdb/design-docs/domain_validation.json'), true, 512, JSON_THROW_ON_ERROR);
    featureJson($client, 'PUT', rawurlencode($database) . '/_design/domain_validation', $validator, [201]);
    $importProcess = proc_open(['composer', 'import:products-csv', '--', 'data/samples/products_import.csv'], [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $importPipes, dirname(__DIR__));
    if (!is_resource($importProcess)) throw new RuntimeException('Could not start composer import:products-csv.');
    fclose($importPipes[0]);
    $importStdout = stream_get_contents($importPipes[1]);
    $importStderr = stream_get_contents($importPipes[2]);
    fclose($importPipes[1]); fclose($importPipes[2]);
    $importExit = proc_close($importProcess);
    if ($importExit !== 0) throw new RuntimeException('composer import:products-csv failed: ' . trim((string) $importStderr) . ' ' . trim((string) $importStdout));
    $import = json_decode((string) $importStdout, true, 512, JSON_THROW_ON_ERROR);
    $all = featureJson($client, 'GET', rawurlencode($database) . '/_all_docs?include_docs=true&limit=1000');
    $importedProducts = array_values(array_filter(array_map(static fn (array $row): array => $row['doc'] ?? [], $all['rows'] ?? []), static fn (array $doc): bool => ($doc['type'] ?? null) === 'product'));
    $demo = (string) (getenv('DEMO_DATABASE') ?: 'retail_order_delivery_demo');
    $request = ['selector' => ['type' => ['$eq' => 'order']], 'use_index' => ['_design/catalog_indexes', 'admin_orders'], 'sort' => [['status' => 'asc'], ['ordered_at' => 'asc']], 'limit' => 1000, 'execution_stats' => true];
    $explain = featureJson($client, 'POST', rawurlencode($demo) . '/_explain', $request);
    $query = featureJson($client, 'POST', rawurlencode($demo) . '/_find', $request);
    $exportCreatedNow = false;
    if (!is_file($exportPath)) {
        $exportProcess = proc_open(['composer', 'export:orders-csv', '--', $exportName], [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $exportPipes, dirname(__DIR__));
        if (!is_resource($exportProcess)) throw new RuntimeException('Could not start composer export:orders-csv.');
        fclose($exportPipes[0]);
        $exportStdout = stream_get_contents($exportPipes[1]);
        $exportStderr = stream_get_contents($exportPipes[2]);
        fclose($exportPipes[1]); fclose($exportPipes[2]);
        $exportExit = proc_close($exportProcess);
        if ($exportExit !== 0) throw new RuntimeException('composer export:orders-csv failed: ' . trim((string) $exportStderr) . ' ' . trim((string) $exportStdout));
        $export = json_decode((string) $exportStdout, true, 512, JSON_THROW_ON_ERROR);
        $exportCreatedNow = true;
    } else {
        $export = ['database' => $demo, 'output' => 'docs/phase3/exports/' . $exportName, 'query_count' => count($query['docs'] ?? []), 'rows_exported' => null, 'docs_examined' => $query['execution_stats']['total_docs_examined'] ?? null];
    }
    $csv = fopen($exportPath, 'rb');
    if ($csv === false) throw new RuntimeException('Could not reopen the order export CSV.');
    try {
        fgetcsv($csv, null, ',', '"', '');
        $exportedRows = [];
        while (($row = fgetcsv($csv, null, ',', '"', '')) !== false) if ($row !== [null] && $row !== []) $exportedRows[] = $row;
    } finally { fclose($csv); }
    $queryIds = array_map(static fn (array $doc): string => (string) $doc['_id'], $query['docs'] ?? []);
    $csvIds = array_map(static fn (array $row): string => (string) ($row[0] ?? ''), $exportedRows);
    sort($queryIds, SORT_STRING); sort($csvIds, SORT_STRING);
    $evidence += ['import_command' => 'composer import:products-csv -- data/samples/products_import.csv', 'import' => $import, 'imported_products_in_db' => count($importedProducts), 'export_command' => 'composer export:orders-csv -- ' . $exportName, 'export' => $export + ['rows_exported' => count($exportedRows), 'created_now' => $exportCreatedNow], 'export_query_index' => $explain['index']['name'] ?? null, 'exported_row_count_matches_query' => count($exportedRows) === count($query['docs'] ?? []) && $queryIds === $csvIds, 'export_query_count' => count($query['docs'] ?? []), 'csv_data_rows' => count($exportedRows), 'export_docs_examined' => $query['execution_stats']['total_docs_examined'] ?? null];
    $ok = $import['imported_count'] === 3 && count($import['errors']) === 1 && count($importedProducts) === 3 && ($explain['index']['name'] ?? null) === 'admin_orders' && count($exportedRows) === count($query['docs'] ?? []) && $queryIds === $csvIds;
} catch (Throwable $exception) {
    $evidence['error'] = $exception->getMessage();
} finally {
    if ($created) $evidence['cleanup'] = featureDeleteDb($client, $database);
    if (!$evidence['cleanup']) $ok = false;
}
$evidence['status'] = $ok ? 'PASS' : 'FAIL';
$evidence['generated_at_utc'] = gmdate('Y-m-d\TH:i:s\Z');
$directory = dirname(__DIR__) . '/docs/phase3';
file_put_contents($directory . '/csv_exchange_evidence.json', json_encode($evidence, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL, LOCK_EX);
fwrite(STDOUT, json_encode($evidence, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL);
exit($ok ? 0 : 1);
