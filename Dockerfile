FROM dunglas/frankenphp:php8.5.6-bookworm

RUN apt-get update && apt-get install -y unzip zip

RUN install-php-extensions pdo_pgsql intl opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-scripts

COPY . .

EXPOSE 8080

CMD ["sh", "-c", "echo PORT=$PORT && sleep 3600"]