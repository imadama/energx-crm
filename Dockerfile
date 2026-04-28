FROM php:8.4-fpm-alpine

# php-extension-installer — pre-built binaries, geen broncode compilatie
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# System dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    unzip \
    git

# PHP extensions (pre-built, razendsnel)
RUN install-php-extensions \
    pdo_mysql \
    mbstring \
    xml \
    gd \
    zip \
    bcmath \
    intl \
    opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies (no dev, optimized autoloader)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copy application
COPY . .

# Run post-install scripts now that full app is present
RUN composer run-script post-autoload-dump

# Storage & cache permissions
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 775 storage bootstrap/cache

# Nginx + Supervisor config
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
