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
ENV APP_ENV=prod
ENV APP_SECRET=placeholder
ENV DATABASE_URL=mysql://placeholder

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Build assets - v2
RUN php bin/console importmap:install
RUN php bin/console asset-map:compile
RUN php bin/console cache:clear

ENV SERVER_NAME=:8080

CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]