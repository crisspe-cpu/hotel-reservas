FROM php:8.2-fpm-alpine

# Dependencias sistema
RUN apk add --no-cache \
    bash \
    curl \
    nodejs \
    npm \
    libpng-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev

# Extensiones PHP
RUN docker-php-ext-configure gd \
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

# Copiar TODO el proyecto primero
COPY . .

# Instalar dependencias Laravel
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Instalar frontend
RUN npm install

# Build Vite
RUN npm run build
RUN php artisan optimize:clear || true

# Crear carpetas necesarias Laravel
RUN mkdir -p storage/framework/cache \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache

# Permisos Laravel
RUN chmod -R 777 storage bootstrap/cache

# Optimización Laravel


EXPOSE 10000

CMD ["sh", "-c", "php artisan migrate --seed --force && php -S 0.0.0.0:$PORT -t public"]