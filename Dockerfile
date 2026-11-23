FROM php:8.4-fpm-alpine

# 1. Install System Dependencies & PHP Extensions
RUN apk add --no-cache \
    nginx \
    curl \
    git \
    supervisor \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    sqlite-dev \
    nodejs \
    npm \
    linux-headers \
    bash

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_sqlite pdo_mysql bcmath zip opcache

# 2. Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 3. Copy Application Files
COPY . /var/www/html/

# 4. Prepare Environment & Permissions
RUN git config --global --add safe.directory /var/www/html \
    && cp .env.example .env \
    && composer install --no-dev --optimize-autoloader --no-interaction \
    && npm install \
    && npm run build \
    && touch database/database.sqlite \
    && php artisan key:generate --force \
    && php artisan migrate --seed --force \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# 5. Copy Nginx & Supervisor Config
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
