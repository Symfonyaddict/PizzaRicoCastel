# Utilisation d'une image PHP officielle avec Apache
FROM php:8.2-apache

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install intl pdo pdo_pgsql zip opcache

# Activation du module rewrite d'Apache (pour le .htaccess)
RUN a2enmod rewrite

# Configuration du DocumentRoot d'Apache vers le dossier public/ de Symfony
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copie des fichiers du projet
WORKDIR /var/www/html
COPY . .

# Installation des dépendances PHP
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader

# Droits sur les dossiers de cache et logs
RUN chown -R www-data:www-data var/ public/images/

# Copie du script d'entrée
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Utilisation du script comme point d'entrée
ENTRYPOINT ["docker-entrypoint.sh"]

# Exposition du port 80
EXPOSE 80
