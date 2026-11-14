#!/usr/bin/env bash

# Deja MinIO listo para backend_petfinder de punta a punta:
# genera secretos, levanta MinIO, configura buckets/políticas/webhook
# y aplica las variables al contenedor Laravel.
#
# Uso:
#   ./docker/minio/quickstart.sh
#
# Variables opcionales:
#   APP_CONTAINER=backend_petfinder
#   NETWORK_NAME=petfinder_network
#   WEBHOOK_ENDPOINT=http://backend_petfinder:80/api/webhooks/minio

set -euo pipefail

export MSYS_NO_PATHCONV=1

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

command -v docker >/dev/null || {
    echo "Docker no está disponible en el PATH." >&2
    exit 1
}

command -v openssl >/dev/null || {
    echo "openssl no está disponible." >&2
    exit 1
}

APP_CONTAINER="${APP_CONTAINER:-backend_petfinder}"
NETWORK_NAME="${NETWORK_NAME:-petfinder_network}"
WEBHOOK_ENDPOINT="${WEBHOOK_ENDPOINT:-http://backend-petfinder:80/api/webhooks/minio}"

echo "==> Verificando network '$NETWORK_NAME'"

if ! docker network inspect "$NETWORK_NAME" >/dev/null 2>&1; then
    echo "    Network no existe. Creándola..."
    docker network create "$NETWORK_NAME"
else
    echo "    Network ya existe."
fi

echo "==> Generando secretos nuevos"

MINIO_ROOT_PASSWORD="$(openssl rand -base64 24 | tr -d '/+=' | cut -c1-24)"
APP_ACCESS_KEY="petfinder-app-$(openssl rand -hex 4)"
APP_SECRET_KEY="$(openssl rand -base64 32 | tr -d '/+=' | cut -c1-32)"
MINIO_WEBHOOK_TOKEN="$(openssl rand -hex 24)"

echo "==> Verificando contenedor de la aplicación"

if docker ps --format '{{.Names}}' | grep -qx "$APP_CONTAINER"; then

    if ! docker network inspect "$NETWORK_NAME" \
        --format '{{range .Containers}}{{.Name}}{{"\n"}}{{end}}' \
        | grep -qx "$APP_CONTAINER"; then

        echo "    Conectando '$APP_CONTAINER' a '$NETWORK_NAME'..."

        docker network connect \
            --alias backend-petfinder \
            "$NETWORK_NAME" \
            "$APP_CONTAINER"

    else
        echo "    '$APP_CONTAINER' ya está conectado a '$NETWORK_NAME'."

        # Aseguramos que tenga el alias válido.
        docker network disconnect "$NETWORK_NAME" "$APP_CONTAINER"

        docker network connect \
            --alias backend-petfinder \
            "$NETWORK_NAME" \
            "$APP_CONTAINER"
    fi

else
    echo "Aviso: no encontré un contenedor corriendo llamado '$APP_CONTAINER'."
fi

echo "==> Paso 1/3: levantando MinIO"

NETWORK_NAME="$NETWORK_NAME" \
WEBHOOK_ENDPOINT="$WEBHOOK_ENDPOINT" \
MINIO_ROOT_PASSWORD="$MINIO_ROOT_PASSWORD" \
WEBHOOK_TOKEN="$MINIO_WEBHOOK_TOKEN" \
    ./run.sh

echo "==> Esperando a que MinIO responda..."

for i in $(seq 1 30); do

    if docker run --rm \
        --network "$NETWORK_NAME" \
        -v mc_config:/root/.mc \
        minio/mc \
        alias set minio \
        http://minio:9000 \
        minioadmin \
        "$MINIO_ROOT_PASSWORD" \
        >/dev/null 2>&1
    then
        echo "    MinIO está disponible."
        break
    fi

    echo "    Intento $i/30..."
    sleep 2

    if [ "$i" -eq 30 ]; then
        echo "MinIO no respondió a tiempo." >&2
        echo
        echo "Logs de MinIO:"
        docker logs minio
        exit 1
    fi
done

echo "==> Paso 2/3: aprovisionando MinIO"

NETWORK_NAME="$NETWORK_NAME" \
MINIO_HOST="http://minio:9000" \
MINIO_ROOT_PASSWORD="$MINIO_ROOT_PASSWORD" \
APP_ACCESS_KEY="$APP_ACCESS_KEY" \
APP_SECRET_KEY="$APP_SECRET_KEY" \
    ./setup.sh

echo "==> Paso 3/3: aplicando variables al contenedor de la app"

if docker ps --format '{{.Names}}' | grep -qx "$APP_CONTAINER"; then

    APP_CONTAINER="$APP_CONTAINER" \
    APP_ACCESS_KEY="$APP_ACCESS_KEY" \
    APP_SECRET_KEY="$APP_SECRET_KEY" \
    MINIO_WEBHOOK_TOKEN="$MINIO_WEBHOOK_TOKEN" \
    AWS_ENDPOINT="http://minio:9000" \
        ./configure-app-env.sh

else

    echo "Aviso: no encontré un contenedor corriendo llamado '$APP_CONTAINER'."
    echo
    echo "Cuando esté levantado, ejecuta:"
    echo
    echo "APP_CONTAINER=$APP_CONTAINER \\"
    echo "APP_ACCESS_KEY=$APP_ACCESS_KEY \\"
    echo "APP_SECRET_KEY=$APP_SECRET_KEY \\"
    echo "MINIO_WEBHOOK_TOKEN=$MINIO_WEBHOOK_TOKEN \\"
    echo "AWS_ENDPOINT=http://minio:9000 \\"
    echo "./configure-app-env.sh"

fi

cat <<EOF

================================================================
MINIO CONFIGURADO CORRECTAMENTE
================================================================

Guarda estos valores en un gestor de secretos:

  MINIO_ROOT_USER=minioadmin
  MINIO_ROOT_PASSWORD=$MINIO_ROOT_PASSWORD

  APP_ACCESS_KEY=$APP_ACCESS_KEY
  APP_SECRET_KEY=$APP_SECRET_KEY

  MINIO_WEBHOOK_TOKEN=$MINIO_WEBHOOK_TOKEN

URLs:

  Consola web: http://localhost:9001
  API S3:      http://localhost:9000

Dentro de Docker:

  MinIO:      http://minio:9000
  Backend:    http://backend_petfinder:80

================================================================
EOF