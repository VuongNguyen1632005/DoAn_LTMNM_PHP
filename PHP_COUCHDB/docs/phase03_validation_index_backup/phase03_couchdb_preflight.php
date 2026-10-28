<?php
declare(strict_types=1);

$base = rtrim((string) getenv('COUCHDB_URL'), '/');
$user = (string) getenv('COUCHDB_USER');
$password = (string) getenv('COUCHDB_PASSWORD');
$appDatabase = (string) getenv('COUCHDB_DATABASE');
$demoDatabase = 'shopquan_ao_phase06_demo_test';
$expectedApplicationDatabase = 'shopquan_ao_sql_migration_test';
$candidateTestDatabase = 'phase03_validator_index_backup_test';

if ($base === '' || $user === '' || $password === '' || $appDatabase === '') {
    fwrite(STDERR, "Preflight blocked: required CouchDB environment is missing. No secrets were printed.\n");
    exit(2);
}

$curl = curl_init($base . '/_all_dbs');
curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
    CURLOPT_USERPWD => $user . ':' . $password,
    CURLOPT_HTTPHEADER => ['Accept: application/json'],
]);
$body = curl_exec($curl);
$status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
$curlError = curl_error($curl);
curl_close($curl);

if (!is_string($body) || $status !== 200) {
    fwrite(STDERR, json_encode([
        'preflight' => 'BLOCKED',
        'http_status' => $status,
        'transport_error' => $curlError === '' ? null : $curlError,
        'secrets_printed' => false,
        'writes_attempted' => false,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
    exit(3);
}

$databases = json_decode($body, true);
if (!is_array($databases) || !array_is_list($databases) || !is_array(array_filter($databases, 'is_string'))) {
    fwrite(STDERR, "Preflight blocked: CouchDB returned an unexpected database-list response. No writes attempted.\n");
    exit(4);
}

$testExists = in_array($candidateTestDatabase, $databases, true);
$appExists = in_array($appDatabase, $databases, true);
$demoExists = in_array($demoDatabase, $databases, true);
$knownApp = $appDatabase === $expectedApplicationDatabase;
$distinct = $candidateTestDatabase !== $appDatabase && $candidateTestDatabase !== $demoDatabase;
$clear = $knownApp && $appExists && $distinct && !$testExists;

$result = [
    'preflight' => $clear ? 'CANDIDATE_CLEAR_FOR_REVIEW' : 'BLOCKED',
    'couchdb_http_status' => $status,
    'application_database' => $appDatabase,
    'application_database_matches_expected' => $knownApp,
    'application_database_exists' => $appExists,
    'demo_database' => $demoDatabase,
    'demo_database_exists' => $demoExists,
    'candidate_test_database' => $candidateTestDatabase,
    'candidate_is_distinct_from_application_and_demo' => $distinct,
    'candidate_test_database_already_exists' => $testExists,
    'database_count' => count($databases),
    'secrets_printed' => false,
    'writes_attempted' => false,
];

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
exit($clear ? 0 : 5);

