<?php
declare(strict_types=1);
require '/var/www/html/vendor/autoload.php';
use App\Infrastructure\CouchDB\CouchDbClient;
$db='retail_ui_capture_demo';
if (getenv('COUCHDB_DATABASE')!==$db) throw new RuntimeException('Dedicated capture database required');
$client=new CouchDbClient(getenv('COUCHDB_URL'),getenv('COUCHDB_USER'),getenv('COUCHDB_PASSWORD'));
$pass=bin2hex(random_bytes(18)).'aA!9';
foreach(['staff:DEMO-S001','customer:DEMO-C001'] as $id){$doc=$client->request('GET',$db.'/'.rawurlencode($id))->json();$doc['auth']['password_hash']=password_hash($pass,PASSWORD_DEFAULT);$doc['auth']['requires_password_reset']=false;$r=$client->request('PUT',$db.'/'.rawurlencode($id),$doc);if($r->statusCode!==201)throw new RuntimeException('Account setup rejected');}
file_put_contents('/var/www/html/tools/screenshots/.auth/ui.json',json_encode(['password'=>$pass]));
echo "Capture accounts prepared; credentials stored locally in ignored .auth only.\n";
