<?php

declare(strict_types=1);

use App\Infrastructure\CouchDB\CouchDbClient;

require dirname(__DIR__) . '/vendor/autoload.php';

function phase1Fail(string $message): never
{
    fwrite(STDERR, $message . PHP_EOL);
    exit(1);
}

function phase1Request(CouchDbClient $client, string $method, string $path, ?array $body = null, array $codes = [200, 201, 202]): array
{
    $response = $client->request($method, $path, $body);
    if (!in_array($response->statusCode, $codes, true)) {
        $payload = json_decode($response->body, true);
        phase1Fail($method . ' ' . $path . ' failed (HTTP ' . $response->statusCode . '): ' . (is_array($payload) ? ($payload['reason'] ?? $payload['error'] ?? 'request rejected') : 'invalid response'));
    }
    return $response->body === '' ? [] : $response->json();
}

function phase1Doc(string $id, string $type, array $fields): array
{
    return array_merge(['_id' => $id, 'type' => $type, 'schema_version' => 2, 'meta' => ['phase1_demo' => ['dataset_version' => 1]]], $fields);
}

function phase1CustomerId(int $n): string { return 'DEMO-C' . str_pad((string) $n, 3, '0', STR_PAD_LEFT); }
function phase1ProductId(int $n): string { return 'DEMO-P' . str_pad((string) $n, 3, '0', STR_PAD_LEFT); }
function phase1Iso(DateTimeImmutable $date): string { return $date->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z'); }

$database = (string) (getenv('DEMO_DATABASE') ?: 'retail_order_delivery_demo');
if (preg_match('/^[a-z][a-z0-9_$()+\/-]*_demo$/', $database) !== 1 || $database === (string) getenv('COUCHDB_DATABASE')) {
    phase1Fail('Refusing target: DEMO_DATABASE must match *_demo and differ from COUCHDB_DATABASE.');
}
foreach (['COUCHDB_URL', 'COUCHDB_USER', 'COUCHDB_PASSWORD'] as $key) {
    if (trim((string) getenv($key)) === '') phase1Fail('Missing CouchDB configuration: ' . $key);
}
$recreate = in_array('--recreate', $argv, true);
$unknown = array_values(array_diff(array_slice($argv, 1), ['--recreate']));
if ($unknown !== []) phase1Fail('Usage: php scripts/seed_final_demo.php [--recreate]');
$client = new CouchDbClient((string) getenv('COUCHDB_URL'), (string) getenv('COUCHDB_USER'), (string) getenv('COUCHDB_PASSWORD'));
$dbPath = rawurlencode($database);
$existing = $client->request('GET', $dbPath);
if ($existing->statusCode === 200) {
    if (!$recreate) phase1Fail('Demo database already exists; refusing to overwrite it. Pass --recreate only for a *_demo database.');
    $rows = phase1Request($client, 'GET', $dbPath . '/_all_docs?include_docs=true&limit=1000');
    foreach ($rows['rows'] ?? [] as $row) {
        $doc = $row['doc'] ?? [];
        if (!str_starts_with((string) ($row['id'] ?? ''), '_design/') && ($doc['meta']['phase1_demo']['dataset_version'] ?? null) !== 1) {
            phase1Fail('Recreate refused: database contains a document not owned by this Phase 1 seeder.');
        }
        if (str_starts_with((string) ($row['id'] ?? ''), '_design/') && !in_array($row['id'], ['_design/catalog_indexes', '_design/domain_validation', '_design/order_statistics'], true)) {
            phase1Fail('Recreate refused: database contains an unrecognized design document.');
        }
    }
    phase1Request($client, 'DELETE', $dbPath);
    phase1Request($client, 'PUT', $dbPath, null, [201, 202]);
} elseif ($existing->statusCode === 404) {
    phase1Request($client, 'PUT', $dbPath, null, [201, 202]);
} else {
    phase1Fail('Could not safely inspect demo database (HTTP ' . $existing->statusCode . ').');
}

$root = dirname(__DIR__);
$docs = [];
$customerNames = [];
for ($i = 1; $i <= 15; ++$i) {
    $legacyId = phase1CustomerId($i);
    $customerNames[$i] = 'Khách Demo ' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
    $docs[] = phase1Doc('customer:' . $legacyId, 'customer', [
        'legacy_id' => $legacyId,
        'auth' => ['username' => 'customer' . str_pad((string) $i, 2, '0', STR_PAD_LEFT) . '@example.test', 'requires_password_reset' => true],
        'profile' => ['name' => $customerNames[$i], 'phone' => '000000' . str_pad((string) $i, 4, '0', STR_PAD_LEFT), 'email' => 'customer' . str_pad((string) $i, 2, '0', STR_PAD_LEFT) . '@example.test', 'addresses' => []],
        'active' => true,
    ]);
}
for ($i = 1; $i <= 3; ++$i) {
    $role = $i === 1 ? 'manager' : 'staff';
    $docs[] = phase1Doc('staff:DEMO-S' . str_pad((string) $i, 3, '0', STR_PAD_LEFT), 'staff', [
        'legacy_id' => 'DEMO-S' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
        'auth' => ['username' => 'staff' . $i . '@example.test', 'requires_password_reset' => true],
        'profile' => ['name' => ($i === 1 ? 'Quản lý Demo' : 'Nhân viên Demo ' . $i)],
        'role' => $role,
        'active' => true,
    ]);
}

$categories = ['áo', 'quần', 'váy', 'phụ kiện', 'giày'];
for ($i = 1; $i <= 30; ++$i) {
    $productId = phase1ProductId($i);
    $price = 120000 + ($i * 7000);
    $docs[] = phase1Doc('product:' . $productId, 'product', [
        'legacy_id' => $productId,
        'name' => 'Sản phẩm Demo ' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
        'description' => 'Sản phẩm tổng hợp dùng cho truy vấn CouchDB.',
        'category' => $categories[($i - 1) % count($categories)],
        'images' => [],
        'variants' => [
            ['variant_id' => $productId . '-A', 'size' => 'M', 'price' => $price, 'stock' => 40 + $i, 'active' => true],
            ['variant_id' => $productId . '-B', 'size' => 'L', 'price' => $price + 10000, 'stock' => 35 + $i, 'active' => true],
        ],
        'active' => true,
    ]);
}

foreach ([['SAVE10', 'percent', 10], ['CASH20', 'cash', 20000], ['FREESHIP', 'shipping', 0]] as [$code, $kind, $value]) {
    $docs[] = phase1Doc('voucher:' . $code, 'voucher', ['code' => $code, 'discount_type' => $kind, 'value' => $value, 'remaining_quantity' => 50, 'active' => true]);
}
foreach ([['BD', 'Bưu điện', 20000], ['HT', 'Hỏa tốc', 35000], ['GH', 'Giao hàng Demo', 25000]] as [$code, $name, $fee]) {
    $docs[] = phase1Doc('shipping_method:' . $code, 'shipping_method', ['code' => $code, 'name' => $name, 'fee' => $fee, 'active' => true, 'currency' => 'VND']);
}

$statusPlan = array_merge(array_fill(0, 11, 'pending'), array_fill(0, 11, 'confirmed'), array_fill(0, 11, 'packing'), array_fill(0, 15, 'shipping'), array_fill(0, 10, 'delivered'), array_fill(0, 7, 'cancelled'));
$orderDocs = [];
$shippingOrderIds = [];
for ($i = 1; $i <= 65; ++$i) {
    $status = $statusPlan[$i - 1];
    $guest = $i <= 15;
    $customerNumber = (($i - 1) % 15) + 1;
    $customerId = phase1CustomerId($customerNumber);
    $ordered = new DateTimeImmutable('2026-06-01T00:00:00Z');
    $ordered = $ordered->modify('+' . intdiv(($i - 1) * 145, 64) . ' days')->modify('+' . (($i * 13) % 1440) . ' minutes');
    $orderedAt = phase1Iso($ordered);
    $items = [];
    $quantityTotal = 0;
    $subtotal = 0;
    $itemCount = (($i - 1) % 4) + 1;
    for ($j = 0; $j < $itemCount; ++$j) {
        $productNumber = (($i + $j * 7 - 1) % 30) + 1;
        $variant = (($i + $j) % 2) === 0 ? 'A' : 'B';
        $unitPrice = 120000 + ($productNumber * 7000) + ($variant === 'B' ? 10000 : 0);
        $quantity = (($i + $j) % 3) + 1;
        $lineTotal = $unitPrice * $quantity;
        $items[] = ['product_id' => 'product:' . phase1ProductId($productNumber), 'product_name' => 'Sản phẩm Demo ' . str_pad((string) $productNumber, 2, '0', STR_PAD_LEFT), 'variant_id' => phase1ProductId($productNumber) . '-' . $variant, 'size' => $variant === 'A' ? 'M' : 'L', 'quantity' => $quantity, 'unit_price' => $unitPrice, 'line_total' => $lineTotal, 'currency' => 'VND'];
        $quantityTotal += $quantity;
        $subtotal += $lineTotal;
    }
    $shippingFee = [20000, 35000, 25000][($i - 1) % 3];
    $discount = $i % 6 === 0 ? min(20000, $subtotal) : 0;
    $grandTotal = $subtotal + $shippingFee - $discount;
    $orderId = 'order:DEMO-O' . str_pad((string) $i, 3, '0', STR_PAD_LEFT);
    $paths = [
        'pending' => ['pending'],
        'confirmed' => ['pending', 'confirmed'],
        'packing' => ['pending', 'confirmed', 'packing'],
        'shipping' => ['pending', 'confirmed', 'packing', 'shipping'],
        'delivered' => ['pending', 'confirmed', 'packing', 'shipping', 'delivered'],
        'cancelled' => ['pending', 'cancelled'],
    ];
    $history = [];
    foreach ($paths[$status] as $step => $eventStatus) {
        $history[] = ['status' => $eventStatus, 'at' => phase1Iso($ordered->modify('+' . ($step * 30) . ' minutes')), 'source' => 'phase1_demo'];
    }
    $paid = $status === 'delivered' || ($i % 13 === 0 && !in_array($status, ['cancelled', 'shipping'], true));
    $method = $i % 5 === 0 ? 'bank_transfer' : ($i % 7 === 0 ? 'card' : 'cod');
    $order = phase1Doc($orderId, 'order', [
        'legacy_id' => 'DEMO-O' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
        'customer' => $guest ? null : ['customer_id' => $customerId, 'name' => $customerNames[$customerNumber]],
        'guest_order' => $guest,
        'assigned_staff_id' => 'staff:DEMO-S002',
        'receiver' => ['name' => $guest ? 'Khách vãng lai Demo' : $customerNames[$customerNumber], 'phone' => '000000' . str_pad((string) $customerNumber, 4, '0', STR_PAD_LEFT), 'address' => 'Địa chỉ tổng hợp ' . str_pad((string) $i, 3, '0', STR_PAD_LEFT) . ', TP Demo'],
        'items' => $items,
        'shipping' => ['method_code' => ['BD', 'HT', 'GH'][($i - 1) % 3], 'method_name' => ['Bưu điện', 'Hỏa tốc', 'Giao hàng Demo'][($i - 1) % 3], 'fee' => $shippingFee],
        'discount' => ['amount' => $discount],
        'payment' => ['method' => $method, 'status' => $paid ? 'paid' : 'unpaid', 'paid' => $paid] + ($paid ? ['paid_at' => phase1Iso($ordered->modify('+1 hour'))] : []),
        'totals' => ['total_quantity' => $quantityTotal, 'subtotal' => $subtotal, 'shipping_fee' => $shippingFee, 'discount_amount' => $discount, 'grand_total' => $grandTotal, 'currency' => 'VND'],
        'status' => $status,
        'status_history' => $history,
        'note' => '',
        'ordered_at' => $orderedAt,
        'active' => true,
    ]);
    if ($paid) $order['payment_history'] = [['status' => 'paid', 'at' => phase1Iso($ordered->modify('+1 hour')), 'source' => 'phase1_demo', 'staff_id' => 'staff:DEMO-S002', 'method' => $method]];
    if ($status === 'shipping') {
        $order['delivery_tracking'] = ['tracking_code' => 'DEMO-TRK-' . str_pad((string) $i, 3, '0', STR_PAD_LEFT), 'status' => 'created', 'estimated_delivery_date' => $ordered->modify('+5 days')->format('Y-m-d'), 'delivered_at' => null, 'history' => [['status' => 'created', 'at' => phase1Iso($ordered->modify('+3 hours')), 'note' => 'Tạo vận đơn demo', 'updated_by' => 'staff:DEMO-S002']]];
        $shippingOrderIds[$i] = $orderId;
    }
    $orderDocs[] = $order;
}
array_push($docs, ...$orderDocs);

for ($i = 1; $i <= 20; ++$i) {
    $productNumber = (($i * 7 - 1) % 30) + 1;
    $docs[] = phase1Doc('review:DEMO-R' . str_pad((string) $i, 3, '0', STR_PAD_LEFT), 'review', [
        'legacy_id' => 'DEMO-R' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
        'product_id' => 'product:' . phase1ProductId($productNumber),
        'customer_id' => null,
        'reviewer_name' => 'Người đánh giá Demo ' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
        'rating' => (($i - 1) % 5) + 1,
        'content' => 'Đánh giá tổng hợp số ' . $i . '.',
        'active' => true,
    ]);
}
for ($i = 1; $i <= 8; ++$i) {
    $customerId = phase1CustomerId($i);
    $productNumber = $i;
    $docs[] = phase1Doc('cart:' . $customerId, 'cart', ['customer_id' => $customerId, 'items' => [['product_id' => 'product:' . phase1ProductId($productNumber), 'variant_id' => phase1ProductId($productNumber) . '-A', 'size' => 'M', 'quantity' => 1, 'unit_price_snapshot' => 127000]], 'updated_at' => '2026-10-01T00:00:00Z']);
}

// Install the validator before business data so CouchDB validates every business write.
$validator = json_decode((string) file_get_contents($root . '/database/couchdb/design-docs/domain_validation.json'), true, 512, JSON_THROW_ON_ERROR);
$statistics = json_decode((string) file_get_contents($root . '/database/couchdb/design-docs/order_statistics.json'), true, 512, JSON_THROW_ON_ERROR);
$catalog = json_decode((string) file_get_contents($root . '/database/couchdb/indexes/catalog_indexes.json'), true, 512, JSON_THROW_ON_ERROR);
$catalogBeforeDeliveryIndex = $catalog;
unset($catalogBeforeDeliveryIndex['views']['delivery_status']);
$catalogBeforeDeliveryIndex['meta'] = ['phase1_demo' => ['dataset_version' => 1]];
foreach ([$catalogBeforeDeliveryIndex, $validator, $statistics] as $design) {
    $design['meta'] = ['phase1_demo' => ['dataset_version' => 1]];
    $designId = rawurlencode((string) $design['_id']);
    phase1Request($client, 'PUT', $dbPath . '/' . $designId, $design, [201, 202]);
}
foreach (array_chunk($docs, 40) as $batch) {
    $results = phase1Request($client, 'POST', $dbPath . '/_bulk_docs', ['docs' => $batch]);
    foreach ($results as $result) if (($result['ok'] ?? false) !== true) phase1Fail('Validator rejected ' . ($result['id'] ?? 'unknown') . ': ' . ($result['reason'] ?? $result['error'] ?? 'unknown error'));
}

// Append valid deterministic delivery transitions after every shipping document begins at created.
$deliveryPaths = [
    35 => ['picked_up'], 36 => ['picked_up'], 37 => ['picked_up', 'in_transit'],
    38 => ['picked_up', 'in_transit'], 39 => ['picked_up', 'in_transit'],
    40 => ['picked_up', 'in_transit', 'out_for_delivery'], 41 => ['picked_up', 'in_transit', 'out_for_delivery'],
    42 => ['picked_up', 'in_transit', 'out_for_delivery', 'failed_delivery'],
    43 => ['picked_up', 'in_transit', 'failed_delivery'], 44 => ['picked_up', 'in_transit', 'failed_delivery'],
    45 => ['picked_up', 'in_transit', 'failed_delivery', 'in_transit'],
    46 => ['picked_up', 'in_transit'], 48 => ['picked_up'],
];
foreach ($deliveryPaths as $orderNumber => $transitions) {
    $orderId = $shippingOrderIds[$orderNumber] ?? null;
    if ($orderId === null) continue;
    $order = phase1Request($client, 'GET', $dbPath . '/' . rawurlencode($orderId));
    foreach ($transitions as $step => $target) {
        $at = phase1Iso((new DateTimeImmutable($order['ordered_at']))->modify('+' . (4 + $step) . ' hours'));
        $order['delivery_tracking']['status'] = $target;
        $order['delivery_tracking']['history'][] = ['status' => $target, 'at' => $at, 'note' => 'Cập nhật delivery demo: ' . $target, 'updated_by' => 'staff:DEMO-S002'];
        $order['_rev'] = phase1Request($client, 'PUT', $dbPath . '/' . rawurlencode($orderId), $order, [201, 202])['rev'];
    }
}

// Capture a measured pre-index baseline on this freshly seeded database before creating delivery_status.
$baseline = ['database' => $database, 'queries' => []];
foreach (['09_delivery_in_transit.json', '10_delivery_out_for_delivery.json', '11_delivery_failed.json'] as $queryFile) {
    $query = json_decode((string) file_get_contents($root . '/database/couchdb/demo_queries/' . $queryFile), true, 512, JSON_THROW_ON_ERROR);
    // Without the composite index, Mango cannot satisfy ordered_at sorting; compare the same selector/result set unsorted.
    unset($query['use_index'], $query['sort']);
    $explain = phase1Request($client, 'POST', $dbPath . '/_explain', $query);
    $result = phase1Request($client, 'POST', $dbPath . '/_find', $query);
    $baseline['queries'][$queryFile] = [
        'index' => $explain['index']['name'] ?? null,
        'warning' => $explain['warning'] ?? null,
        'results_returned' => count($result['docs'] ?? []),
        'execution_stats' => $result['execution_stats'] ?? null,
    ];
}
$baselinePath = $root . '/var/phase1_delivery_baseline.json';
if (!is_dir(dirname($baselinePath)) && !mkdir(dirname($baselinePath), 0770, true) && !is_dir(dirname($baselinePath))) phase1Fail('Could not create phase1 baseline directory.');
if (file_put_contents($baselinePath, json_encode($baseline, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL, LOCK_EX) === false) phase1Fail('Could not save delivery baseline metrics.');

// Add the new index definition only after the no-delivery-index baseline has been measured.
$catalogPath = $dbPath . '/_design/catalog_indexes';
$currentCatalog = phase1Request($client, 'GET', $catalogPath);
$catalog['_rev'] = $currentCatalog['_rev'];
$catalog['meta'] = ['phase1_demo' => ['dataset_version' => 1]];
phase1Request($client, 'PUT', $catalogPath, $catalog, [201, 202]);

$env = getenv();
if (!is_array($env)) phase1Fail('Could not read process environment for index installation.');
$env['COUCHDB_DATABASE'] = $database;
$process = proc_open([PHP_BINARY, $root . '/scripts/create_catalog_indexes.php'], [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, $root, $env);
if (!is_resource($process)) phase1Fail('Could not start catalog index setup.');
fclose($pipes[0]);
$stdout = stream_get_contents($pipes[1]);
$stderr = stream_get_contents($pipes[2]);
fclose($pipes[1]);
fclose($pipes[2]);
if (proc_close($process) !== 0) phase1Fail('Catalog index setup failed: ' . trim((string) $stderr));

fwrite(STDOUT, 'Seeded ' . $database . ' with ' . count($docs) . ' business documents and 3 design documents. Content and timestamps are deterministic.' . PHP_EOL . (string) $stdout);
