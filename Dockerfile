# ── Stage 1 : génération de la documentation Scribe ─────────────────────────
FROM php:8.4-fpm-bullseye AS docs-builder

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev libzip-dev zip unzip \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_pgsql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1

# Installer toutes les dépendances (dont Scribe en dev)
RUN composer install --no-scripts --no-interaction --no-progress

# URL de l'API en production (utilisée par Scribe pour le "Try it out")
ARG APP_URL=https://boucherie-api.onrender.com

# Environnement minimal pour booter Laravel sans DB réelle
RUN printf "APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=\n\
APP_ENV=local\n\
APP_URL=${APP_URL}\n\
DB_CONNECTION=sqlite\n\
DB_DATABASE=:memory:\n\
LOG_CHANNEL=stderr\n" > .env

# Découverte des packages puis génération de la doc statique
RUN php artisan package:discover --ansi \
    && php artisan scribe:generate \
    && rm .env


# ── Stage 2 : tests (bloque le déploiement si un test échoue) ────────────────
FROM php:8.4-fpm-bullseye AS tester

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev libzip-dev libsqlite3-dev zip unzip \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_pgsql pdo_sqlite zip bcmath

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1

# Installer toutes les dépendances (dont Pest/factories en dev)
RUN composer install --no-scripts --no-interaction --no-progress

# Environnement de test minimal avec SQLite en mémoire
RUN printf "APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=\n\
APP_ENV=testing\n\
DB_CONNECTION=sqlite\n\
DB_DATABASE=:memory:\n\
LOG_CHANNEL=stderr\n" > .env

RUN php artisan package:discover --ansi

# Lancement des tests — le build échoue ici si un test est rouge
RUN vendor/bin/pest --no-coverage


# ── Stage 3 : image de production ────────────────────────────────────────────
FROM php:8.4-fpm-bullseye

# Dépendances système
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    supervisor \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Extensions PHP
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pdo_mysql \
    opcache \
    zip \
    bcmath \
    pcntl

# Opcache production
RUN printf "opcache.enable=1\nopcache.memory_consumption=128\nopcache.max_accelerated_files=10000\nopcache.revalidate_freq=0\n" \
    > /usr/local/etc/php/conf.d/opcache.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Copier la doc générée depuis le stage 1
COPY --from=docs-builder /var/www/html/public/docs public/docs

ENV COMPOSER_ALLOW_SUPERUSER=1

# Porte de sécurité : ce COPY échoue si le stage tester a échoué (tests rouges)
COPY --from=tester /var/www/html/vendor/autoload.php /tmp/.test-gate
RUN rm /tmp/.test-gate

# Installer uniquement les dépendances de production
RUN composer install --no-dev --no-scripts --optimize-autoloader --no-interaction --no-progress

RUN mkdir -p storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Nginx
RUN rm -f /etc/nginx/sites-enabled/default
COPY conf/nginx/nginx-site.conf /etc/nginx/conf.d/laravel.conf

# Supervisor + démarrage
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
