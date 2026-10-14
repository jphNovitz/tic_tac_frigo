FROM dunglas/frankenphp

RUN install-php-extensions pdo_mysql intl zip gd opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
