#!/bin/sh
set -e

# Correction des fins de ligne au cas où le fichier a été édité sous Windows
# sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh

echo "🎨 Compilation des assets..."
php bin/console asset-map:compile --env=prod --no-interaction

echo "⏳ Exécution des migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

echo "🧹 Nettoyage du cache..."
php bin/console cache:clear --env=prod

echo "🚀 Lancement d'Apache..."
exec apache2-foreground
