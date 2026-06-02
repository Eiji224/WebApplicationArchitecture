FROM php:8.3-fpm

# Системные пакеты
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev \
    zip unzip libzip-dev \
    netcat-traditional \
    && rm -rf /var/lib/apt/lists/* \

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs && apt-get install -y npm

# PHP-расширения
RUN docker-php-ext-install pdo_mysql mbstring zip opcache
RUN pecl install redis && docker-php-ext-enable redis

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Сначала только composer.json — для кеширования слоя зависимостей
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader

# Потом весь код
COPY . .
RUN composer dump-autoload --optimize

# Права
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["entrypoint.sh"]
