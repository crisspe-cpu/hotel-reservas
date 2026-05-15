# ─── Etapa base ───────────────────────────────────────────────
FROM php:8.2-fpm-alpine AS base

# Dependencias del sistema
RUN apk add --no-cache \
    bash \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    && docker-php-ext-configure gd \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        zip \
        gd \
        opcache \
        bcmath

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar archivos de dependencias primero (cache de capas)
COPY composer.json composer.lock ./

# ─── Etapa desarrollo ─────────────────────────────────────────
FROM base AS development

RUN composer install --no-scripts --no-autoloader

COPY . .

RUN composer dump-autoload --optimize

EXPOSE 9000
CMD ["php-fpm"]

# ─── Etapa producción ─────────────────────────────────────────
FROM base AS production

# Instalar sin dependencias de desarrollo
RUN composer install --no-dev --no-scripts --no-autoloader --optimize-autoloader

COPY . .

RUN composer dump-autoload --optimize \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]