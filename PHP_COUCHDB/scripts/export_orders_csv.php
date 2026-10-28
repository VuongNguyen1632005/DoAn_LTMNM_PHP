<?php

declare(strict_types=1);

use App\Infrastructure\CouchDB\CouchDbClient;

require dirname(__DIR__) . '/vendor/autoload.php';

/** @return array{database:string, output:string, query_count:int, rows_exported:int, docs_examined:int|null} */
function exportOrdersCsv(string $database, string $filename): array
{
    if (preg_match('/^[a-z][a-z0-9_$()+\/-]*_demo$/', $database) !== 1) throw new RuntimeException('Order CSV export is read-only and restricted to *_demo.');
    if (preg_match('/^orders_[A-Za-z0-9_-]+\.csv$/', $filename) !== 1) throw new RuntimeException('Provide a simple orders_*.csv filename.');
    $client = new CouchDbClient((string) getenv('COUCHDB_URL'), (string) getenv('COUCHDB_USER'), (string) getenv('COUCHDB_PASSWORD'));
    $request = ['selector' => ['type' => ['$eq' => 'order']], 'use_index' => ['_design/catalog_indexes', 'admin_orders'], 'sort' => [['status' => 'asc'], ['ordered_at' => 'asc']], 'limit' => 1000, 'execution_stats' => true];
    $dbPath = rawurlencode($database);
    $explainResponse = $client->request('POST', $dbPath . '/_explain', $request);
    if ($explainResponse->statusCode !== 200 || ($explainResponse->json()['index']['name'] ?? null) !== 'admin_orders') throw new RuntimeException('Order export count query did not use admin_orders.');
    $queryResponse = $client->request('POST', $dbPath . '/_find', $request);
    if ($queryResponse->statusCode !== 200) throw new RuntimeException('Order export query returned HTTP ' . $queryResponse->statusCode . '.');
    $result = $queryResponse->json();
    $orders = $result['docs'] ?? [];
    $directory = dirname(__DIR__) . '/docs/phase3/exports';
    if (!is_dir($directory) && !mkdir($directory, 0770, true) && !is_dir($directory)) throw new RuntimeException('Could not create docs/phase3/exports.');
    $path = $directory . '/' . $filename;
    $handle = @fopen($path, 'xb');
    if ($handle === false) throw new RuntimeException('Output CSV already exists or cannot be created; refusing to overwrite.');
    try {
        if (fputcsv($handle, ['id', 'date', 'customer', 'status', 'total', 'payment', 'delivery_status'], ',', '"', '') === false) throw new RuntimeException('Could not write CSV header.');
        foreach ($orders as $order) {
            $customer = $order['customer']['customer_id'] ?? 'guest';
            $row = [$order['_id'], $order['ordered_at'] ?? '', $customer, $order['status'] ?? '', $order['totals']['grand_total'] ?? '', $order['payment']['method'] ?? '', $order['delivery_tracking']['status'] ?? ''];
            if (fputcsv($handle, $row, ',', '"', '') === false) throw new RuntimeException('Could not write CSV row.');
        }
        fflush($handle);
    } catch (Throwable $exception) {
        fclose($handle);
        @unlink($path);
        throw $exception;
    }
    fclose($handle);
    return ['database' => $database, 'output' => 'docs/phase3/exports/' . $filename, 'query_count' => count($orders), 'rows_exported' => count($orders), 'docs_examined' => $result['execution_stats']['total_docs_examined'] ?? null];
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    try {
        $database = (string) (getenv('DEMO_DATABASE') ?: 'retail_order_delivery_demo');
        $filename = $argv[1] ?? '';
        $result = exportOrdersCsv($database, $filename);
        fwrite(STDOUT, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL);
        exit(0);
    } catch (Throwable $exception) {
        fwrite(STDERR, json_encode(['status' => 'FAIL', 'error' => $exception->getMessage()], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL);
        exit(1);
    }
}
