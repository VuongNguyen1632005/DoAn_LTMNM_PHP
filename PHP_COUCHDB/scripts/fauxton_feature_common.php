<?php

declare(strict_types=1);

use App\Infrastructure\CouchDB\CouchDbClient;

require_once dirname(__DIR__) . '/vendor/autoload.php';

function featureClient(?string $user = null, ?string $password = null): CouchDbClient
{
    return new CouchDbClient((string) getenv('COUCHDB_URL'), $user ?? (string) getenv('COUCHDB_USER'), $password ?? (string) getenv('COUCHDB_PASSWORD'));
}

function featureRequest(CouchDbClient $client, string $method, string $path, ?array $body = null): App\Infrastructure\CouchDB\CouchDbResponse
{
    return $client->request($method, $path, $body);
}

function featureJson(CouchDbClient $client, string $method, string $path, ?array $body = null, array $codes = [200, 201, 202]): array
{
    $response = $client->request($method, $path, $body);
    if (!in_array($response->statusCode, $codes, true)) throw new RuntimeException($method . ' ' . $path . ' returned HTTP ' . $response->statusCode . ': ' . $response->body);
    return $response->body === '' ? [] : $response->json();
}

function featureCreateDb(CouchDbClient $client, string $database): void
{
    if (preg_match('/^[a-z][a-z0-9_$()+\/-]*_(scratch|test)$/', $database) !== 1) throw new RuntimeException('Refusing non-scratch/test database: ' . $database);
    $path = rawurlencode($database);
    $existing = $client->request('GET', $path);
    if ($existing->statusCode !== 404) throw new RuntimeException('Scratch database already exists or cannot be inspected (HTTP ' . $existing->statusCode . '): ' . $database);
    featureJson($client, 'PUT', $path, null, [201, 202]);
}

function featureDeleteDb(CouchDbClient $client, string $database): bool
{
    $path = rawurlencode($database);
    $current = $client->request('GET', $path);
    if ($current->statusCode === 404) return true;
    $deleted = $client->request('DELETE', $path);
    if (!in_array($deleted->statusCode, [200, 202, 404], true)) return false;
    return $client->request('GET', $path)->statusCode === 404;
}

function featureDocs(CouchDbClient $client, string $database): array
{
    $result = featureJson($client, 'GET', rawurlencode($database) . '/_all_docs?include_docs=true&limit=10000');
    $docs = [];
    foreach ($result['rows'] ?? [] as $row) if (is_array($row['doc'] ?? null)) $docs[(string) $row['id']] = $row['doc'];
    ksort($docs, SORT_STRING);
    return $docs;
}

function featureNormalize(mixed $value): mixed
{
    if (!is_array($value)) return $value;
    if (!array_is_list($value)) {
        unset($value['_rev']);
        ksort($value, SORT_STRING);
    }
    foreach ($value as $key => $child) $value[$key] = featureNormalize($child);
    return $value;
}

function featureHash(mixed $document): string
{
    return hash('sha256', json_encode(featureNormalize($document), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
}

function featureContentHashes(array $docs): array
{
    $hashes = [];
    foreach ($docs as $id => $doc) $hashes[$id] = featureHash($doc);
    ksort($hashes, SORT_STRING);
    return $hashes;
}

function featureReplicate(CouchDbClient $client, string $source, string $target, bool $continuous = false, bool $cancel = false, ?array $selector = null): array
{
    $base = parse_url((string) getenv('COUCHDB_URL'));
    if (!is_array($base) || !isset($base['scheme'], $base['host'])) throw new RuntimeException('Invalid COUCHDB_URL for replication.');
    $auth = rawurlencode((string) getenv('COUCHDB_USER')) . ':' . rawurlencode((string) getenv('COUCHDB_PASSWORD')) . '@';
    $host = $base['host'] . (isset($base['port']) ? ':' . $base['port'] : '');
    $makeUrl = static fn (string $database): string => $base['scheme'] . '://' . $auth . $host . '/' . rawurlencode($database);
    $payload = ['source' => $makeUrl($source), 'target' => $makeUrl($target), 'create_target' => true, 'continuous' => $continuous, 'cancel' => $cancel];
    if ($selector !== null) $payload['selector'] = $selector;
    return featureJson($client, 'POST', '_replicate', $payload, [200, 201, 202]);
}

function featureOutput(array $evidence, bool $ok): never
{
    $evidence['generated_at_utc'] = gmdate('Y-m-d\TH:i:s\Z');
    $directory = dirname(__DIR__) . '/docs/phase3/feature_evidence';
    if (!is_dir($directory) && !mkdir($directory, 0770, true) && !is_dir($directory)) throw new RuntimeException('Could not create feature evidence directory.');
    $json = json_encode($evidence, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL;
    $filename = preg_replace('/[^a-z0-9_-]+/i', '_', (string) ($evidence['feature'] ?? 'feature')) . '.json';
    file_put_contents($directory . '/' . $filename, $json, LOCK_EX);
    fwrite(STDOUT, $json);
    exit($ok ? 0 : 1);
}
