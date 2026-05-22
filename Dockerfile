# ==============================
# BASE
# ==============================
FROM php:8.2-fpm-alpine

# Dependencias del sistema
RUN apk add --no-cache \
    bash \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
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

# Copiar dependencias primero (cache Docker)
COPY composer.json composer.lock ./

# Instalar dependencias Laravel
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copiar proyecto
COPY . .

# Instalar frontend
RUN npm install

# Build Vite
RUN npm run build

# Permisos Laravel
RUN chmod -R 775 storage bootstrap/cache

# Optimización Laravel
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

EXPOSE 10000

CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT"]