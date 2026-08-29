#!/usr/bin/env bash
# Levanta el nodo MinIO usado por backend_petfinder con la configuración de
# servidor (webhook incluido) inyectada por variables de entorno.
# Uso:
#   MINIO_ROOT_PASSWORD=... WEBHOOK_TOKEN=... ./run.sh
set -euo pipefail

# Evita que Git Bash (Windows) reescriba rutas tipo /data, /tmp/... antes de
# pasarlas a docker. Inofensivo en Linux/macOS.
export MSYS_NO_PATHCONV=1

MINIO_ROOT_PASSWORD="${MINIO_ROOT_PASSWORD:?Define MINIO_ROOT_PASSWORD}"
WEBHOOK_TOKEN="${WEBHOOK_TOKEN:?Define WEBHOOK_TOKEN}"
WEBHOOK_ENDPOINT="${WEBHOOK_ENDPOINT:-http://host.docker.internal:8000/api/webhooks/minio}"

if docker ps -a --format '{{.Names}}' | grep -qx "minio"; then
  echo "Ya existe un contenedor llamado 'minio'. Si es de un intento anterior y quieres" >&2
  echo "recrearlo (los datos persisten en el volumen 'minio_data'), corre primero:" >&2
  echo "  docker rm -f minio" >&2
  exit 1
fi

docker run -d \
  --name minio \
  --restart unless-stopped \
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
