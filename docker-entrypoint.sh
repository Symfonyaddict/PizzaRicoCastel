#!/bin/sh
set -e

echo "🧹 Préparation du cache..."
php bin/console cache:clear --env=prod --no-warmup
php bin/console cache:warmup --env=prod

echo "📦 Installation des assets vendor (ImportMap)..."
php bin/console importmap:install --env=prod --no-interaction

echo "🎨 Compilation des assets..."
php bin/console asset-map:compile --env=prod --no-interaction

echo "🗄️ Mise à jour du schéma de la base de données (Direct)..."
# On utilise schema:update --force car les fichiers de migrations sont incompatibles avec PostgreSQL
php bin/console doctrine:schema:update --force --no-interaction

echo "🚀 Lancement d'Apache..."
exec apache2-foreground
