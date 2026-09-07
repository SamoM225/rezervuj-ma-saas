# ============================================================================
#  Easy deploy: rezervuj-ma-booking  ->  box (192.168.100.50)
#  Uploads the current WORKING copy (including uncommitted changes) to the
#  server and runs composer install + migrations + caches + restart.
#
#  Usage:  right click -> Run with PowerShell
#      or: powershell -ExecutionPolicy Bypass -File deploy.ps1
#  Options: -Build       run `npm run build` first (after frontend changes)
#           -Bootstrap   first deploy: create DB, .env, systemd units, seed demo
#           -PublicUrl   APP_URL used by -Bootstrap (default LAN address)
#  Auth: ssh key ~/.ssh/id_ed25519 if present, otherwise ssh asks for the password.
# ============================================================================
param([switch]$Build, [switch]$Bootstrap, [string]$PublicUrl = 'http://192.168.100.50:8081')

$ErrorActionPreference = 'Stop'
$proj      = $PSScriptRoot
$server    = 'root@192.168.100.50'
$key       = "$env:USERPROFILE\.ssh\id_ed25519"
$remoteApp = '/opt/webstack/rezervuj'
$tar       = Join-Path $env:TEMP 'rezervuj-deploy.tar.gz'
$sshOpts   = @('-o', 'StrictHostKeyChecking=accept-new')
if (Test-Path $key) { $sshOpts += @('-i', $key) }

Write-Host "== rezervuj-ma deploy -> $server ==" -ForegroundColor Cyan

if ($Build) {
    Write-Host ">> npm run build (frontend assets)..." -ForegroundColor Yellow
    Push-Location $proj; npm run build; Pop-Location
    if ($LASTEXITCODE -ne 0) { throw "npm run build failed" }
}

Write-Host ">> packing the working copy (without vendor/node_modules/.git/.env)..." -ForegroundColor Yellow
tar -czf "$tar" -C "$proj" `
    --exclude='./.git' --exclude='./node_modules' --exclude='./vendor' `
    --exclude='./.env' --exclude='./.env.*' `
    --exclude='./storage/logs' `
    --exclude='./storage/framework/cache' `
    --exclude='./storage/framework/sessions' `
    --exclude='./storage/framework/views' `
    --exclude='./storage/app' `
    --exclude='./bootstrap/cache/*.php' `
    --exclude='./public/hot' --exclude='./public/storage' `
    --exclude='./.phpunit.result.cache' --exclude='./.phpunit.cache' `
    .
if ($LASTEXITCODE -ne 0) { throw "tar failed" }
$mb = [math]::Round((Get-Item $tar).Length / 1MB, 2)
Write-Host "   package: $mb MB" -ForegroundColor DarkGray

Write-Host ">> uploading..." -ForegroundColor Yellow
& scp @sshOpts "$tar" "${server}:/tmp/rezervuj-deploy.tar.gz"
if ($LASTEXITCODE -ne 0) { throw "scp failed" }

Write-Host ">> extracting + server-side deploy..." -ForegroundColor Yellow
$remoteCmd = "mkdir -p $remoteApp && tar xzf /tmp/rezervuj-deploy.tar.gz -C $remoteApp && rm -f /tmp/rezervuj-deploy.tar.gz && "
if ($Bootstrap) { $remoteCmd += "bash $remoteApp/deploy/bootstrap-rezervuj.sh '$PublicUrl'" }
else            { $remoteCmd += "bash /opt/webstack/deploy-rezervuj.sh" }
& ssh @sshOpts $server $remoteCmd
$rc = $LASTEXITCODE

Remove-Item $tar -ErrorAction SilentlyContinue

if ($rc -eq 0) {
    Write-Host ""
    Write-Host "DONE. The site runs at:" -ForegroundColor Green
    Write-Host "  LAN:    http://192.168.100.50:8081" -ForegroundColor Green
    Write-Host "  public: after the Cloudflare Tunnel hostname points at localhost:8081" -ForegroundColor DarkGray
} else {
    Write-Host "DEPLOY FAILED (rc=$rc). See: ssh $server 'podman logs --tail 40 web-rezervuj'" -ForegroundColor Red
    exit $rc
}
