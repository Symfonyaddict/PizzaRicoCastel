#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${APP_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)}"
BACKUP_DIR="${BACKUP_DIR:-${APP_DIR}/var/backups}"
TIMESTAMP="$(date '+%Y%m%d_%H%M%S')"
DB_BACKUP_FILE="${BACKUP_DIR}/db_${TIMESTAMP}.sql"
APP_BACKUP_FILE="${BACKUP_DIR}/app_${TIMESTAMP}.tar.zst"
KEEP_DAYS="${KEEP_DAYS:-14}"
PGDATABASE="${PGDATABASE:-pizzarico}"
PGHOST="${PGHOST:-127.0.0.1}"
PGPORT="${PGPORT:-5432}"
PGUSER="${PGUSER:-app}"

log() { printf '[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$*"; }

idempotent_mkdir() { local d="$1"; if [[ ! -d "${d}" ]]; then mkdir -p "${d}"; fi; }

log "Sauvegarde idempotente..."
idempotent_mkdir "${BACKUP_DIR}"

log "Dump PostgreSQL : ${PGDATABASE} -> ${DB_BACKUP_FILE}"
if command -v pg_dump >/dev/null 2>&1; then
  PGDATABASE="${PGDATABASE}" PGHOST="${PGHOST}" PGPORT="${PGPORT}" PGUSER="${PGUSER}" \
    pg_dump --no-owner --no-acl --format=plain --file="${DB_BACKUP_FILE}"
  if command -v zstd >/dev/null 2>&1; then
    zstd -f --rm -q "${DB_BACKUP_FILE}"
  fi
else
  log "pg_dump absent, skip dump base."
fi

log "Archive application : ${APP_BACKUP_FILE}"
if command -v zstd >/dev/null 2>&1; then
  tar --zstd -cf "${APP_BACKUP_FILE}" \
    --exclude="./var/cache" \
    --exclude="./var/log" \
    --exclude="./vendor" \
    --exclude="./node_modules" \
    --exclude="./public/assets" \
    -C "${APP_DIR}" .
else
  tar -cJf "${APP_BACKUP_FILE%.zst}.xz" \
    --exclude="./var/cache" \
    --exclude="./var/log" \
    --exclude="./vendor" \
    --exclude="./node_modules" \
    --exclude="./public/assets" \
    -C "${APP_DIR}" .
fi

log "Purge sauvegardes > ${KEEP_DAYS} jours..."
find "${BACKUP_DIR}" -type f -mtime "+${KEEP_DAYS}" -delete || true

log "Sauvegarde terminée."
