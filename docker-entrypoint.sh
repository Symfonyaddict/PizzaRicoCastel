#!/bin/sh
set -e

# Vérification de la présence de DATABASE_URL pour éviter un crash silencieux
if [ -z "$DATABASE_URL" ]; then
    echo "❌ Erreur : La variable d'environnement DATABASE_URL n'est pas définie."
    echo "Veuillez la configurer dans le tableau de bord Render."
    # On ne quitte pas forcément ici car Symfony peut avoir une valeur par défaut dans .env
    # mais on prévient l'utilisateur.
fi

# Correction du préfixe postgres:// en postgresql:// pour Render
if [ -n "$DATABASE_URL" ]; then
    # On remplace postgres:// par postgresql:// si nécessaire
    case "$DATABASE_URL" in
        postgres://*)
            export DATABASE_URL="postgresql://${DATABASE_URL#postgres://}"
            echo "🔧 DATABASE_URL corrigée (préfixe postgresql:// ajouté)"
            ;;
    esac
fi

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
