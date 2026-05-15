# ─── Etapa base ───────────────────────────────────────────────
FROM php:8.2-fpm-alpine AS base

# Dependencias del sistema + Node
RUN apk add --no-cache \
    bash \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    nodejs \
    npm \
    && docker-php-ext-configure gd \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        zip \
        gd \
        opcache \
        bcmath

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar dependencias primero
COPY composer.json composer.lock ./
COPY package.json package-lock.json ./

# ─── Producción ───────────────────────────────────────────────
FROM base AS production

# Instalar backend
RUN composer install --no-dev --optimize-autoloader

# Instalar frontend
RUN npm install

# Copiar proyecto
COPY . .

# Build Vite
RUN npm run build

# Optimizar Laravel
RUN composer dump-autoload --optimize \
    && mkdir -p storage/framework/cache \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=$PORT