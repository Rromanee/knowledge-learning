FROM dunglas/frankenphp:php8.4

RUN install-php-extensions \
    pdo_mysql \
    intl \
    opcache \
    zip

WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --no-dev --optimize-autoloader --no-interaction

ENV APP_ENV=prod
ENV SERVER_NAME=:8080

RUN php bin/console importmap:install
RUN php bin/console cache:clear

CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]