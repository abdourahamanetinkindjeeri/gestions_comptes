FROM php:8.3-fpm

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

COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

RUN composer install --no-dev --optimize-autoloader

# Nettoyer les caches Laravel et générer Swagger
RUN php artisan config:clear \
 && php artisan route:clear \
 && php artisan view:clear \
 && mkdir -p storage/api-docs \
 && chown -R www-data:www-data storage/api-docs \
 && chmod -R 775 storage/api-docs \
 && php artisan l5-swagger:generate

RUN npm install && npm run build

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV L5_SWAGGER_UI_CSS=https://gestions-comptes.onrender.com/docs/asset/swagger-ui.css
ENV L5_SWAGGER_UI_BUNDLE_JS=https://gestions-comptes.onrender.com/docs/asset/swagger-ui-bundle.js
ENV L5_SWAGGER_UI_STANDALONE_PRESET_JS=https://gestions-comptes.onrender.com/docs/asset/swagger-ui-standalone-preset.js

EXPOSE 8000


 RUN php artisan config:clear \
 && php artisan route:clear \
 && php artisan view:clear \
 && mkdir -p storage/api-docs \
 && chown -R www-data:www-data storage/api-docs \
 && chmod -R 775 storage/api-docs \
 && php artisan l5-swagger:generate

