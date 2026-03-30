FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip curl zip libzip-dev libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite zip

# Cài composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader

# Tạo SQLite
RUN touch database/database.sqlite

# Quyền
RUN chmod -R 775 database storage bootstrap/cache

# Laravel setup
RUN php artisan key:generate
RUN php artisan migrate --force

CMD php artisan serve --host=0.0.0.0 --port=10000