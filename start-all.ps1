Write-Host "============================================"
Write-Host "  Starting Perpustakaan Microservices"
Write-Host "============================================"
Write-Host ""

# Check Docker
try {
    $null = docker info 2>&1
    if ($LASTEXITCODE -ne 0) { throw "Docker not running" }
} catch {
    Write-Host "[ERROR] Docker is not running. Please start Docker Desktop first." -ForegroundColor Red
    exit 1
}

# Generate APP_KEY if .env.docker doesn't exist
$envFile = Join-Path $PSScriptRoot ".env.docker"
if (-not (Test-Path $envFile)) {
    Write-Host "[*] Generating APP_KEY..."
    $bytes = New-Object byte[] 32
    [System.Security.Cryptography.RNGCryptoServiceProvider]::Create().GetBytes($bytes)
    $appKey = "base64:" + [Convert]::ToBase64String($bytes)
    Set-Content -Path $envFile -Value "APP_KEY=$appKey"
    Write-Host "[OK] APP_KEY generated and saved to .env.docker"
} else {
    Write-Host "[OK] .env.docker already exists, using existing APP_KEY"
}

Write-Host ""
Write-Host "[*] Building and starting all services..."
docker compose --env-file $envFile up --build -d

Write-Host ""
Write-Host "============================================"
Write-Host "  All services started successfully!"
Write-Host "============================================"
Write-Host ""
Write-Host "  Gateway      : http://localhost:8000"
Write-Host "  User Service : http://localhost:8001"
Write-Host "  Book Service : http://localhost:8002"
Write-Host "  Loan Service : http://localhost:8003"
Write-Host "  RabbitMQ UI  : http://localhost:15672"
Write-Host ""
Write-Host "  Run '.\stop-all.ps1' to stop all services"
Write-Host ""
