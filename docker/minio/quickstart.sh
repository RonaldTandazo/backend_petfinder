#!/usr/bin/env bash
# Deja MinIO listo para backend_petfinder de punta a punta en un solo comando:
# genera secretos propios, levanta el nodo, aprovisiona buckets/policías/webhook,
# y si encuentra el contenedor de la app corriendo, le aplica las variables.
#
# Uso (desde la raíz del repo o desde docker/minio/, da igual):
#   ./docker/minio/quickstart.sh
#
# Variables opcionales:
#   APP_CONTAINER=backend_petfinder   nombre del contenedor de la app (default: backend_petfinder)
#   WEBHOOK_ENDPOINT=...              a dónde le pega MinIO (default: http://host.docker.internal:8000/api/webhooks/minio)
set -euo pipefail

# Evita que Git Bash (Windows) reescriba rutas POSIX antes de pasarlas a
# docker (afecta a los tres scripts que este orquesta). Inofensivo en Linux/macOS.
export MSYS_NO_PATHCONV=1

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

command -v docker >/dev/null || { echo "Docker no está disponible en el PATH." >&2; exit 1; }
command -v openssl >/dev/null || { echo "openssl no está disponible (se usa para generar secretos)." >&2; exit 1; }

APP_CONTAINER="${APP_CONTAINER:-backend_petfinder}"

echo "==> Generando secretos nuevos (propios de este ambiente, no reuses los de otra persona)"
MINIO_ROOT_PASSWORD="$(openssl rand -base64 24 | tr -d '/+=' | cut -c1-24)"
APP_ACCESS_KEY="petfinder-app-$(openssl rand -hex 4)"
APP_SECRET_KEY="$(openssl rand -base64 32 | tr -d '/+=' | cut -c1-32)"
MINIO_WEBHOOK_TOKEN="$(openssl rand -hex 24)"

echo "==> Paso 1/3: levantando el nodo MinIO (run.sh)"
MINIO_ROOT_PASSWORD="$MINIO_ROOT_PASSWORD" \
WEBHOOK_TOKEN="$MINIO_WEBHOOK_TOKEN" \
  ./run.sh

echo "==> Esperando a que MinIO responda..."
for i in $(seq 1 15); do
  if docker run --rm -v mc_config:/root/.mc minio/mc alias set minio http://host.docker.internal:9000 minioadmin "$MINIO_ROOT_PASSWORD" >/dev/null 2>&1; then
    break
  fi
  sleep 2
  [ "$i" -eq 15 ] && { echo "MinIO no respondió a tiempo." >&2; exit 1; }
done

echo "==> Paso 2/3: aprovisionando buckets, ciclo de vida, política y webhook (setup.sh)"
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
    ./configure-app-env.sh
else
  echo "Aviso: no encontré un contenedor corriendo llamado '$APP_CONTAINER'."
  echo "Cuando lo tengas levantado, corre a mano:"
  echo "  APP_CONTAINER=$APP_CONTAINER APP_ACCESS_KEY=$APP_ACCESS_KEY APP_SECRET_KEY=$APP_SECRET_KEY MINIO_WEBHOOK_TOKEN=$MINIO_WEBHOOK_TOKEN ./configure-app-env.sh"
fi

cat <<EOF

================================================================
Listo. Guarda estos valores en un gestor de secretos (no quedan
guardados en ningún archivo del repo):

  MINIO_ROOT_USER=minioadmin
  MINIO_ROOT_PASSWORD=$MINIO_ROOT_PASSWORD
  APP_ACCESS_KEY=$APP_ACCESS_KEY
  APP_SECRET_KEY=$APP_SECRET_KEY
  MINIO_WEBHOOK_TOKEN=$MINIO_WEBHOOK_TOKEN

Consola web:  http://localhost:9001  (login con las credenciales root de arriba)
API S3:       http://localhost:9000
================================================================
EOF
