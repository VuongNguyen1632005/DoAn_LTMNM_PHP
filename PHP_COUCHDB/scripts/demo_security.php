<?php

declare(strict_types=1);

require __DIR__ . '/fauxton_feature_common.php';

$admin = featureClient();
$database = 'retail_security_scratch';
$tag = bin2hex(random_bytes(4));
$memberName = 'phase3_member_' . $tag;
$outsiderName = 'phase3_outsider_' . $tag;
$memberPassword = bin2hex(random_bytes(18));
$outsiderPassword = bin2hex(random_bytes(18));
$userIds = [];
$createdDb = false;
$evidence = ['feature' => 'security', 'database' => $database, 'member_user' => $memberName, 'outsider_user' => $outsiderName, 'cleanup' => ['database' => false, 'users' => []]];
$ok = false;
try {
    featureCreateDb($admin, $database);
    $createdDb = true;
    $usersDb = $admin->request('GET', rawurlencode('_users'));
    if ($usersDb->statusCode === 404) {
        featureJson($admin, 'PUT', rawurlencode('_users'), null, [201, 202]);
        $evidence['users_database_created'] = true;
    } elseif ($usersDb->statusCode !== 200) {
        throw new RuntimeException('Could not inspect CouchDB _users database (HTTP ' . $usersDb->statusCode . ').');
    } else {
        $evidence['users_database_created'] = false;
    }
    foreach ([[$memberName, $memberPassword], [$outsiderName, $outsiderPassword]] as [$username, $password]) {
        $userId = 'org.couchdb.user:' . $username;
        $user = ['_id' => $userId, 'name' => $username, 'type' => 'user', 'roles' => [], 'password' => $password];
        featureJson($admin, 'PUT', rawurlencode('_users') . '/' . rawurlencode($userId), $user, [201]);
        $userIds[] = $userId;
    }
    featureJson($admin, 'PUT', rawurlencode($database) . '/public-demo-document', ['_id' => 'public-demo-document', 'type' => 'permission_demo', 'value' => 1], [201]);
    featureJson($admin, 'PUT', rawurlencode($database) . '/_design/member_test', ['_id' => '_design/member_test', 'language' => 'javascript', 'views' => ['all' => ['map' => 'function (doc) { emit(doc._id, null); }']]], [201]);
    featureJson($admin, 'PUT', rawurlencode($database) . '/_security', ['admins' => ['names' => [], 'roles' => []], 'members' => ['names' => [$memberName], 'roles' => []]], [200, 201]);
    $member = featureClient($memberName, $memberPassword);
    $outsider = featureClient($outsiderName, $outsiderPassword);
    $memberRead = $member->request('GET', rawurlencode($database) . '/public-demo-document');
    $outsiderRead = $outsider->request('GET', rawurlencode($database) . '/public-demo-document');
    $design = featureJson($member, 'GET', rawurlencode($database) . '/_design/member_test');
    $design['views']['all']['map'] = 'function (doc) { emit(doc._id, doc.type); }';
    $designWrite = $member->request('PUT', rawurlencode($database) . '/_design/member_test', $design);
    $security = featureJson($admin, 'GET', rawurlencode($database) . '/_security');
    $evidence += ['security_document' => ['admins' => $security['admins'] ?? null, 'members' => $security['members'] ?? null], 'member_read_http' => $memberRead->statusCode, 'outsider_read_http' => $outsiderRead->statusCode, 'member_design_update_http' => $designWrite->statusCode, 'member_design_update_body' => $designWrite->body];
    $ok = $memberRead->statusCode === 200 && in_array($outsiderRead->statusCode, [401, 403], true) && in_array($designWrite->statusCode, [401, 403], true);
} catch (Throwable $exception) {
    $evidence['error'] = $exception->getMessage();
} finally {
    if ($createdDb) $evidence['cleanup']['database'] = featureDeleteDb($admin, $database);
    foreach (array_reverse($userIds) as $userId) {
        $existing = $admin->request('GET', rawurlencode('_users') . '/' . rawurlencode($userId));
        if ($existing->statusCode === 404) { $evidence['cleanup']['users'][$userId] = true; continue; }
        if ($existing->statusCode !== 200) { $evidence['cleanup']['users'][$userId] = false; continue; }
        $doc = $existing->json();
        $deleted = $admin->request('DELETE', rawurlencode('_users') . '/' . rawurlencode($userId) . '?rev=' . rawurlencode((string) $doc['_rev']));
        $evidence['cleanup']['users'][$userId] = in_array($deleted->statusCode, [200, 202, 404], true) && $admin->request('GET', rawurlencode('_users') . '/' . rawurlencode($userId))->statusCode === 404;
    }
    if (!$evidence['cleanup']['database'] || in_array(false, $evidence['cleanup']['users'], true)) $ok = false;
}
$evidence['status'] = $ok ? 'PASS' : 'FAIL';
featureOutput($evidence, $ok);
