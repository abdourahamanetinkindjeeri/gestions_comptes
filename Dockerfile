# Dockerfile optimisé pour Laravel sur Render
FROM php:8.3-fpm

# Installer les dépendances système
RUN apt-get update \
    && apt-get install -y \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        zip \
        unzip \
        git \
        curl \
        libpq-dev \
        libzip-dev \
        npm \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

# Installer Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Copier le code source
WORKDIR /var/www
COPY . .

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Générer la documentation Swagger
RUN mkdir -p storage/api-docs \
    && chown -R www-data:www-data storage/api-docs \
    && chmod -R 775 storage/api-docs \
    && php artisan l5-swagger:generate

# Installer les dépendances JS et builder le front
RUN npm install && npm run build

# Donner les bons droits
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Variables d'environnement
ENV APP_ENV=production
ENV APP_DEBUG=false

# Exposer le port 8000
EXPOSE 8000

# Commande de démarrage
CMD php artisan config:clear \
 && php artisan route:clear \
 && php artisan view:clear \
 && php artisan migrate --force \
 && php artisan config:cache \
 && php artisan route:cache \
 && php artisan serve --host=0.0.0.0 --port=8000

