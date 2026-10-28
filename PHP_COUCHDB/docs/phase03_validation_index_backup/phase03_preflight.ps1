$ErrorActionPreference = 'Stop'
$repoRoot = Split-Path (Split-Path $PSScriptRoot -Parent) -Parent
$composeFile = Join-Path $repoRoot 'docker-compose.yml'
$phpProbe = '/var/www/html/docs/phase03_validation_index_backup/phase03_couchdb_preflight.php'

Set-Location -LiteralPath $repoRoot
Write-Host '=== Docker context ==='
docker context show
if ($LASTEXITCODE -ne 0) { throw 'Docker context lookup failed.' }

Write-Host '=== Docker containers (read-only) ==='
docker ps --format 'table {{.Names}}\t{{.Status}}\t{{.Image}}'
if ($LASTEXITCODE -ne 0) { throw 'docker ps failed; stopping before Compose or CouchDB checks.' }

Write-Host '=== Compose service status (read-only) ==='
docker compose -f $composeFile ps --format json
if ($LASTEXITCODE -ne 0) { throw 'Compose status check failed.' }

Write-Host '=== CouchDB and isolated test-database preflight (read-only) ==='
docker compose -f $composeFile exec -T app php $phpProbe
if ($LASTEXITCODE -ne 0) { throw 'CouchDB preflight failed; no database writes were attempted.' }
