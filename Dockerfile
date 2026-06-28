FROM php:8.2-apache

# Installation des dépendances système et PHP
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl pdo pdo_pgsql pdo_mysql zip opcache gd mbstring xml exif

# Activation du module rewrite d'Apache
RUN a2enmod rewrite

# Configuration du DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copie des fichiers de dépendances en premier
COPY composer.json composer.lock ./

# Installation des dépendances sans lancer les scripts (pour éviter les erreurs de DB)
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copie du reste du projet
COPY . .

# Compilation des assets (AssetMapper)
RUN php bin/console asset-map:compile --env=prod

# Permissions
RUN mkdir -p var/cache var/log public/images && \
    chown -R www-data:www-data var public/images

# Gestion du script d'entrée (et correction des fins de ligne Windows si besoin)
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh && \
    chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
EXPOSE 80
