#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${APP_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)}"
REMOTE_HOST="${REMOTE_HOST:-}"
REMOTE_USER="${REMOTE_USER:-}"
REMOTE_DIR="${REMOTE_DIR:-/var/www/pizzarico}"
BRANCH="${BRANCH:-version-2}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
CONSOLE_BIN="${APP_DIR}/bin/console"

log() { printf '[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$*"; }

idempotent_mkdir() { local d="$1"; if [[ ! -d "${d}" ]]; then mkdir -p "${d}"; fi; }

log "Déploiement idempotent depuis la branche ${BRANCH}..."

if [[ -n "${REMOTE_HOST}" && -n "${REMOTE_USER}" ]]; then
  log "Déploiement SSH sur ${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_DIR}"
  ssh -o BatchMode=yes "${REMOTE_USER}@${REMOTE_HOST}" \
    "set -euo pipefail
     cd \"${REMOTE_DIR}\"
     mkdir -p var/cache var/log public/images public/media
     git fetch --all --prune
     git checkout --force \"${BRANCH}\"
     git reset --hard \"origin/${BRANCH}\"
     ${COMPOSER_BIN} install --no-dev --optimize-autoloader --no-interaction --no-progress
     ${PHP_BIN} bin/console cache:clear --env=prod --no-warmup
     ${PHP_BIN} bin/console cache:warmup --env=prod
     ${PHP_BIN} bin/console doctrine:migrations:migrate --env=prod --no-interaction --allow-no-migration
     ${PHP_BIN} bin/console importmap:install --env=prod --no-interaction || true
     ${PHP_BIN} bin/console asset-map:compile --env=prod --no-interaction || true
     chown -R www-data:www-data var public/images public/media || true"
  exit 0
fi

log "Mode local : mise à jour du dépôt..."
idempotent_mkdir "${APP_DIR}/var/cache"
idempotent_mkdir "${APP_DIR}/var/log"
idempotent_mkdir "${APP_DIR}/public/images"
idempotent_mkdir "${APP_DIR}/public/media"
if command -v git >/dev/null 2>&1; then
  git fetch --all --prune
  git checkout --force "${BRANCH}"
  git reset --hard "origin/${BRANCH}"
fi

log "Installation dépendances Composer..."
"${COMPOSER_BIN}" install --no-dev --optimize-autoloader --no-interaction --no-progress

log "Mise à jour cache Symfony..."
"${PHP_BIN}" "${CONSOLE_BIN}" cache:clear --env=prod --no-warmup
"${PHP_BIN}" "${CONSOLE_BIN}" cache:warmup --env=prod

log "Migrations Doctrine..."
"${PHP_BIN}" "${CONSOLE_BIN}" doctrine:migrations:migrate --env=prod --no-interaction --allow-no-migration

log "Assets ImportMap..."
"${PHP_BIN}" "${CONSOLE_BIN}" importmap:install --env=prod --no-interaction || true
"${PHP_BIN}" "${CONSOLE_BIN}" asset-map:compile --env=prod --no-interaction || true

log "Permissions..."
chown -R www-data:www-data "${APP_DIR}/var" "${APP_DIR}/public/images" "${APP_DIR}/public/media" || true

log "Déploiement terminé."
