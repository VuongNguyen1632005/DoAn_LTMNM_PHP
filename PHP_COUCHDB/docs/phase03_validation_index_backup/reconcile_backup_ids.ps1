$ErrorActionPreference = 'Stop'
$ProgressPreference = 'SilentlyContinue'
$ev = 'docs/phase03_validation_index_backup/evidence'
$seed = @(
  foreach ($i in 1..18) { [pscustomobject]@{ id = ('product:P06-{0:D3}' -f $i); type = 'product' } }
  foreach ($i in 1..6) { [pscustomobject]@{ id = ('customer:P06-{0:D3}' -f $i); type = 'customer' } }
  [pscustomobject]@{ id = 'staff:P06-001'; type = 'staff' }
  [pscustomobject]@{ id = 'staff:P06-002'; type = 'staff' }
  [pscustomobject]@{ id = 'shipping_method:BD'; type = 'shipping_method' }
  [pscustomobject]@{ id = 'shipping_method:HT'; type = 'shipping_method' }
  [pscustomobject]@{ id = 'voucher:SAVE10'; type = 'voucher' }
  foreach ($i in 1..27) { [pscustomobject]@{ id = ('order:P06-{0:D4}' -f $i); type = 'order' } }
  [pscustomobject]@{ id = '_design/domain_validation'; type = 'design' }
  [pscustomobject]@{ id = '_design/catalog_indexes'; type = 'design' }
)
$backupPaths = @('var/backups/nosql-demo-20261010.json','var/backups/nosql-demo-20261013.json','var/backups/nosql-demo-20261013-2.json')
$backups = @{}
foreach ($path in $backupPaths) {
  $item = Get-Content -Raw -Encoding utf8 $path | ConvertFrom-Json
  $backups[$path] = @($item.documents | ForEach-Object { [pscustomobject]@{ id = $_._id; type = $(if ($_._id -like '_design/*') { 'design' } else { $_.type }) } })
}
$dotenv = @{}
foreach ($line in Get-Content .env) { if ($line -match '^\s*([A-Za-z_][A-Za-z0-9_]*)\s*=\s*(.*)\s*$') { $dotenv[$Matches[1]] = $Matches[2].Trim('"', "'") } }
$token = [Convert]::ToBase64String([Text.Encoding]::ASCII.GetBytes("$($dotenv['COUCHDB_USER']):$($dotenv['COUCHDB_PASSWORD'])"))
$headers = @{ Authorization = "Basic $token"; Accept = 'application/json' }
$dbNames = @('shopquan_ao_phase06_demo_test','nosql_report_restore_check_test','nosql_report_restore_photo_20261013223526_test')
$live = @{}
foreach ($db in $dbNames) {
  $uri = "http://127.0.0.1:5984/$db/_all_docs?include_docs=true&limit=1000"
  $response = Invoke-WebRequest -Uri $uri -Method Get -Headers $headers -NoProxy -SkipHttpErrorCheck
  if ([int]$response.StatusCode -ne 200) { throw "GET $db returned HTTP $($response.StatusCode): $($response.Content)" }
  $body = $response.Content | ConvertFrom-Json
  $live[$db] = @($body.rows | ForEach-Object { [pscustomobject]@{ id = $_.id; type = $(if ($_.id -like '_design/*') { 'design' } else { $_.doc.type }) } })
}
function Compare-Ids($left, $right, $label) {
  $leftSet = @{}; foreach ($item in $left) { $leftSet[$item.id] = $item.type }
  $rightSet = @{}; foreach ($item in $right) { $rightSet[$item.id] = $item.type }
  foreach ($id in ($leftSet.Keys | Where-Object { $_ -notin $rightSet.Keys } | Sort-Object)) { [pscustomobject]@{ comparison = $label; id = $id; type = $leftSet[$id]; classification = 'present_in_left_absent_in_right' } }
  foreach ($id in ($rightSet.Keys | Where-Object { $_ -notin $leftSet.Keys } | Sort-Object)) { [pscustomobject]@{ comparison = $label; id = $id; type = $rightSet[$id]; classification = 'absent_in_left_present_in_right' } }
}
$backup = $backups[$backupPaths[0]]
$diffs = @()
$diffs += Compare-Ids $seed $backup 'backup_20261010_vs_seed'
$diffs += Compare-Ids $backup $live[$dbNames[0]] 'demo_live_vs_backup_20261010'
$diffs += Compare-Ids $backup $live[$dbNames[1]] 'restore_check_live_vs_backup_20261010'
$diffs += Compare-Ids $backup $live[$dbNames[2]] 'restore_photo_live_vs_backup_20261010'
foreach ($path in $backupPaths[1..2]) { if ((Compare-Object @($backup.id | Sort-Object) @($backups[$path].id | Sort-Object)).Count -ne 0) { throw "Backup snapshots have different IDs: $path" } }
$diffs | Export-Csv -NoTypeInformation -Encoding utf8 -Path (Join-Path $ev 'backup_restore_id_diff.csv')
$summary = [ordered]@{
  checked_at = '2026-10-28 Asia/Saigon'
  method = 'Read-only ID/type comparison; authenticated CouchDB GET; no DB writes.'
  seed = [ordered]@{ source = 'scripts/seed_phase06_demo.php deterministic IDs'; total = $seed.Count }
  backups = @(foreach ($path in $backupPaths) { [ordered]@{ source = $path; total = $backups[$path].Count } })
  live_databases = @(foreach ($db in $dbNames) { [ordered]@{ database = $db; http_status = 200; total = $live[$db].Count } })
  backup_snapshots_same_id_set = $true
  diff_rows = @($diffs)
  source_evidence = @('database/couchdb/design-docs/domain_validation.json','scripts/seed_phase06_demo.php','var/backups/nosql-demo-20261010.json','var/backups/nosql-demo-20261013.json','var/backups/nosql-demo-20261013-2.json','CouchDB GET /shopquan_ao_phase06_demo_test/_all_docs?include_docs=true','CouchDB GET /nosql_report_restore_check_test/_all_docs?include_docs=true','CouchDB GET /nosql_report_restore_photo_20261013223526_test/_all_docs?include_docs=true')
  writes_attempted = $false
}
$summary | ConvertTo-Json -Depth 7 | Set-Content -Encoding utf8 (Join-Path $ev 'backup_restore_id_reconciliation.json')
$diffs | Format-Table -AutoSize
Get-Item (Join-Path $ev 'backup_restore_id_diff.csv'), (Join-Path $ev 'backup_restore_id_reconciliation.json') | Select-Object FullName,Length




