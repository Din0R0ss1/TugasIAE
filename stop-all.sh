#!/bin/bash

echo "============================================"
echo "  Stopping Perpustakaan Microservices"
echo "============================================"
echo ""

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

# Stop in reverse order
echo "[1/6] Stopping Gateway..."
docker compose -f "$SCRIPT_DIR/gateway-perpus/docker-compose.yml" down 2>/dev/null

echo "[2/6] Stopping Loan Service..."
docker compose -f "$SCRIPT_DIR/loan-service/docker-compose.yml" down 2>/dev/null

echo "[3/6] Stopping User Service..."
docker compose -f "$SCRIPT_DIR/user-service/docker-compose.yml" down 2>/dev/null

echo "[4/6] Stopping Hasura..."
docker compose -f "$SCRIPT_DIR/hasura/docker-compose.yml" down 2>/dev/null

echo "[5/6] Stopping Book Service..."
docker compose -f "$SCRIPT_DIR/book-service/docker-compose.yml" down 2>/dev/null

echo "[6/6] Stopping RabbitMQ..."
docker compose -f "$SCRIPT_DIR/rabbitmq/docker-compose.yml" down 2>/dev/null

echo ""
echo "[OK] All services stopped."
echo ""
