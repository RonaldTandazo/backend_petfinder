#!/usr/bin/env bash
# Escribe/actualiza en el .env de un contenedor Laravel corriendo las variables
# necesarias para hablar con este MinIO (credenciales del usuario de aplicación,
# no las root, y el token del webhook). Pensado para backend_petfinder, que no
# monta el .env como volumen (vive horneado en la imagen) — por eso se edita
# en caliente con `docker exec`. Como no hay cache de config, el cambio aplica
# de inmediato, sin reiniciar el contenedor.
#
# Uso:
#   APP_CONTAINER=backend_petfinder \
#   APP_ACCESS_KEY=... APP_SECRET_KEY=... MINIO_WEBHOOK_TOKEN=... \
#   ./configure-app-env.sh
set -euo pipefail

# Evita que Git Bash (Windows) reescriba rutas tipo /var/www/.env antes de
# pasarlas a docker exec. Inofensivo en Linux/macOS.
export MSYS_NO_PATHCONV=1

APP_CONTAINER="${APP_CONTAINER:-backend_petfinder}"
APP_ACCESS_KEY="${APP_ACCESS_KEY:?Define APP_ACCESS_KEY}"
APP_SECRET_KEY="${APP_SECRET_KEY:?Define APP_SECRET_KEY}"
MINIO_WEBHOOK_TOKEN="${MINIO_WEBHOOK_TOKEN:?Define MINIO_WEBHOOK_TOKEN}"
AWS_BUCKET="${AWS_BUCKET:-petfinder-pictures}"
AWS_ENDPOINT="${AWS_ENDPOINT:-http://host.docker.internal:9000}"
ENV_PATH="${ENV_PATH:-/var/www/.env}"

set_env_var() {
  local key="$1" value="$2"
  docker exec "$APP_CONTAINER" sh -c "
    grep -q '^${key}=' '$ENV_PATH' && \
      sed -i 's|^${key}=.*|${key}=${value}|' '$ENV_PATH' || \
      echo '${key}=${value}' >> '$ENV_PATH'
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
