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
$rootDir = $PSScriptRoot
$envFile = Join-Path $rootDir ".env.docker"
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

# Create shared network if not exists
Write-Host ""
Write-Host "[*] Creating perpus-network..."
docker network create perpus-network --driver bridge 2>$null
Write-Host "[OK] Network ready"

# 1. RabbitMQ
Write-Host ""
Write-Host "[1/6] Starting RabbitMQ..."
docker compose -f "$rootDir\rabbitmq\docker-compose.yml" --env-file $envFile up -d
Write-Host "[OK] RabbitMQ started"

# 2. Book Service (PostgreSQL + worker)
Write-Host ""
Write-Host "[2/6] Starting Book Service (PostgreSQL + queue worker)..."
docker compose -f "$rootDir\book-service\docker-compose.yml" --env-file $envFile up -d --build
Write-Host "[OK] Book Service started"

# 3. Hasura
Write-Host ""
Write-Host "[3/6] Starting Hasura GraphQL Engine..."
docker compose -f "$rootDir\hasura\docker-compose.yml" --env-file $envFile up -d
Write-Host "[OK] Hasura started"

# 4. User Service
Write-Host ""
Write-Host "[4/6] Starting User Service..."
docker compose -f "$rootDir\user-service\docker-compose.yml" --env-file $envFile up -d --build
Write-Host "[OK] User Service started"

# 5. Loan Service
Write-Host ""
Write-Host "[5/6] Starting Loan Service..."
docker compose -f "$rootDir\loan-service\docker-compose.yml" --env-file $envFile up -d --build
Write-Host "[OK] Loan Service started"

# 6. Gateway
Write-Host ""
Write-Host "[6/6] Starting Gateway..."
docker compose -f "$rootDir\gateway-perpus\docker-compose.yml" --env-file $envFile up -d --build
Write-Host "[OK] Gateway started"

Write-Host ""
Write-Host "============================================"
Write-Host "  All services started successfully!"
Write-Host "============================================"
Write-Host ""
Write-Host "  Gateway       : http://localhost:8000"
Write-Host "  User Service  : http://localhost:8001"
Write-Host "  Hasura GraphQL: http://localhost:8080"
Write-Host "  Hasura Console: http://localhost:8080/console"
Write-Host "  Loan Service  : http://localhost:8003"
Write-Host "  RabbitMQ UI   : http://localhost:15672"
Write-Host ""
Write-Host "  Run '.\stop-all.ps1' to stop all services"
Write-Host ""
