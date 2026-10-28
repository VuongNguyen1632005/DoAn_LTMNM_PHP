$ErrorActionPreference = 'Stop'
$root = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$zip = Join-Path $root 'dist/source_code.zip'
$target = Join-Path $root ('tmp/source_package_smoke_' + [guid]::NewGuid().ToString('N'))
$project = 'codex-source-package-smoke'
$expanded = $false
$up = $false
New-Item -ItemType Directory -Path $target -Force | Out-Null
try {
    Expand-Archive -LiteralPath $zip -DestinationPath $target
    $expanded = $true
    Copy-Item -LiteralPath (Join-Path $target '.env.example') -Destination (Join-Path $target '.env')
    $bytes = [byte[]]::new(32)
    $rng = [System.Security.Cryptography.RandomNumberGenerator]::Create()
    try { $rng.GetBytes($bytes) } finally { $rng.Dispose() }
    $secret = ($bytes | ForEach-Object { $_.ToString('x2') }) -join ''
    $envPath = Join-Path $target '.env'
    $envText = [System.IO.File]::ReadAllText($envPath)
    $envText = $envText -replace '(?m)^APP_PORT=.*$', 'APP_PORT=18081'
    $envText = $envText -replace '(?m)^COUCHDB_PORT=.*$', 'COUCHDB_PORT=15984'
    $envText = $envText -replace '(?m)^COUCHDB_USER=.*$', 'COUCHDB_USER=package_smoke_user'
    $envText = $envText -replace '(?m)^COUCHDB_PASSWORD=.*$', "COUCHDB_PASSWORD=$secret"
    $envText = $envText -replace '(?m)^JWT_SECRET=.*$', "JWT_SECRET=$secret"
    [System.IO.File]::WriteAllText($envPath, $envText, [System.Text.UTF8Encoding]::new($false))
    Push-Location $target
    try {
        $up = $true
        docker compose -p $project up --build -d
        if ($LASTEXITCODE -ne 0) { throw 'docker compose up failed' }
        docker compose -p $project exec -T app composer db:seed-demo -- --recreate
        if ($LASTEXITCODE -ne 0) { throw 'demo seeder failed' }
        docker compose -p $project exec -T app composer seed:verify
        if ($LASTEXITCODE -ne 0) { throw 'seed verification failed' }
    }
    finally { Pop-Location }
    Write-Output 'PACKAGE_RUNTIME_SMOKE: PASS (fresh extraction, compose up, seed, verify)'
}
finally {
    if ($up) {
        Push-Location $target
        try { docker compose -p $project down -v --remove-orphans | Out-Null }
        finally { Pop-Location }
    }
    $fullTarget = [System.IO.Path]::GetFullPath($target)
    if ($fullTarget.StartsWith($root + [System.IO.Path]::DirectorySeparatorChar, [StringComparison]::OrdinalIgnoreCase) -and $fullTarget -match 'source_package_smoke_[0-9a-f]{32}$') {
        Remove-Item -LiteralPath $fullTarget -Recurse -Force
    }
}
