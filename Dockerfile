FROM php:8.2-fpm-bullseye

# System dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    supervisor \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pdo_mysql \
    opcache \
    zip \
    bcmath \
    pcntl

# Opcache tuning for production
RUN printf "opcache.enable=1\nopcache.memory_consumption=128\nopcache.max_accelerated_files=10000\nopcache.revalidate_freq=0\n" \
    > /usr/local/etc/php/conf.d/opcache.ini

# Composer 2
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Skip post-autoload scripts (they need a real DB/key at runtime)
ENV COMPOSER_ALLOW_SUPERUSER=1

# Regenerate composer.lock for PHP 8.2 (the lock may have been generated on PHP 8.4 locally)
RUN composer update --lock --no-scripts --no-interaction --no-progress --quiet

# Install production dependencies
RUN composer install --no-dev --no-scripts --optimize-autoloader --no-interaction --no-progress

RUN mkdir -p storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Nginx — remove default site, install ours
RUN rm -f /etc/nginx/sites-enabled/default
COPY conf/nginx/nginx-site.conf /etc/nginx/conf.d/laravel.conf

# Supervisor + startup
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
