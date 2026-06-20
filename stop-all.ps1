Write-Host "============================================"
Write-Host "  Stopping Perpustakaan Microservices"
Write-Host "============================================"
Write-Host ""

$rootDir = $PSScriptRoot

# Stop in reverse order
Write-Host "[1/6] Stopping Gateway..."
docker compose -f "$rootDir\gateway-perpus\docker-compose.yml" down 2>$null

Write-Host "[2/6] Stopping Loan Service..."
docker compose -f "$rootDir\loan-service\docker-compose.yml" down 2>$null

Write-Host "[3/6] Stopping User Service..."
docker compose -f "$rootDir\user-service\docker-compose.yml" down 2>$null

Write-Host "[4/6] Stopping Hasura..."
docker compose -f "$rootDir\hasura\docker-compose.yml" down 2>$null

Write-Host "[5/6] Stopping Book Service..."
docker compose -f "$rootDir\book-service\docker-compose.yml" down 2>$null

Write-Host "[6/6] Stopping RabbitMQ..."
docker compose -f "$rootDir\rabbitmq\docker-compose.yml" down 2>$null

Write-Host ""
Write-Host "[OK] All services stopped."
Write-Host ""
