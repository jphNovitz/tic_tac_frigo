FROM dunglas/frankenphp

RUN install-php-extensions pdo_mysql intl zip gd opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Préparer les répertoires de FrankenPHP
RUN mkdir -p /config/caddy /data/caddy \
    && chown -R www-data:www-data /config /data

# Écouter sur un port non privilégié
ENV SERVER_NAME=:8080

RUN setcap -r /usr/local/bin/frankenphp

# Ne pas exécuter en root
USER www-data
