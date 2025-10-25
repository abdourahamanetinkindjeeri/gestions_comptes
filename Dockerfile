# ---- Étape 1 : Builder l'application PHP ----
FROM php:8.3-fpm AS builder

# Installer les dépendances système nécessaires
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl libpq-dev libzip-dev npm \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

# Installer Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copier les fichiers de Composer d'abord (pour le cache Docker)
COPY composer.json composer.lock ./

# Installer les dépendances PHP sans exécuter les scripts artisan
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copier tout le code de l'application
COPY . .

# ---- Étape 2 : Builder le front-end ----
FROM node:20 AS frontend
WORKDIR /app
COPY --from=builder /var/www/package*.json ./
RUN npm install
COPY --from=builder /var/www ./
RUN npm run build

# ---- Étape 3 : Image finale ----
FROM php:8.3-fpm

# Installer dépendances nécessaires
RUN apt-get update && apt-get install -y libpq-dev libzip-dev libpng-dev \
    && docker-php-ext-install pdo pdo_pgsql bcmath gd zip

WORKDIR /var/www

# Copier le code source et le build frontend
COPY --from=builder /var/www ./
COPY --from=frontend /app/public/build ./public/build

# Installer Composer (pour artisan et cache Laravel)
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Générer les caches Laravel
RUN php -r "file_exists('.env') || copy('.env.example', '.env');" \
    && php artisan key:generate \
    && php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear \
    && mkdir -p storage/api-docs \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Générer la doc Swagger
RUN php artisan vendor:publish --provider="L5Swagger\\L5SwaggerServiceProvider" --tag=swagger-ui --force \
    && php artisan l5-swagger:generate

# Variables d'environnement
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV L5_SWAGGER_UI_CSS=https://gestions-comptes.onrender.com/docs/asset/swagger-ui.css
ENV L5_SWAGGER_UI_BUNDLE_JS=https://gestions-comptes.onrender.com/docs/asset/swagger-ui-bundle.js
ENV L5_SWAGGER_UI_STANDALONE_PRESET_JS=https://gestions-comptes.onrender.com/docs/asset/swagger-ui-standalone-preset.js

EXPOSE 8000

# Commande de démarrage
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000