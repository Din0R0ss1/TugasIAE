#!/bin/bash

set -e

echo "============================================"
echo "  Starting Perpustakaan Microservices"
echo "============================================"
echo ""

# Check Docker
if ! command -v docker &> /dev/null; then
    echo "[ERROR] Docker is not installed. Please install Docker first."
    exit 1
fi

if ! docker info &> /dev/null; then
    echo "[ERROR] Docker is not running. Please start Docker Desktop first."
    exit 1
fi

# Generate APP_KEY if .env.docker doesn't exist
if [ ! -f .env.docker ]; then
    echo "[*] Generating APP_KEY..."
    APP_KEY="base64:$(openssl rand -base64 32)"
    echo "APP_KEY=$APP_KEY" > .env.docker
    echo "[OK] APP_KEY generated and saved to .env.docker"
else
    echo "[OK] .env.docker already exists, using existing APP_KEY"
fi

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

# Create shared network if not exists
echo ""
echo "[*] Creating perpus-network..."
docker network create perpus-network --driver bridge 2>/dev/null || true
echo "[OK] Network ready"

# 1. RabbitMQ
echo ""
echo "[1/6] Starting RabbitMQ..."
docker compose -f "$SCRIPT_DIR/rabbitmq/docker-compose.yml" --env-file "$SCRIPT_DIR/.env.docker" up -d
echo "[OK] RabbitMQ started"

# 2. Book Service (PostgreSQL + worker)
echo ""
echo "[2/6] Starting Book Service (PostgreSQL + queue worker)..."
docker compose -f "$SCRIPT_DIR/book-service/docker-compose.yml" --env-file "$SCRIPT_DIR/.env.docker" up -d --build
echo "[OK] Book Service started"

# 3. Hasura
echo ""
echo "[3/6] Starting Hasura GraphQL Engine..."
docker compose -f "$SCRIPT_DIR/hasura/docker-compose.yml" --env-file "$SCRIPT_DIR/.env.docker" up -d
echo "[OK] Hasura started"

# 4. User Service
echo ""
echo "[4/6] Starting User Service..."
docker compose -f "$SCRIPT_DIR/user-service/docker-compose.yml" --env-file "$SCRIPT_DIR/.env.docker" up -d --build
echo "[OK] User Service started"

# 5. Loan Service
echo ""
echo "[5/6] Starting Loan Service..."
docker compose -f "$SCRIPT_DIR/loan-service/docker-compose.yml" --env-file "$SCRIPT_DIR/.env.docker" up -d --build
echo "[OK] Loan Service started"

# 6. Gateway
echo ""
echo "[6/6] Starting Gateway..."
docker compose -f "$SCRIPT_DIR/gateway-perpus/docker-compose.yml" --env-file "$SCRIPT_DIR/.env.docker" up -d --build
echo "[OK] Gateway started"

echo ""
echo "============================================"
echo "  All services started successfully!"
echo "============================================"
echo ""
echo "  Gateway       : http://localhost:8000"
echo "  User Service  : http://localhost:8001"
echo "  Hasura GraphQL: http://localhost:8080"
echo "  Hasura Console: http://localhost:8080/console"
echo "  Loan Service  : http://localhost:8003"
echo "  RabbitMQ UI   : http://localhost:15672"
echo ""
echo "  Run './stop-all.sh' to stop all services"
echo ""
