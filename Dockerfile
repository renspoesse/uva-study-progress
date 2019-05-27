FROM composer:latest AS vendor

WORKDIR /app

COPY composer.json ./
COPY composer.lock ./

RUN composer install --no-scripts --no-autoloader --ignore-platform-reqs

COPY . ./

RUN composer dump-autoload --optimize

FROM php:7.3-fpm AS app

# Laravel 5.8 requirements met by this image:

# PHP >= 7.1.3
# OpenSSL PHP Extension
# PDO PHP Extension
# Mbstring PHP Extension
# Tokenizer PHP Extension
# XML PHP Extension
# Ctype PHP Extension
# JSON PHP Extension
# BCMath PHP Extension TODO: not yet installed

RUN docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd && \
    docker-php-ext-install pdo_mysql

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY ./php.ini $PHP_INI_DIR/conf.d/local.ini
COPY ./php-fpm.conf /usr/local/etc/php-fpm.d/zzz-docker-overrides.conf

WORKDIR /usr/share/nginx/html

COPY --chown=www-data:www-data . ./
COPY --chown=www-data:www-data --from=vendor /app/vendor ./vendor

RUN find * -type d -exec chmod 755 {} \; && \
    find * -type d -exec chmod ug+s {} \; && \
    find * -type f -exec chmod 644 {} \; && \
    chmod -R ug+rwx storage bootstrap/cache

#RUN php artisan route:cache TODO: enable when no more Closures are used

FROM nginx:1.15 AS web

COPY ./nginx.conf /etc/nginx/conf.d/default.conf

WORKDIR /usr/share/nginx/html/public

COPY ./public ./
