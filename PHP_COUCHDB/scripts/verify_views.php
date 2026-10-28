<?php

declare(strict_types=1);

use App\Infrastructure\CouchDB\CouchDbClient;

require dirname(__DIR__) . '/vendor/autoload.php';

function viewRequest(CouchDbClient $client, string $database, string $view, array $params = []): array
{
    $query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    $path = rawurlencode($database) . '/_design/order_statistics/_view/' . rawurlencode($view) . ($query === '' ? '' : '?' . $query);
    $response = $client->request('GET', $path);
    if ($response->statusCode < 200 || $response->statusCode >= 300) throw new RuntimeException('GET ' . $path . ' returned HTTP ' . $response->statusCode . ': ' . $response->body);
    return $response->json();
}

function viewEqual(mixed $actual, mixed $expected): bool
{
    if (is_int($actual) || is_float($actual)) return (is_int($expected) || is_float($expected)) && abs((float) $actual - (float) $expected) < 0.00001;
    if (is_array($actual) && is_array($expected)) {
        if (array_is_list($actual) !== array_is_list($expected) || count($actual) !== count($expected)) return false;
        foreach ($expected as $key => $value) if (!array_key_exists($key, $actual) || !viewEqual($actual[$key], $value)) return false;
        return true;
    }
    return $actual === $expected;
}

function viewCheck(string $label, mixed $actual, mixed $expected, array &$evidence): void
{
    $pass = viewEqual($actual, $expected);
    $evidence[$label] = ['status' => $pass ? 'PASS' : 'FAIL', 'expected' => $expected, 'actual' => $actual];
    fwrite(STDOUT, $label . ': ' . ($pass ? 'PASS' : 'FAIL') . PHP_EOL);
}

$database = (string) (getenv('DEMO_DATABASE') ?: 'retail_order_delivery_demo');
if (preg_match('/^[a-z][a-z0-9_$()+\/-]*_demo$/', $database) !== 1) throw new RuntimeException('View verification is read-only and restricted to *_demo.');
$client = new CouchDbClient((string) getenv('COUCHDB_URL'), (string) getenv('COUCHDB_USER'), (string) getenv('COUCHDB_PASSWORD'));
$listingResponse = $client->request('GET', rawurlencode($database) . '/_all_docs?include_docs=true&limit=1000');
if ($listingResponse->statusCode !== 200) throw new RuntimeException('Could not list demo documents.');
$orders = [];
foreach ($listingResponse->json()['rows'] ?? [] as $row) if (($row['doc']['type'] ?? null) === 'order') $orders[] = $row['doc'];
$evidence = [];

$statusCounts = $statusTotals = [];
$monthlyValues = $deliveredMonthly = $topProducts = [];
$customers = [];
foreach ($orders as $order) {
    $status = (string) $order['status'];
    $total = (float) $order['totals']['grand_total'];
    $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
    $statusTotals[$status] = ($statusTotals[$status] ?? 0) + $total;
    $year = (int) substr((string) $order['ordered_at'], 0, 4);
    $month = (int) substr((string) $order['ordered_at'], 5, 2);
    $monthKey = $year . '-' . $month;
    $monthlyValues[$monthKey][] = $total;
    if ($status === 'delivered') $deliveredMonthly[$monthKey] = ($deliveredMonthly[$monthKey] ?? 0) + $total;
    foreach ($order['items'] as $item) $topProducts[$item['product_id']] = ($topProducts[$item['product_id']] ?? 0) + (int) $item['quantity'];
    if (is_array($order['customer'] ?? null) && is_string($order['customer']['customer_id'] ?? null)) $customers[] = $order;
}
ksort($statusCounts); ksort($statusTotals); ksort($monthlyValues); ksort($deliveredMonthly); ksort($topProducts);
$expectedCountRows = [];
$expectedTotalRows = [];
foreach ($statusCounts as $key => $count) $expectedCountRows[$key] = $count;
foreach ($statusTotals as $key => $sum) $expectedTotalRows[$key] = $sum;
$actual = viewRequest($client, $database, 'order_count_by_status', ['group' => 'true']);
$actualMap = [];
foreach ($actual['rows'] ?? [] as $row) $actualMap[(string) $row['key']] = $row['value'];
ksort($actualMap);
viewCheck('order_count_by_status group=true', $actualMap, $expectedCountRows, $evidence);
$actual = viewRequest($client, $database, 'order_total_by_status', ['group' => 'true']);
$actualMap = [];
foreach ($actual['rows'] ?? [] as $row) $actualMap[(string) $row['key']] = $row['value'];
ksort($actualMap);
viewCheck('order_total_by_status group=true', $actualMap, $expectedTotalRows, $evidence);
$actual = viewRequest($client, $database, 'orders_by_status', ['group' => 'true']);
$actualMap = [];
foreach ($actual['rows'] ?? [] as $row) $actualMap[(string) $row['key']] = $row['value'];
ksort($actualMap);
viewCheck('orders_by_status compatibility group=true', $actualMap, $expectedTotalRows, $evidence);

$expectedMonthlyStats = [];
foreach ($monthlyValues as $key => $values) {
    [$year, $month] = array_map('intval', explode('-', $key));
    $sum = array_sum($values);
    $expectedMonthlyStats[json_encode([$year, $month])] = ['count' => count($values), 'sum' => $sum, 'min' => min($values), 'max' => max($values), 'sumsqr' => array_sum(array_map(static fn (float $v): float => $v * $v, $values))];
}
foreach ([1, 2] as $level) {
    $actual = viewRequest($client, $database, 'orders_by_month', ['group' => 'true', 'group_level' => (string) $level]);
    $actualMap = [];
    foreach ($actual['rows'] ?? [] as $row) {
        $key = is_array($row['key']) ? array_slice($row['key'], 0, $level) : $row['key'];
        $actualMap[json_encode($key)] = $row['value'];
    }
    $expected = [];
    if ($level === 2) $expected = $expectedMonthlyStats;
    else {
        $byYear = [];
        foreach ($monthlyValues as $key => $values) {
            [$year] = array_map('intval', explode('-', $key));
            $byYear[$year] = array_merge($byYear[$year] ?? [], $values);
        }
        foreach ($byYear as $year => $values) {
            $expected[json_encode([(int) $year])] = ['count' => count($values), 'sum' => array_sum($values), 'min' => min($values), 'max' => max($values), 'sumsqr' => array_sum(array_map(static fn (float $v): float => $v * $v, $values))];
        }
    }
    viewCheck('orders_by_month group_level=' . $level, $actualMap, $expected, $evidence);
}
$actual = viewRequest($client, $database, 'top_products', ['group' => 'true']);
$actualMap = [];
foreach ($actual['rows'] ?? [] as $row) $actualMap[(string) $row['key']] = $row['value'];
ksort($actualMap);
viewCheck('top_products group=true', $actualMap, $topProducts, $evidence);
$actual = viewRequest($client, $database, 'revenue_delivered_by_month', ['group' => 'true', 'group_level' => '2']);
$actualMap = [];
foreach ($actual['rows'] ?? [] as $row) $actualMap[json_encode($row['key'])] = $row['value'];
$expected = [];
foreach ($deliveredMonthly as $key => $sum) { [$year, $month] = array_map('intval', explode('-', $key)); $expected[json_encode([$year, $month])] = $sum; }
viewCheck('revenue_delivered_by_month group_level=2', $actualMap, $expected, $evidence);
$actual = viewRequest($client, $database, 'orders_by_customer', ['reduce' => 'false', 'include_docs' => 'true', 'startkey' => json_encode('DEMO-C001'), 'endkey' => json_encode('DEMO-C015')]);
$expectedIds = array_map(static fn (array $order): string => (string) $order['_id'], $customers);
$actualIds = [];
foreach ($actual['rows'] ?? [] as $row) $actualIds[] = (string) ($row['doc']['_id'] ?? $row['id'] ?? '');
sort($expectedIds); sort($actualIds);
viewCheck('orders_by_customer reduce=false include_docs startkey/endkey', $actualIds, $expectedIds, $evidence);

$facts = ['database' => $database, 'orders_count' => count($orders), 'orders_by_status' => $statusCounts, 'order_total_by_status' => $statusTotals, 'order_count_by_status' => $statusCounts, 'monthly_order_count' => array_map('count', $monthlyValues), 'monthly_revenue_stats' => $expectedMonthlyStats, 'top_products_quantity' => $topProducts, 'delivered_revenue_by_month' => $deliveredMonthly, 'customer_lookup_orders' => count($customers), 'checks' => $evidence];
$docsDir = dirname(__DIR__) . '/docs/phase3';
if (!is_dir($docsDir) && !mkdir($docsDir, 0770, true) && !is_dir($docsDir)) throw new RuntimeException('Could not create docs/phase3.');
file_put_contents($docsDir . '/views_facts.json', json_encode($facts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . PHP_EOL, LOCK_EX);
$viewFunctions = json_decode((string) file_get_contents(dirname(__DIR__) . '/database/couchdb/design-docs/order_statistics.json'), true, 512, JSON_THROW_ON_ERROR)['views'];
$lines = ['# MapReduce Views', '', 'Cơ sở dữ liệu: `' . $database . '`. Các số liệu được đối chiếu với lọc và tổng hợp độc lập bằng PHP trên order trong DB demo.', '', '| View | Map | Reduce | Runtime kiểm tra |', '|---|---|---|---|'];
foreach ($viewFunctions as $name => $definition) {
    $checkLabels = array_filter(array_keys($evidence), static fn (string $label): bool => str_contains($label, $name));
    $states = array_map(static fn (string $label): string => $evidence[$label]['status'], $checkLabels);
    $lines[] = '| `' . $name . '` | `' . str_replace('|', '\\|', $definition['map']) . '` | `' . ($definition['reduce'] ?? 'không có') . '` | ' . ($states === [] ? 'được kiểm tra qua view tương ứng' : implode(', ', $states)) . ' |';
}
$lines[] = '';
$lines[] = '## URL dùng trong Fauxton';
$lines[] = '';
$lines[] = 'Trong Fauxton mở database → **Design Documents** → `_design/order_statistics` → chọn view → chỉnh Query Options như dưới và bấm **Run**.';
$lines[] = '';
foreach ([
    ['View', 'URL/Query Options', 'Kết quả kỳ vọng từ runtime'],
    ['order_count_by_status', '`/_design/order_statistics/_view/order_count_by_status?group=true`', json_encode($statusCounts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)],
    ['order_total_by_status', '`/_design/order_statistics/_view/order_total_by_status?group=true`', json_encode($statusTotals, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)],
    ['orders_by_month (năm)', '`/_design/order_statistics/_view/orders_by_month?group=true&group_level=1`', 'Một hàng theo năm với `_stats` count/sum/min/max/sumsqr'],
    ['orders_by_month (tháng)', '`/_design/order_statistics/_view/orders_by_month?group=true&group_level=2`', json_encode($expectedMonthlyStats, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)],
    ['top_products', '`/_design/order_statistics/_view/top_products?group=true`', 'Quantity được cộng theo từng product_id; xem `views_facts.json`'],
    ['revenue_delivered_by_month', '`/_design/order_statistics/_view/revenue_delivered_by_month?group=true&group_level=2`', json_encode($deliveredMonthly, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)],
    ['orders_by_customer', '`/_design/order_statistics/_view/orders_by_customer?reduce=false&include_docs=true&startkey="DEMO-C001"&endkey="DEMO-C015"`', count($customers) . ' documents có customer_id trong khoảng'],
] as $row) $lines[] = '| ' . implode(' | ', $row) . ' |';
$lines[] = '';
$lines[] = 'View `orders_by_month_total_sum` giữ lại hợp đồng cũ của view `orders_by_month` trước Phase C (key `YYYY-MM`, reduce `_sum`). Các view cũ `orders_by_status` cũng được giữ nguyên; hai tên view lịch sử `order_count_by_status` và `order_total_by_status` hiện diện tương thích.';
$lines[] = '';
$lines[] = 'Kết quả kiểm tra từng view:';
$lines[] = '';
foreach ($evidence as $label => $check) $lines[] = '- **' . $check['status'] . '** `' . $label . '`.';
file_put_contents($docsDir . '/VIEWS.md', implode(PHP_EOL, $lines) . PHP_EOL, LOCK_EX);
if (in_array('FAIL', array_column($evidence, 'status'), true)) exit(1);
