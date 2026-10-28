<?php

declare(strict_types=1);

use App\Infrastructure\CouchDB\CouchDbClient;

require dirname(__DIR__) . '/vendor/autoload.php';

function advancedRequest(CouchDbClient $client, string $method, string $path, ?array $body = null): array
{
    $response = $client->request($method, $path, $body);
    if ($response->statusCode < 200 || $response->statusCode >= 300) throw new RuntimeException($method . ' ' . $path . ' returned HTTP ' . $response->statusCode . ': ' . $response->body);
    return $response->json();
}

function advancedField(array $doc, string $path, bool &$exists): mixed
{
    $value = $doc;
    foreach (explode('.', $path) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) { $exists = false; return null; }
        $value = $value[$part];
    }
    $exists = true;
    return $value;
}

function advancedCondition(mixed $actual, bool $exists, mixed $condition): bool
{
    $operators = is_array($condition) ? $condition : ['$eq' => $condition];
    foreach ($operators as $operator => $expected) {
        $ok = match ($operator) {
            '$eq' => $exists && $actual === $expected,
            '$ne' => !$exists || $actual !== $expected,
            '$gt' => $exists && $actual > $expected,
            '$gte' => $exists && $actual >= $expected,
            '$lt' => $exists && $actual < $expected,
            '$lte' => $exists && $actual <= $expected,
            '$exists' => $exists === (bool) $expected,
            '$in' => $exists && in_array($actual, $expected, true),
            '$nin' => !$exists || !in_array($actual, $expected, true),
            '$regex' => $exists && is_string($actual) && preg_match('~' . str_replace('~', '\\~', (string) $expected) . '~u', $actual) === 1,
            '$not' => !advancedCondition($actual, $exists, $expected),
            '$elemMatch' => $exists && is_array($actual) && array_filter($actual, static fn (mixed $item): bool => is_array($item) && advancedMatches($item, $expected)) !== [],
            default => throw new RuntimeException('Unsupported independent-count operator: ' . $operator),
        };
        if (!$ok) return false;
    }
    return true;
}

function advancedMatches(array $doc, array $selector): bool
{
    foreach ($selector as $field => $condition) {
        if ($field === '$and' || $field === '$or') {
            $outcomes = array_map(static fn (array $child): bool => advancedMatches($doc, $child), $condition);
            if ($field === '$and' && in_array(false, $outcomes, true)) return false;
            if ($field === '$or' && !in_array(true, $outcomes, true)) return false;
            continue;
        }
        if ($field === '$not') {
            if (advancedMatches($doc, $condition)) return false;
            continue;
        }
        $exists = false;
        $actual = advancedField($doc, (string) $field, $exists);
        if (!advancedCondition($actual, $exists, $condition)) return false;
    }
    return true;
}

$database = (string) (getenv('DEMO_DATABASE') ?: 'retail_order_delivery_demo');
if (preg_match('/^[a-z][a-z0-9_$()+\/-]*_demo$/', $database) !== 1) throw new RuntimeException('Advanced query verification is read-only and restricted to *_demo.');
$client = new CouchDbClient((string) getenv('COUCHDB_URL'), (string) getenv('COUCHDB_USER'), (string) getenv('COUCHDB_PASSWORD'));
$db = rawurlencode($database);
$listing = advancedRequest($client, 'GET', $db . '/_all_docs?include_docs=true&limit=1000');
$docs = [];
foreach ($listing['rows'] ?? [] as $row) if (!str_starts_with((string) ($row['id'] ?? ''), '_design/')) $docs[] = $row['doc'];
$queryFiles = glob(dirname(__DIR__) . '/database/couchdb/demo_queries/1[3-9]_*.json') ?: [];
$queryFiles = array_merge($queryFiles, glob(dirname(__DIR__) . '/database/couchdb/demo_queries/20_*.json') ?: []);
sort($queryFiles, SORT_STRING);
$metadata = [
    '13' => ['purpose' => 'Order pending hoặc confirmed của một khách cụ thể.', 'operators' => '$or', 'index' => 'customer_orders_status'],
    '14' => ['purpose' => 'Lọc nhiều trạng thái và loại cancelled.', 'operators' => '$in, $nin', 'index' => 'admin_orders'],
    '15' => ['purpose' => 'Order có một item quantity ≥ 2 và unit_price > 200000 VND.', 'operators' => '$elemMatch, $gte, $gt', 'index' => 'orders_type_scan'],
    '16' => ['purpose' => 'Order theo khoảng tổng tiền và ngày, sắp xếp theo tổng tiền.', 'operators' => '$gt, $lte, $gte, $lt, sort', 'index' => 'orders_by_total'],
    '17' => ['purpose' => 'Order có delivery_tracking; dùng $exists=false để tìm order không có tracking.', 'operators' => '$exists', 'index' => 'orders_type_scan'],
    '18' => ['purpose' => 'Tìm tên sản phẩm theo regex, giới hạn tập ứng viên bằng type=product và active=true có index.', 'operators' => '$regex', 'index' => 'active_products'],
    '19' => ['purpose' => 'Order COD không cancelled, chiếu một số trường và giới hạn trang.', 'operators' => '$not, $and, fields, limit, skip', 'index' => 'admin_orders'],
    '20' => ['purpose' => 'Tra cứu trường lồng receiver.name vì schema demo không có receiver.city.', 'operators' => 'nested field, $eq', 'index' => 'orders_by_receiver'],
];
$evidence = [];
foreach ($queryFiles as $file) {
    $code = substr(basename($file), 0, 2);
    $query = json_decode((string) file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
    $expected = [];
    foreach ($docs as $doc) if (advancedMatches($doc, $query['selector'] ?? [])) $expected[] = (string) $doc['_id'];
    $explain = advancedRequest($client, 'POST', $db . '/_explain', $query);
    $request = $query;
    $limit = (int) ($query['limit'] ?? 25);
    $bookmark = null;
    $page = 0;
    $ids = [];
    $examined = 0;
    $timeMs = 0.0;
    do {
        if ($bookmark !== null) $request['bookmark'] = $bookmark;
        $result = advancedRequest($client, 'POST', $db . '/_find', $request);
        ++$page;
        foreach ($result['docs'] ?? [] as $doc) $ids[] = (string) $doc['_id'];
        $examined += (int) ($result['execution_stats']['total_docs_examined'] ?? 0);
        $timeMs += (float) ($result['execution_stats']['execution_time_ms'] ?? 0.0);
        $next = $result['bookmark'] ?? null;
        if ($page > 1000 || ($result['docs'] ?? []) === [] || !is_string($next) || $next === '' || $next === $bookmark) break;
        $bookmark = $next;
    } while (true);
    $missing = array_values(array_diff($expected, $ids));
    $unexpected = array_values(array_diff($ids, $expected));
    $index = $explain['index']['name'] ?? 'unknown';
    $ok = $index === ($metadata[$code]['index'] ?? '') && !str_contains((string) $index, '_all_docs') && $missing === [] && $unexpected === [] && count($ids) === count(array_unique($ids));
    $evidence[$code] = ['file' => basename($file), 'purpose' => $metadata[$code]['purpose'] ?? '', 'operators' => $metadata[$code]['operators'] ?? '', 'expected_index' => $metadata[$code]['index'] ?? '', 'selected_index' => $index, 'explain' => $explain, 'request' => $query, 'results_returned' => count($ids), 'independent_filter_count' => count($expected), 'docs_examined' => $examined, 'execution_time_ms' => round($timeMs, 3), 'pages' => $page, 'ids_match' => $missing === [] && $unexpected === [] && count($ids) === count(array_unique($ids)), 'missing_ids' => $missing, 'unexpected_ids' => $unexpected, 'status' => $ok ? 'PASS' : 'FAIL'];
    if ($code === '17') {
        $absentQuery = $query;
        $absentQuery['selector']['delivery_tracking']['$exists'] = false;
        $absentQuery['limit'] = 1000;
        $absentExplain = advancedRequest($client, 'POST', $db . '/_explain', $absentQuery);
        $absentResult = advancedRequest($client, 'POST', $db . '/_find', $absentQuery);
        $absentExpected = count(array_filter($docs, static fn (array $doc): bool => advancedMatches($doc, $absentQuery['selector'])));
        $evidence[$code]['exists_false_probe'] = ['selected_index' => $absentExplain['index']['name'] ?? 'unknown', 'results_returned' => count($absentResult['docs'] ?? []), 'independent_filter_count' => $absentExpected, 'docs_examined' => $absentResult['execution_stats']['total_docs_examined'] ?? null, 'ids_match' => count($absentResult['docs'] ?? []) === $absentExpected];
        if (!$evidence[$code]['exists_false_probe']['ids_match'] || str_contains((string) $evidence[$code]['exists_false_probe']['selected_index'], '_all_docs')) $evidence[$code]['status'] = 'FAIL';
    }
}
$root = dirname(__DIR__);
$directory = $root . '/docs/phase3';
if (!is_dir($directory) && !mkdir($directory, 0770, true) && !is_dir($directory)) throw new RuntimeException('Could not create docs/phase3.');
file_put_contents($directory . '/advanced_query_facts.json', json_encode(['database' => $database, 'queries' => $evidence], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . PHP_EOL, LOCK_EX);
$summary = ['# Truy vấn Mango nâng cao', '', 'Cơ sở dữ liệu: `' . $database . '`. Bằng chứng dưới đây lấy từ `_explain`, `_find`/`execution_stats` và bộ lọc độc lập PHP trên documents hiện có. Từng payload JSON có thể dán vào Fauxton Mango Query.', '', '| Mã | Mục đích | Toán tử | Index `_explain` | Kết quả / đếm PHP | docs_examined | execution_time_ms | Trạng thái |', '|---:|---|---|---|---:|---:|---:|---|'];
foreach ($evidence as $code => $item) $summary[] = '| ' . $code . ' | ' . $item['purpose'] . ' | `' . $item['operators'] . '` | `' . $item['selected_index'] . '` | ' . $item['results_returned'] . ' / ' . $item['independent_filter_count'] . ' | ' . $item['docs_examined'] . ' | ' . $item['execution_time_ms'] . ' | ' . $item['status'] . ' |';
$summary[] = '';
foreach ($evidence as $code => $item) {
    $summary[] = '## Query ' . $code . ' — ' . $item['purpose'];
    $summary[] = '';
    $summary[] = 'Toán tử: `' . $item['operators'] . '`. Index: `' . $item['selected_index'] . '`. Kết quả: ' . $item['results_returned'] . '; đếm độc lập PHP: ' . $item['independent_filter_count'] . '; docs_examined: ' . $item['docs_examined'] . '; trạng thái: **' . $item['status'] . '**.';
    if ($code === '18') $summary[] = 'Regex không phải phép tiền tố được tối ưu bằng index; `type` và `active` giới hạn candidate trước khi CouchDB áp dụng regex.';
    if ($code === '17' && isset($item['exists_false_probe'])) $summary[] = 'Đảo `$exists` sang `false`: ' . $item['exists_false_probe']['results_returned'] . ' kết quả, PHP filter=' . $item['exists_false_probe']['independent_filter_count'] . ', index `' . $item['exists_false_probe']['selected_index'] . '`.';
    if ($code === '20') $summary[] = 'Schema demo không có `receiver.city`; truy vấn dùng nested field `receiver.name` có thật trong receiver.';
    $summary[] = '';
    $summary[] = '```json';
    $summary[] = json_encode($item['request'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    $summary[] = '```';
    $summary[] = '';
    $short = ['# Query ' . $code, '', '- Mục đích nghiệp vụ: ' . $item['purpose'], '- Toán tử: `' . $item['operators'] . '`.', '- Index theo `_explain`: `' . $item['selected_index'] . '`.', '- Kết quả kỳ vọng và kiểm chứng độc lập: ' . $item['results_returned'] . ' document(s); PHP filter=' . $item['independent_filter_count'] . '.', '- docs_examined: ' . $item['docs_examined'] . '; execution_time_ms: ' . $item['execution_time_ms'] . '.', '- Trạng thái: **' . $item['status'] . '**.', '', 'Payload đầy đủ nằm trong `ADVANCED_QUERIES.md` và JSON tương ứng dưới `database/couchdb/demo_queries/' . $item['file'] . '`.'];
    if ($code === '17' && isset($item['exists_false_probe'])) $short[] = '- Đối chứng `$exists: false`: ' . $item['exists_false_probe']['results_returned'] . ' kết quả; PHP filter=' . $item['exists_false_probe']['independent_filter_count'] . '; index `' . $item['exists_false_probe']['selected_index'] . '`.';
    file_put_contents($directory . '/' . pathinfo($item['file'], PATHINFO_FILENAME) . '.md', implode(PHP_EOL, $short) . PHP_EOL, LOCK_EX);
}
file_put_contents($directory . '/ADVANCED_QUERIES.md', implode(PHP_EOL, $summary) . PHP_EOL, LOCK_EX);
foreach ($evidence as $code => $item) fwrite(STDOUT, $code . ': ' . $item['status'] . ' index=' . $item['selected_index'] . ' results=' . $item['results_returned'] . ' independent=' . $item['independent_filter_count'] . ' docs_examined=' . $item['docs_examined'] . PHP_EOL);
if (in_array('FAIL', array_column($evidence, 'status'), true)) exit(1);
