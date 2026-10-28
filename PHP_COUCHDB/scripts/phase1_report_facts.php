<?php

declare(strict_types=1);

use App\Infrastructure\CouchDB\CouchDbClient;

require dirname(__DIR__) . '/vendor/autoload.php';

function factsRequest(CouchDbClient $client, string $method, string $path, ?array $body = null): array
{
    $response = $client->request($method, $path, $body);
    if ($response->statusCode < 200 || $response->statusCode >= 300) throw new RuntimeException($method . ' ' . $path . ' failed with HTTP ' . $response->statusCode . '.');
    return $response->json();
}

function compactSelector(array $selector): string
{
    return str_replace('|', '\\|', json_encode($selector, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
}

$database = (string) (getenv('DEMO_DATABASE') ?: 'retail_order_delivery_demo');
if (preg_match('/^[a-z][a-z0-9_$()+\/-]*_demo$/', $database) !== 1) throw new RuntimeException('DEMO_DATABASE must match *_demo.');
$client = new CouchDbClient((string) getenv('COUCHDB_URL'), (string) getenv('COUCHDB_USER'), (string) getenv('COUCHDB_PASSWORD'));
$db = rawurlencode($database);
$info = factsRequest($client, 'GET', $db);
$page = factsRequest($client, 'GET', $db . '/_all_docs?include_docs=true&limit=1000');
$rows = $page['rows'] ?? [];
if (count($rows) !== ($info['doc_count'] ?? -1)) throw new RuntimeException('Document listing is incomplete; increase report pagination before proceeding.');
$typeCounts = [];
$orderStatusCounts = [];
$deliveryStatusCounts = [];
$businessCount = 0;
foreach ($rows as $row) {
    $doc = $row['doc'] ?? [];
    if (str_starts_with((string) ($row['id'] ?? ''), '_design/')) continue;
    ++$businessCount;
    $type = (string) ($doc['type'] ?? 'unknown');
    $typeCounts[$type] = ($typeCounts[$type] ?? 0) + 1;
    if ($type === 'order') {
        $status = (string) ($doc['status'] ?? 'unknown');
        $orderStatusCounts[$status] = ($orderStatusCounts[$status] ?? 0) + 1;
        if (is_array($doc['delivery_tracking'] ?? null)) {
            $delivery = (string) ($doc['delivery_tracking']['status'] ?? 'unknown');
            $deliveryStatusCounts[$delivery] = ($deliveryStatusCounts[$delivery] ?? 0) + 1;
        }
    }
}
ksort($typeCounts); ksort($orderStatusCounts); ksort($deliveryStatusCounts);
$indexes = factsRequest($client, 'GET', $db . '/_index');
$indexCount = count(array_filter($indexes['indexes'] ?? [], static fn (array $index): bool => ($index['ddoc'] ?? null) === '_design/catalog_indexes'));
$designCount = $info['doc_count'] - $businessCount;

$queryRows = [];
$queryDetails = [];
$queryFiles = glob(dirname(__DIR__) . '/database/couchdb/demo_queries/[0-9][0-9]_*.json') ?: [];
sort($queryFiles, SORT_STRING);
foreach ($queryFiles as $queryFile) {
    $query = json_decode((string) file_get_contents($queryFile), true, 512, JSON_THROW_ON_ERROR);
    $code = substr(basename($queryFile), 0, 2);
    $label = pathinfo($queryFile, PATHINFO_FILENAME);
    $selector = compactSelector($query['selector'] ?? []);
    try {
        $explain = factsRequest($client, 'POST', $db . '/_explain', $query);
        $result = factsRequest($client, 'POST', $db . '/_find', $query);
        $stats = $result['execution_stats'] ?? [];
        $index = $explain['index'] ?? [];
        $indexLabel = trim((string) ($index['ddoc'] ?? '_all_docs') . '/' . (string) ($index['name'] ?? 'all_docs'), '/');
        $note = $result['warning'] ?? $explain['warning'] ?? '';
        $queryRows[] = ['code' => $code, 'query' => $label, 'selector' => $selector, 'index' => $indexLabel, 'results' => count($result['docs'] ?? []), 'docs_examined' => $stats['total_docs_examined'] ?? null, 'execution_time_ms' => $stats['execution_time_ms'] ?? null, 'note' => (string) $note];
        $queryDetails[$label] = ['payload' => $query, 'explain' => $explain, 'results_returned' => count($result['docs'] ?? []), 'execution_stats' => $stats, 'warning' => $note];
    } catch (Throwable $exception) {
        $queryRows[] = ['code' => $code, 'query' => $label, 'selector' => $selector, 'index' => 'ERROR', 'results' => null, 'docs_examined' => null, 'execution_time_ms' => null, 'note' => $exception->getMessage()];
        $queryDetails[$label] = ['payload' => $query, 'error' => $exception->getMessage()];
    }
}

$baselinePath = dirname(__DIR__) . '/var/phase1_delivery_baseline.json';
$baseline = is_file($baselinePath) ? json_decode((string) file_get_contents($baselinePath), true, 512, JSON_THROW_ON_ERROR) : null;
$deliveryBeforeAfter = [];
foreach (['09_delivery_in_transit.json', '10_delivery_out_for_delivery.json', '11_delivery_failed.json'] as $filename) {
    $before = $baseline['queries'][$filename] ?? null;
    $after = null;
    foreach ($queryRows as $row) if (str_starts_with($row['query'], substr($filename, 0, 2))) $after = $row;
    $deliveryBeforeAfter[] = ['query' => substr($filename, 0, 2), 'before_index' => $before['index'] ?? 'baseline unavailable', 'before_docs_examined' => $before['execution_stats']['total_docs_examined'] ?? null, 'after_index' => $after['index'] ?? null, 'after_docs_examined' => $after['docs_examined'] ?? null, 'before_results' => $before['results_returned'] ?? null, 'after_results' => $after['results'] ?? null];
}

$legacyDatabase = 'shopquan_ao_phase06_demo_test';
$legacyComparison = ['database' => $legacyDatabase, 'available' => false, 'probes' => []];
$legacyInfo = $client->request('GET', rawurlencode($legacyDatabase));
if ($legacyInfo->statusCode === 200) {
    $legacyComparison['available'] = true;
    foreach (['01_pending_orders.json', '02_shipping_orders.json', '06_orders_by_date.json'] as $filename) {
        $query = json_decode((string) file_get_contents(dirname(__DIR__) . '/database/couchdb/demo_queries/' . $filename), true, 512, JSON_THROW_ON_ERROR);
        $variants = ['query_file' => $query];
        if ($filename === '06_orders_by_date.json') {
            $limit25 = $query;
            $limit25['limit'] = 25;
            $variants['limit_25'] = $limit25;
        }
        if ($filename === '01_pending_orders.json') {
            $fallback = $query;
            unset($fallback['use_index'], $fallback['sort']);
            $variants['fallback_reproduction_without_index_or_sort'] = $fallback;
        }
        foreach ($variants as $variantName => $request) {
            $explain = factsRequest($client, 'POST', rawurlencode($legacyDatabase) . '/_explain', $request);
            $result = factsRequest($client, 'POST', rawurlencode($legacyDatabase) . '/_find', $request);
            $legacyComparison['probes'][$filename][$variantName] = ['payload' => $request, 'index' => $explain['index']['name'] ?? null, 'explain_warning' => $explain['warning'] ?? null, 'results_returned' => count($result['docs'] ?? []), 'execution_stats' => $result['execution_stats'] ?? null, 'warning' => $result['warning'] ?? null];
        }
    }
}

$commands = [
    'docker compose up --build -d',
    'Invoke-WebRequest http://127.0.0.1:8081/health/ready',
    'docker compose exec -T app composer db:seed-demo',
    'docker compose exec -T app composer seed:verify',
    'docker compose exec -T app composer phase1:report',
    'docker compose exec -T -e COUCHDB_DATABASE=retail_order_delivery_demo app composer db:backup -- phase1-demo.json',
    'docker compose exec -T -e COUCHDB_DATABASE=retail_restore_check_test app composer db:restore -- phase1-demo.json retail_restore_check_test',
    'docker compose exec -T -e COUCHDB_DATABASE=retail_restore_check_test app composer db:restore-verify -- phase1-demo.json retail_restore_check_test',
];
$facts = ['generated_at_utc' => gmdate('Y-m-d\TH:i:s\Z'), 'database' => $database, 'total_documents' => $info['doc_count'] ?? null, 'design_documents' => $designCount, 'business_documents' => $businessCount, 'catalog_mango_indexes' => $indexCount, 'documents_by_type' => $typeCounts, 'orders_by_status' => $orderStatusCounts, 'delivery_tracking_by_status' => $deliveryStatusCounts, 'queries' => $queryDetails, 'delivery_index_before_after' => $deliveryBeforeAfter, 'legacy_database_comparison' => $legacyComparison, 'query_06_root_cause' => 'In the available Phase 06 DB, the query-file payload returns 27 and changing only limit to 25 returns 25, not 21. Default limit alone therefore does not explain 21; the historical GUI payload/bookmark/database was not captured, so exact cause remains unverified.', 'query_01_root_cause' => 'The full query-file payload selects admin_orders. Removing both use_index and sort reproduces _all_docs fallback scanning all 61 rows; removing use_index but keeping the sort returns HTTP 400 (no index for sort). This identifies a fallback-producing payload difference, but the original Fauxton request was not captured, so it cannot prove which fields Fauxton omitted.', 'reproduction_commands' => $commands];
$directory = dirname(__DIR__) . '/docs/phase1';
if (!is_dir($directory) && !mkdir($directory, 0770, true) && !is_dir($directory)) throw new RuntimeException('Could not create docs/phase1.');
file_put_contents($directory . '/phase1_facts.json', json_encode($facts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . PHP_EOL, LOCK_EX);

$md = [];
$md[] = '# Phase 1 – Số liệu CouchDB';
$md[] = '';
$md[] = '- Database demo: `' . $database . '`';
$md[] = '- Tổng documents: ' . ($info['doc_count'] ?? 'N/A') . ' (business: ' . $businessCount . ', design: ' . $designCount . ')';
$md[] = '- Mango indexes trong `_design/catalog_indexes`: ' . $indexCount;
$md[] = '- Tạo lúc (UTC): ' . $facts['generated_at_utc'];
$md[] = '';
$md[] = '## Bảng 4.3 – Số lượng document theo type';
$md[] = '';
$md[] = '| Type | Số lượng |'; $md[] = '|---|---:|';
foreach ($typeCounts as $type => $count) $md[] = '| ' . $type . ' | ' . $count . ' |';
$md[] = '';
$md[] = '## Bảng 4.6 – Phân bố order theo trạng thái';
$md[] = '';
$md[] = '| Trạng thái | Số đơn |'; $md[] = '|---|---:|';
foreach ($orderStatusCounts as $status => $count) $md[] = '| ' . $status . ' | ' . $count . ' |';
$md[] = '';
$md[] = '## Bảng 4.7 – Phân bố delivery tracking';
$md[] = '';
$md[] = '| Delivery status | Số đơn |'; $md[] = '|---|---:|';
foreach ($deliveryStatusCounts as $status => $count) $md[] = '| ' . $status . ' | ' . $count . ' |';
$md[] = '';
$md[] = '## Bảng 4.8 – Kết quả 12 truy vấn Mango';
$md[] = '';
$md[] = '| Mã | Selector tóm tắt | Index theo `_explain` | Kết quả API | docs_examined | execution_time_ms | Ghi chú |';
$md[] = '|---:|---|---|---:|---:|---:|---|';
foreach ($queryRows as $row) $md[] = '| ' . $row['code'] . ' | `' . $row['selector'] . '` | `' . $row['index'] . '` | ' . ($row['results'] ?? 'N/A') . ' | ' . ($row['docs_examined'] ?? 'N/A') . ' | ' . ($row['execution_time_ms'] ?? 'N/A') . ' | ' . str_replace('|', '\\|', $row['note']) . ' |';
$md[] = '';
$md[] = '## Bảng 3.3 / 3.5 / 3.6 – Đối chiếu delivery index';
$md[] = '';
$md[] = '| Query | Index trước | docs_examined trước | Index sau | docs_examined sau | Kết quả trước/sau |';
$md[] = '|---:|---|---:|---|---:|---|';
foreach ($deliveryBeforeAfter as $row) $md[] = '| ' . $row['query'] . ' | `' . $row['before_index'] . '` | ' . ($row['before_docs_examined'] ?? 'N/A') . ' | `' . ($row['after_index'] ?? 'N/A') . '` | ' . ($row['after_docs_examined'] ?? 'N/A') . ' | ' . ($row['before_results'] ?? 'N/A') . ' / ' . ($row['after_results'] ?? 'N/A') . ' |';
$md[] = '';
$md[] = '## Tái lập';
$md[] = '';
$md[] = 'Chạy chính xác các lệnh sau từ thư mục repo trong PowerShell:';
$md[] = '';
foreach ($commands as $command) $md[] = '- `' . $command . '`';
$md[] = '';
$md[] = 'Payload query và toàn bộ response `_explain`/`_find` được lưu theo từng truy vấn trong `phase1_facts.json`.';
$md[] = '';
$md[] = '## Nguyên nhân gốc các sai lệch cũ';
$md[] = '';
$md[] = '- Query 06 (27 so với 21): **chưa xác định** nếu thiếu payload Fauxton cũ và tên database mà Fauxton dùng. File hiện tại đặt `limit: 50`, nên mặc định CouchDB 25 không giải thích được con số 21.';
$md[] = '- Query 01 (fallback): **chưa xác định** nếu thiếu response `_explain` từ đúng payload Fauxton. Báo cáo có `_explain` API của database mới nhưng không xem đó là bằng chứng về request cũ.';
$md[] = '- Probe read-only trên `shopquan_ao_phase06_demo_test`: query 06 file trả 27; cùng selector/payload nhưng chỉ đặt `limit: 25` trả 25. Query 01 đầy đủ trả 5 bằng `admin_orders`; bỏ `use_index` và `sort` tái hiện `_all_docs`, 61 docs examined. Bỏ riêng `use_index` nhưng giữ `sort` trả HTTP 400 vì không có index cho sort. Xem payload/response đầy đủ trong `legacy_database_comparison` của JSON.';
file_put_contents($directory . '/PHASE1_FACTS.md', implode(PHP_EOL, $md) . PHP_EOL, LOCK_EX);
fwrite(STDOUT, 'Wrote docs/phase1/PHASE1_FACTS.md and docs/phase1/phase1_facts.json for ' . $database . PHP_EOL);
