#!/bin/sh
set -e

echo "🧹 Préparation du cache..."
php bin/console cache:clear --env=prod --no-warmup
php bin/console cache:warmup --env=prod

echo "📦 Installation des assets vendor (ImportMap)..."
php bin/console importmap:install --env=prod --no-interaction

echo "🎨 Compilation des assets..."
php bin/console asset-map:compile --env=prod --no-interaction

echo "⏳ Exécution des migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

echo "🚀 Lancement d'Apache..."
exec apache2-foreground
