<?php

declare(strict_types=1);

use App\Infrastructure\CouchDB\CouchDbClient;

require dirname(__DIR__) . '/vendor/autoload.php';

function verifyDemoFail(string $message): never
{
    fwrite(STDERR, 'FAIL: ' . $message . PHP_EOL);
    exit(1);
}

$database = (string) (getenv('DEMO_DATABASE') ?: 'retail_order_delivery_demo');
if (preg_match('/^[a-z][a-z0-9_$()+\/-]*_demo$/', $database) !== 1) verifyDemoFail('DEMO_DATABASE must match *_demo.');
$client = new CouchDbClient((string) getenv('COUCHDB_URL'), (string) getenv('COUCHDB_USER'), (string) getenv('COUCHDB_PASSWORD'));
$path = rawurlencode($database);
$response = $client->request('GET', $path . '/_all_docs?include_docs=true&limit=1000');
if ($response->statusCode !== 200) verifyDemoFail('Could not read demo database (HTTP ' . $response->statusCode . ').');
$payload = $response->json();
$rows = $payload['rows'] ?? null;
if (!is_array($rows) || !is_int($payload['total_rows'] ?? null) || count($rows) !== $payload['total_rows']) verifyDemoFail('Incomplete database document listing.');
$counts = [];
$orders = [];
$documents = [];
$designs = 0;
foreach ($rows as $row) {
    $doc = $row['doc'] ?? null;
    if (!is_array($doc)) verifyDemoFail('A database row has no document.');
    $id = (string) ($row['id'] ?? '');
    if (str_starts_with($id, '_design/')) { ++$designs; continue; }
    if (($doc['meta']['phase1_demo']['dataset_version'] ?? null) !== 1) verifyDemoFail('Document is not owned by this dataset: ' . $id);
    $type = (string) ($doc['type'] ?? '');
    $counts[$type] = ($counts[$type] ?? 0) + 1;
    $documents[$id] = $doc;
    if ($type === 'order') $orders[] = $doc;
}
$minimums = ['customer' => 15, 'staff' => 3, 'product' => 30, 'order' => 60, 'voucher' => 3, 'shipping_method' => 3, 'review' => 20, 'cart' => 1];
foreach ($minimums as $type => $minimum) if (($counts[$type] ?? 0) < $minimum) verifyDemoFail($type . ' count is below ' . $minimum . '.');
if ($designs !== 3 || count($rows) < 150 || count($rows) > 200) verifyDemoFail('Expected 150–200 total documents including exactly 3 design documents.');

$orderStatuses = [];
$trackingStatuses = [];
$guests = 0;
$retry = false;
foreach ($documents as $document) {
    if (($document['type'] ?? null) !== 'product') continue;
    if (count($document['variants'] ?? []) < 2) verifyDemoFail('Product has fewer than two variants: ' . $document['_id']);
}
foreach ($orders as $order) {
    $orderStatuses[$order['status']] = ($orderStatuses[$order['status']] ?? 0) + 1;
    if (($order['guest_order'] ?? false) === true) ++$guests;
    $total = $order['totals'] ?? [];
    if (($total['subtotal'] ?? null) + ($total['shipping_fee'] ?? null) - ($total['discount_amount'] ?? null) !== ($total['grand_total'] ?? null)) verifyDemoFail('Order total mismatch: ' . $order['_id']);
    if (count($order['items'] ?? []) < 1 || count($order['items']) > 4) verifyDemoFail('Order item count must be 1–4: ' . $order['_id']);
    foreach ($order['items'] as $item) if (($item['line_total'] ?? null) !== ($item['unit_price'] ?? null) * ($item['quantity'] ?? 0)) verifyDemoFail('Order line total mismatch: ' . $order['_id']);
    $tracking = $order['delivery_tracking'] ?? null;
    if (!is_array($tracking)) continue;
    $status = (string) ($tracking['status'] ?? '');
    $trackingStatuses[$status] = ($trackingStatuses[$status] ?? 0) + 1;
    $history = array_column($tracking['history'] ?? [], 'status');
    if (count($history) < 1 || end($history) !== $status) verifyDemoFail('Delivery history does not end at current status: ' . $order['_id']);
    for ($i = 1; $i < count($history); ++$i) {
        $allowed = ['created' => ['picked_up', 'failed_delivery'], 'picked_up' => ['in_transit', 'failed_delivery'], 'in_transit' => ['out_for_delivery', 'failed_delivery'], 'out_for_delivery' => ['delivered', 'failed_delivery'], 'failed_delivery' => ['in_transit', 'out_for_delivery'], 'delivered' => []];
        if (!in_array($history[$i], $allowed[$history[$i - 1]] ?? [], true)) {
            if (!($history[$i - 1] === $history[$i])) verifyDemoFail('Invalid delivery transition in ' . $order['_id']);
        }
    }
    for ($i = 0; $i + 1 < count($history); ++$i) if ($history[$i] === 'failed_delivery' && in_array($history[$i + 1], ['in_transit', 'out_for_delivery'], true)) $retry = true;
}
if ($guests < 5) verifyDemoFail('Fewer than five guest orders.');
foreach (['pending', 'confirmed', 'packing', 'shipping', 'delivered', 'cancelled'] as $status) if (!isset($orderStatuses[$status])) verifyDemoFail('No order found in status ' . $status . '.');
foreach (['created', 'picked_up', 'in_transit', 'out_for_delivery', 'failed_delivery'] as $status) if (!isset($trackingStatuses[$status])) verifyDemoFail('No delivery found in status ' . $status . '.');
if (($trackingStatuses['failed_delivery'] ?? 0) < 3 || !$retry) verifyDemoFail('Need at least three current failed deliveries and one failed-then-retried delivery.');
$designIds = array_values(array_filter(array_column($rows, 'id'), static fn (string $id): bool => str_starts_with($id, '_design/')));
foreach (['_design/catalog_indexes', '_design/domain_validation', '_design/order_statistics'] as $id) if (!in_array($id, $designIds, true)) verifyDemoFail('Missing design document ' . $id . '.');

ksort($counts);
ksort($orderStatuses);
ksort($trackingStatuses);
fwrite(STDOUT, 'PASS: verified ' . count($rows) . ' documents in ' . $database . '; business types=' . json_encode($counts, JSON_UNESCAPED_UNICODE) . '; order status=' . json_encode($orderStatuses) . '; delivery status=' . json_encode($trackingStatuses) . '; guest_orders=' . $guests . '; retry=yes.' . PHP_EOL);
