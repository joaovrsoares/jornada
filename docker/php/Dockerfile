FROM php:8.4-fpm-alpine

# Instala pacotes necessários (nginx, mariadb, mariadb-client, supervisor, curl)
RUN apk add --no-cache \
    nginx \
    mariadb \
    mariadb-client \
    supervisor \
    curl \
    && docker-php-ext-install pdo_mysql \
    && sed -i 's/skip-networking/#skip-networking/' /etc/my.cnf.d/mariadb-server.cnf \
    && ARCH="$(uname -m)" \
    && if [ "$ARCH" = "aarch64" ] || [ "$ARCH" = "arm64" ]; then TW_ARCH="linux-arm64"; else TW_ARCH="linux-x64"; fi \
    && curl -sSLf "https://github.com/tailwindlabs/tailwindcss/releases/download/v4.3.5/tailwindcss-${TW_ARCH}" -o /usr/local/bin/tailwindcss \
    && chmod +x /usr/local/bin/tailwindcss

COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY docker/db/init.sql /docker-entrypoint-initdb.d/init.sql

RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]