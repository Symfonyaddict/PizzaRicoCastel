# syntax=docker/dockerfile:1.7

# -----------------------------
# Stage 1 : build (composer + autoload + cache prod)
# -----------------------------
FROM php:8.2-apache AS builder

RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl pdo pdo_pgsql zip opcache gd mbstring xml exif \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    APP_ENV=prod \
    APP_SECRET=!ChangeMe!

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --no-scripts \
    && rm -rf /tmp/* /root/.composer/cache

COPY . .
RUN composer run-script post-install-cmd --no-interaction

RUN rm -rf var/cache/* var/log/* \
    && mkdir -p var/cache var/log public/images public/media \
    && chown -R www-data:www-data var public/images public/media

# -----------------------------
# Stage 2 : runtime final (sans outils de build)
# -----------------------------
FROM php:8.2-apache AS runtime

RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl pdo pdo_pgsql zip opcache gd mbstring xml exif \
    && a2enmod rewrite headers expires \
    && apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc /usr/share/man

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    APP_ENV=prod

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY --from=builder --chown=www-data:www-data /var/www/html /var/www/html
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

WORKDIR /var/www/html
ENTRYPOINT ["docker-entrypoint.sh"]
EXPOSE 80
