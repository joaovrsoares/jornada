FROM php:8.4-fpm

RUN docker-php-ext-install pdo_mysql \
    && ARCH="$(uname -m)" \
    && if [ "$ARCH" = "aarch64" ] || [ "$ARCH" = "arm64" ]; then TW_ARCH="linux-arm64"; else TW_ARCH="linux-x64"; fi \
    && curl -sSLf "https://github.com/tailwindlabs/tailwindcss/releases/download/v4.3.5/tailwindcss-${TW_ARCH}" -o /usr/local/bin/tailwindcss \
    && chmod +x /usr/local/bin/tailwindcss

COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www