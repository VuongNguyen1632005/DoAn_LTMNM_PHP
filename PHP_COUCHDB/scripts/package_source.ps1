$ErrorActionPreference = 'Stop'
$root = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$dist = Join-Path $root 'dist'
New-Item -ItemType Directory -Path $dist -Force | Out-Null
$zipPath = Join-Path $dist 'source_code.zip'
if (Test-Path -LiteralPath $zipPath) { Remove-Item -LiteralPath $zipPath -Force }
Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem
$archive = [System.IO.Compression.ZipFile]::Open($zipPath, [System.IO.Compression.ZipArchiveMode]::Create)
$manifest = [System.Collections.Generic.List[string]]::new()
$skipped = [System.Collections.Generic.List[string]]::new()
$secretPattern = '(?im)^\s*(?:COUCHDB_PASSWORD|JWT_SECRET|API_KEY|ACCESS_TOKEN)\s*[:=]\s*["'']?([A-Za-z0-9_./+\-]{16,})'
$textExtensions = @('.php','.json','.md','.yml','.yaml','.xml','.html','.css','.js','.ps1','.txt','.example','.lock')
try {
    Get-ChildItem -LiteralPath $root -File -Recurse -Force | ForEach-Object {
        $file = $_
        $relative = $file.FullName.Substring($root.Length + 1).Replace('\','/')
        $parts = $relative.Split('/')
        $excluded = $relative -ceq '.env' -or $parts -contains '.git' -or $parts -contains 'vendor' -or $parts -contains 'node_modules' -or $parts -contains 'dist' -or $parts -contains 'var' -or $relative.StartsWith('docs/report/') -or $relative.StartsWith('docs/final_report/') -or $relative.StartsWith('docs/phase02_fauxton/') -or $relative.StartsWith('docs/phase03_validation_index_backup/') -or $file.Extension -ieq '.zip' -or $relative -match '\.(log|tmp|swp)$'
        if ($excluded) { $skipped.Add($relative); return }
        $entry = [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($archive, $file.FullName, $relative, [System.IO.Compression.CompressionLevel]::Optimal)
        $hash = (Get-FileHash -LiteralPath $file.FullName -Algorithm SHA256).Hash.ToLowerInvariant()
        $manifest.Add("$hash  $relative")
        if ($textExtensions -contains $file.Extension.ToLowerInvariant() -and $file.Length -lt 5MB) {
            $content = [System.IO.File]::ReadAllText($file.FullName)
            foreach ($match in [regex]::Matches($content, $secretPattern)) {
                $value = $match.Groups[1].Value
                if ($value -match '(?i)replace-with|example|placeholder|your-|<|\$\{') { continue }
                $skipped.Add("SECRET_SCAN_REVIEW:$relative")
            }
        }
    }
    $manifestEntry = $archive.CreateEntry('MANIFEST.sha256')
    $writer = [System.IO.StreamWriter]::new($manifestEntry.Open(), [System.Text.Encoding]::UTF8)
    try { $writer.WriteLine(($manifest | Sort-Object) -join "`n") } finally { $writer.Dispose() }
}
finally { $archive.Dispose() }
$review = @($skipped | Where-Object { $_ -like 'SECRET_SCAN_REVIEW:*' })
if ($review.Count -gt 0) {
    Write-Output 'SECRET_SCAN: REVIEW REQUIRED'
    $review | ForEach-Object { Write-Output $_ }
    exit 1
}
Write-Output "Created $zipPath"
Write-Output "Included files: $($manifest.Count)"
Write-Output 'Excluded paths: .env, vendor, node_modules, .git, dist, working report/draft folders, legacy phase handoffs, all var runtime data, nested zip files, logs and temp files'
Write-Output 'SECRET_SCAN: PASS (no credential-like values found; .env excluded)'
