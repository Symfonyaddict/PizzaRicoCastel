#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${APP_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)}"
REMOTE_HOST="${REMOTE_HOST:-}"
REMOTE_USER="${REMOTE_USER:-}"
REMOTE_DIR="${REMOTE_DIR:-/var/www/pizzarico}"
ROLLBACK_TO="${ROLLBACK_TO:-HEAD~1}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
CONSOLE_BIN="${APP_DIR}/bin/console"

log() { printf '[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$*"; }

log "Rollback idempotent vers ${ROLLBACK_TO}..."

if [[ -n "${REMOTE_HOST}" && -n "${REMOTE_USER}" ]]; then
  log "Rollback SSH sur ${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_DIR}"
  ssh -o BatchMode=yes "${REMOTE_USER}@${REMOTE_HOST}" \
    "set -euo pipefail
     cd \"${REMOTE_DIR}\"
     git checkout --force \"${ROLLBACK_TO}\"
     ${COMPOSER_BIN} install --no-dev --optimize-autoloader --no-interaction --no-progress
     ${PHP_BIN} bin/console cache:clear --env=prod --no-warmup
     ${PHP_BIN} bin/console cache:warmup --env=prod
     chown -R www-data:www-data var public/images public/media || true"
  exit 0
fi

if command -v git >/dev/null 2>&1; then
  git checkout --force "${ROLLBACK_TO}"
fi

"${COMPOSER_BIN}" install --no-dev --optimize-autoloader --no-interaction --no-progress
"${PHP_BIN}" "${CONSOLE_BIN}" cache:clear --env=prod --no-warmup
"${PHP_BIN}" "${CONSOLE_BIN}" cache:warmup --env=prod

chown -R www-data:www-data "${APP_DIR}/var" "${APP_DIR}/public/images" "${APP_DIR}/public/media" || true

log "Rollback terminé."
