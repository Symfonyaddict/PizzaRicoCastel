#!/bin/sh
set -e

echo "⏳ Attente de la base de données et exécution des migrations..."
# On lance les migrations automatiquement
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

echo "🚀 Lancement d'Apache..."
exec apache2-foreground
