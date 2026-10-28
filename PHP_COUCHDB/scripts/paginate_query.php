<?php

declare(strict_types=1);

use App\Infrastructure\CouchDB\CouchDbClient;

require dirname(__DIR__) . '/vendor/autoload.php';

function paginationRequest(CouchDbClient $client, string $method, string $path, ?array $body = null): array
{
    $response = $client->request($method, $path, $body);
    if ($response->statusCode < 200 || $response->statusCode >= 300) {
        throw new RuntimeException($method . ' ' . $path . ' returned HTTP ' . $response->statusCode . ': ' . $response->body);
    }
    return $response->json();
}

function paginationField(array $doc, string $path, bool &$exists): mixed
{
    $value = $doc;
    foreach (explode('.', $path) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) { $exists = false; return null; }
        $value = $value[$part];
    }
    $exists = true;
    return $value;
}

function paginationMatches(array $doc, array $selector): bool
{
    foreach ($selector as $field => $condition) {
        if ($field === '$and' || $field === '$or') {
            $matches = array_map(static fn (array $child): bool => paginationMatches($doc, $child), $condition);
            if ($field === '$and' && in_array(false, $matches, true)) return false;
            if ($field === '$or' && !in_array(true, $matches, true)) return false;
            continue;
        }
        $exists = false;
        $actual = paginationField($doc, (string) $field, $exists);
        foreach (is_array($condition) ? $condition : ['$eq' => $condition] as $operator => $expected) {
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
                default => throw new RuntimeException('Unsupported local count operator: ' . $operator),
            };
            if (!$ok) return false;
        }
    }
    return true;
}

$filename = $argv[1] ?? '';
if ($filename === '' || !is_file($filename)) {
    fwrite(STDERR, "Usage: php scripts/paginate_query.php <query-json-file>\n");
    exit(2);
}
$query = json_decode((string) file_get_contents($filename), true, 512, JSON_THROW_ON_ERROR);
$databaseName = (string) (getenv('DEMO_DATABASE') ?: getenv('COUCHDB_DATABASE') ?: 'retail_order_delivery_demo');
if (preg_match('/^[a-z][a-z0-9_$()+\/-]*_demo$/', $databaseName) !== 1) throw new RuntimeException('Pagination is read-only and only allowed against *_demo.');
$client = new CouchDbClient((string) getenv('COUCHDB_URL'), (string) getenv('COUCHDB_USER'), (string) getenv('COUCHDB_PASSWORD'));
$db = rawurlencode($databaseName);
$all = paginationRequest($client, 'GET', $db . '/_all_docs?include_docs=true&limit=1000');
$docs = array_values(array_map(static fn (array $row): array => $row['doc'] ?? [], array_filter($all['rows'] ?? [], static fn (array $row): bool => !str_starts_with((string) ($row['id'] ?? ''), '_design/'))));
$expectedIds = [];
foreach ($docs as $doc) if (paginationMatches($doc, $query['selector'] ?? [])) $expectedIds[] = $doc['_id'];

$limit = (int) ($query['limit'] ?? 25);
if ($limit < 1) throw new RuntimeException('Query limit must be positive.');
$request = $query;
$request['limit'] = $limit;
$request['execution_stats'] = true;
$bookmark = null;
$pages = 0;
$ids = [];
$pageEvidence = [];
do {
    if ($bookmark !== null) $request['bookmark'] = $bookmark;
    $result = paginationRequest($client, 'POST', $db . '/_find', $request);
    ++$pages;
    $pageDocs = $result['docs'] ?? [];
    foreach ($pageDocs as $doc) $ids[] = (string) $doc['_id'];
    $pageEvidence[] = ['page' => $pages, 'returned' => count($pageDocs), 'docs_examined' => $result['execution_stats']['total_docs_examined'] ?? null];
    $next = $result['bookmark'] ?? null;
    if ($pages > 1000) throw new RuntimeException('Pagination exceeded 1000 pages; stopping to avoid an unbounded request loop.');
    if ($pageDocs === [] || !is_string($next) || $next === '' || $next === $bookmark) break;
    $bookmark = $next;
} while (true);

$actualUnique = array_values(array_unique($ids));
$expectedUnique = array_values(array_unique($expectedIds));
$missing = array_values(array_diff($expectedUnique, $actualUnique));
$unexpected = array_values(array_diff($actualUnique, $expectedUnique));
$evidence = ['database' => $databaseName, 'query_file' => basename($filename), 'limit' => $limit, 'pages' => $pages, 'results_returned' => count($ids), 'independent_filter_count' => count($expectedIds), 'ids_match' => $missing === [] && $unexpected === [] && count($ids) === count($actualUnique), 'missing_ids' => $missing, 'unexpected_ids' => $unexpected, 'page_evidence' => $pageEvidence];
fwrite(STDOUT, json_encode($evidence, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL);
if (!$evidence['ids_match']) exit(1);
