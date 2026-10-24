
FROM serversideup/php:8.3-fpm-nginx

USER root

# Install MySQL PDO driver
RUN install-php-extensions pdo_mysql

WORKDIR /var/www/html

# Copy the existing Laravel application
COPY --chown=www-data:www-data . .

# Install production dependencies
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-progress

# Prepare Laravel writable directories
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Render's default web service port
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr
ENV NGINX_HTTP_PORT=10000

USER www-data

EXPOSE 10000
