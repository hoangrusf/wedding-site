FROM php:8.2-fpm

# Cài extension cần thiết
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev zip curl \
    && docker-php-ext-install pdo pdo_sqlite zip

# Cài composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Thư mục làm việc
WORKDIR /var/www

# Copy code vào container
COPY . .

# Cài dependency
RUN composer install --no-dev --optimize-autoloader

# Tạo database SQLite (nếu chưa có)
RUN touch database/database.sqlite

# Quyền ghi
RUN chmod -R 775 database storage bootstrap/cache

# Laravel setup
RUN php artisan key:generate
RUN php artisan migrate --force

# Chạy server
CMD php artisan serve --host=0.0.0.0 --port=10000