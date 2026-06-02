#!/bin/sh
set -e

# Ждем доступности MySQL (порт 3306)
echo "Waiting for MySQL to be ready..."
# nc -z проверяет порт. Если netcat-traditional, используйте nc.
while ! nc -z mysql 3306; do
  echo "MySQL is not ready yet... sleeping"
  sleep 2
done

echo "MySQL is ready! Running migrations..."
php artisan migrate --force

echo "Generating Passport keys..."
# Добавим проверку, чтобы не генерировать каждый раз, если они уже есть
if [ ! -f storage/oauth-private.key ]; then
    php artisan passport:keys --force
fi

chown -R www-data:www-data /var/www/html/storage
chmod -R 660 /var/www/html/storage/oauth-*.key

echo "Starting PHP-FPM..."
exec php-fpm
