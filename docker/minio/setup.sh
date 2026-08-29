#!/usr/bin/env bash
# Aprovisiona el nodo MinIO usado por backend_petfinder: buckets, ciclo de vida,
# política de lectura pública del bucket definitivo, usuario de aplicación con
# permisos mínimos y suscripción de eventos al webhook configurado a nivel de nodo
# (variables MINIO_NOTIFY_WEBHOOK_* pasadas al `docker run` del contenedor minio).
#
# Requiere: el contenedor `minio` corriendo y accesible en MINIO_HOST.
# Uso:
#   MINIO_ROOT_PASSWORD=... APP_ACCESS_KEY=... APP_SECRET_KEY=... ./setup.sh
set -euo pipefail

# En Git Bash (Windows) MSYS reescribe argumentos que parecen rutas POSIX antes
# de pasarlos a docker, incluyendo las rutas DENTRO del contenedor (ej. /tmp/...
# termina resuelto contra C:\...). Desactivarlo es inofensivo en Linux/macOS.
export MSYS_NO_PATHCONV=1

# Resuelve la ruta de app-policy.json contra la ubicación real de este script,
# no contra el directorio desde el que se invoque (evita que `$(pwd)` apunte
# a otro lado si lo corres como ./docker/minio/setup.sh desde la raíz del repo).
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

MINIO_HOST="${MINIO_HOST:-http://host.docker.internal:9000}"
MINIO_ROOT_USER="${MINIO_ROOT_USER:-minioadmin}"
MINIO_ROOT_PASSWORD="${MINIO_ROOT_PASSWORD:?Define MINIO_ROOT_PASSWORD}"
APP_ACCESS_KEY="${APP_ACCESS_KEY:?Define APP_ACCESS_KEY}"
APP_SECRET_KEY="${APP_SECRET_KEY:?Define APP_SECRET_KEY}"

TMP_BUCKET="petfinder-tmp"
PICTURES_BUCKET="petfinder-pictures"
POLICY_NAME="petfinder-app-policy"

mc() {
  docker run --rm -v mc_config:/root/.mc minio/mc "$@"
}

mc alias set minio "$MINIO_HOST" "$MINIO_ROOT_USER" "$MINIO_ROOT_PASSWORD"

mc mb --ignore-existing "minio/$TMP_BUCKET"
mc mb --ignore-existing "minio/$PICTURES_BUCKET"

# Bucket temporal: expira objetos a los 2 días, sin versionado (no aporta aquí).
mc ilm rule add --expire-days 2 "minio/$TMP_BUCKET"

# Bucket definitivo: versionado + expiración de versiones viejas para no crecer sin límite.
mc version enable "minio/$PICTURES_BUCKET"
mc ilm rule add --noncurrent-expire-days 30 "minio/$PICTURES_BUCKET"

# Lectura pública anónima (solo GetObject/ListBucket) — son fotos públicas de mascotas.
mc anonymous set download "minio/$PICTURES_BUCKET"

# Usuario de aplicación con permisos mínimos (nunca usar las credenciales root en Laravel).
docker run --rm -v mc_config:/root/.mc -v "$SCRIPT_DIR/app-policy.json:/tmp/app-policy.json" \
  minio/mc admin policy create minio "$POLICY_NAME" /tmp/app-policy.json
mc admin user add minio "$APP_ACCESS_KEY" "$APP_SECRET_KEY"
mc admin policy attach minio "$POLICY_NAME" --user "$APP_ACCESS_KEY"

# Notificaciones: el target `PETFINDER` ya existe a nivel de nodo (env MINIO_NOTIFY_WEBHOOK_*
# del `docker run` de minio). Aquí solo se suscriben los buckets a ese target.
mc event add "minio/$TMP_BUCKET" arn:minio:sqs::PETFINDER:webhook --event put,delete
mc event add "minio/$PICTURES_BUCKET" arn:minio:sqs::PETFINDER:webhook --event put

echo "Listo. Buckets, ciclo de vida, política pública, usuario de app y eventos configurados."
