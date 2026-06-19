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

echo ""
echo "[*] Building and starting all services..."
docker compose --env-file .env.docker up --build -d

echo ""
echo "============================================"
echo "  All services started successfully!"
echo "============================================"
echo ""
echo "  Gateway      : http://localhost:8000"
echo "  User Service : http://localhost:8001"
echo "  Book Service : http://localhost:8002"
echo "  Loan Service : http://localhost:8003"
echo "  RabbitMQ UI  : http://localhost:15672"
echo ""
echo "  Run './stop-all.sh' to stop all services"
echo ""
