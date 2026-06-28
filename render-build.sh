#!/usr/bin/env bash
# exit on error
set -o errexit

echo "🚀 Début du build sur Render..."

# Installation des dépendances
composer install --no-dev --optimize-autoloader

# Compilation des assets (AssetMapper)
echo "🎨 Compilation des assets..."
php bin/console asset-map:compile

# Nettoyage du cache
echo "🧹 Nettoyage du cache..."
php bin/console cache:clear --env=prod

# Migrations de base de données
# Note: On peut aussi le mettre ici, mais Render préfère parfois une commande séparée
echo "🗄️ Migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --env=prod

echo "✅ Build terminé !"
