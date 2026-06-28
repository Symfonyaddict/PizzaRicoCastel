#!/bin/bash

# Script de déploiement pour Pizza Rico
# Usage: ./deploy.sh

echo "🚀 Début du déploiement..."

# 1. Mettre le site en mode maintenance (si vous avez un bundle ou un fichier .html dédié)
# bin/console lexik:maintenance:lock

# 2. Installation des dépendances Composer sans les outils de dev
echo "📦 Installation des dépendances..."
composer install --no-dev --optimize-autoloader

# 3. Compilation des variables d'environnement pour la prod
echo "🔐 Génération de .env.local.php..."
composer dump-env prod

# 4. Nettoyage et préchauffage du cache
echo "🧹 Nettoyage du cache..."
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

# 5. Exécution des migrations de base de données
echo "🗄️ Exécution des migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --env=prod

# 6. Compilation des assets (AssetMapper)
echo "🎨 Compilation des assets..."
php bin/console asset-map:compile

# 7. Droits d'écriture sur les dossiers de médias (VichUploader)
echo "📁 Gestion des permissions..."
chmod -R 775 public/images/
# chown -R www-data:www-data public/images/ var/ # À adapter selon votre serveur

# 8. Sortie du mode maintenance
# bin/console lexik:maintenance:unlock

echo "✅ Déploiement terminé avec succès !"
