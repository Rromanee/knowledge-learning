FROM dunglas/frankenphp:php8.4

RUN install-php-extensions \
    pdo_mysql \
    intl \
    opcache

WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

ENV APP_ENV=prod

RUN php bin/console cache:clear

CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]