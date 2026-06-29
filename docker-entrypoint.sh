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
php bin/console doctrine:schema:update --force --no-interaction

echo "📁 Correction des permissions..."
# On s'assure que l'utilisateur www-data (Apache) peut écrire dans var/ et public/images/
chown -R www-data:www-data var public/images

echo "🚀 Lancement d'Apache..."
exec apache2-foreground
