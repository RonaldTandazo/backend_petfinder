#!/usr/bin/env bash

# Configura las variables de MinIO dentro del contenedor Laravel.

set -euo pipefail

export MSYS_NO_PATHCONV=1

APP_CONTAINER="${APP_CONTAINER:-backend_petfinder}"

APP_ACCESS_KEY="${APP_ACCESS_KEY:?Define APP_ACCESS_KEY}"
APP_SECRET_KEY="${APP_SECRET_KEY:?Define APP_SECRET_KEY}"
MINIO_WEBHOOK_TOKEN="${MINIO_WEBHOOK_TOKEN:?Define MINIO_WEBHOOK_TOKEN}"

AWS_BUCKET="${AWS_BUCKET:-petfinder-pictures}"
AWS_ENDPOINT="${AWS_ENDPOINT:-http://minio:9000}"

ENV_PATH="${ENV_PATH:-/var/www/.env}"

set_env_var() {

    local key="$1"
    local value="$2"

    docker exec "$APP_CONTAINER" sh -c "
        if grep -q '^${key}=' '$ENV_PATH'; then
            sed -i 's|^${key}=.*|${key}=${value}|' '$ENV_PATH'
        else
            echo '${key}=${value}' >> '$ENV_PATH'
        fi
    "
}

set_env_var "FILESYSTEM_DISK" "s3"
set_env_var "AWS_ACCESS_KEY_ID" "$APP_ACCESS_KEY"
set_env_var "AWS_SECRET_ACCESS_KEY" "$APP_SECRET_KEY"
set_env_var "AWS_DEFAULT_REGION" "us-east-1"
set_env_var "AWS_BUCKET" "$AWS_BUCKET"
set_env_var "AWS_ENDPOINT" "$AWS_ENDPOINT"
set_env_var "AWS_USE_PATH_STYLE_ENDPOINT" "true"
set_env_var "MINIO_WEBHOOK_TOKEN" "$MINIO_WEBHOOK_TOKEN"

echo "Variables de MinIO aplicadas al .env de $APP_CONTAINER."
echo "AWS_ENDPOINT=$AWS_ENDPOINT"