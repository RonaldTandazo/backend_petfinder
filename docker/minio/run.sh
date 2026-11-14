#!/usr/bin/env bash

# Levanta el nodo MinIO usado por backend_petfinder.

set -euo pipefail

export MSYS_NO_PATHCONV=1

MINIO_ROOT_PASSWORD="${MINIO_ROOT_PASSWORD:?Define MINIO_ROOT_PASSWORD}"
WEBHOOK_TOKEN="${WEBHOOK_TOKEN:?Define WEBHOOK_TOKEN}"

NETWORK_NAME="${NETWORK_NAME:-petfinder_network}"
WEBHOOK_ENDPOINT="${WEBHOOK_ENDPOINT:-http://backend-petfinder:80/api/webhooks/minio}"

echo "==> Verificando network '$NETWORK_NAME'"

if ! docker network inspect "$NETWORK_NAME" >/dev/null 2>&1; then
    echo "    Creando network '$NETWORK_NAME'..."
    docker network create "$NETWORK_NAME"
fi

if docker ps -a --format '{{.Names}}' | grep -qx "minio"; then

    echo
    echo "Ya existe un contenedor llamado 'minio'."
    echo
    echo "Si es de un intento anterior y quieres recrearlo:"
    echo
    echo "  docker rm -f minio"
    echo
    echo "El volumen 'minio_data' NO se elimina."
    echo

    exit 1
fi

echo "==> Creando contenedor MinIO"

docker run -d \
    --name minio \
    --restart unless-stopped \
    --network "$NETWORK_NAME" \
    -p 9000:9000 \
    -p 9001:9001 \
    -e "MINIO_ROOT_USER=minioadmin" \
    -e "MINIO_ROOT_PASSWORD=$MINIO_ROOT_PASSWORD" \
    -e "MINIO_REGION_NAME=us-east-1" \
    -e "MINIO_BROWSER=on" \
    -e "MINIO_NOTIFY_WEBHOOK_ENABLE_PETFINDER=on" \
    -e "MINIO_NOTIFY_WEBHOOK_ENDPOINT_PETFINDER=$WEBHOOK_ENDPOINT" \
    -e "MINIO_NOTIFY_WEBHOOK_AUTH_TOKEN_PETFINDER=$WEBHOOK_TOKEN" \
    -v minio_data:/data \
    minio/minio server /data --console-address ":9001"