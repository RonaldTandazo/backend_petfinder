#!/usr/bin/env bash

# Aprovisiona MinIO:
# - buckets
# - lifecycle
# - versionado
# - política pública
# - usuario de aplicación
# - webhook events

set -euo pipefail

export MSYS_NO_PATHCONV=1

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

NETWORK_NAME="${NETWORK_NAME:-petfinder_network}"

MINIO_HOST="${MINIO_HOST:-http://minio:9000}"
MINIO_ROOT_USER="${MINIO_ROOT_USER:-minioadmin}"

MINIO_ROOT_PASSWORD="${MINIO_ROOT_PASSWORD:?Define MINIO_ROOT_PASSWORD}"
APP_ACCESS_KEY="${APP_ACCESS_KEY:?Define APP_ACCESS_KEY}"
APP_SECRET_KEY="${APP_SECRET_KEY:?Define APP_SECRET_KEY}"

TMP_BUCKET="petfinder-tmp"
PICTURES_BUCKET="petfinder-pictures"
POLICY_NAME="petfinder-app-policy"

echo "==> Configurando MinIO en $MINIO_HOST"

mc() {
    docker run --rm \
        --network "$NETWORK_NAME" \
        -v mc_config:/root/.mc \
        minio/mc "$@"
}

echo "==> Configurando alias MinIO"

mc alias set \
    minio \
    "$MINIO_HOST" \
    "$MINIO_ROOT_USER" \
    "$MINIO_ROOT_PASSWORD"

echo "==> Creando buckets"

mc mb --ignore-existing "minio/$TMP_BUCKET"
mc mb --ignore-existing "minio/$PICTURES_BUCKET"

echo "==> Configurando lifecycle del bucket temporal"

mc ilm rule add \
    --expire-days 2 \
    "minio/$TMP_BUCKET"

echo "==> Configurando versionado del bucket definitivo"

mc version enable \
    "minio/$PICTURES_BUCKET"

echo "==> Configurando expiración de versiones antiguas"

mc ilm rule add \
    --noncurrent-expire-days 30 \
    "minio/$PICTURES_BUCKET"

echo "==> Configurando lectura pública"

mc anonymous set download \
    "minio/$PICTURES_BUCKET"

echo "==> Creando política de aplicación"

POLICY_CONTAINER="minio-mc-policy-$$"

docker create \
    --name "$POLICY_CONTAINER" \
    --network "$NETWORK_NAME" \
    -v mc_config:/root/.mc \
    minio/mc \
    sleep infinity

docker cp \
    "$SCRIPT_DIR/app-policy.json" \
    "$POLICY_CONTAINER:/tmp/app-policy.json"

docker start "$POLICY_CONTAINER" >/dev/null

docker exec "$POLICY_CONTAINER" \
    mc admin policy create \
    minio \
    "$POLICY_NAME" \
    /tmp/app-policy.json

docker rm -f "$POLICY_CONTAINER" >/dev/null

echo "==> Creando usuario de aplicación"

mc admin user add \
    minio \
    "$APP_ACCESS_KEY" \
    "$APP_SECRET_KEY"

echo "==> Asignando política"

mc admin policy attach \
    minio \
    "$POLICY_NAME" \
    --user "$APP_ACCESS_KEY"

# ============================================================
# Eventos / Webhook
# ============================================================

echo "==> Limpiando eventos existentes"

mc event remove "minio/$TMP_BUCKET" --force || true
mc event remove "minio/$PICTURES_BUCKET" --force || true

echo "==> Configurando eventos del bucket temporal"

mc event add \
    "minio/$TMP_BUCKET" \
    arn:minio:sqs::PETFINDER:webhook \
    --event put,delete

echo "==> Configurando eventos del bucket definitivo"

mc event add \
    "minio/$PICTURES_BUCKET" \
    arn:minio:sqs::PETFINDER:webhook \
    --event put

echo
echo "Listo. Buckets, ciclo de vida, política pública, usuario de aplicación y eventos configurados."