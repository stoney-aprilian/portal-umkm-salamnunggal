# Build stage — installs Composer deps, Node/npm, and builds Vite assets
FROM php:8.3-fpm-alpine AS build

RUN apk add --no-cache \
    nodejs \
    npm \
    gettext \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libzip-dev \
    libpq-dev \
    libxml2-dev \
    icu-dev \
    oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        exif \
        gd \
        intl \
        mbstring \
        pdo_mysql \
        pdo_pgsql \
        pcntl \
        zip \
        opcache \
        sockets \
    && rm -rf /var/cache/apk/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

COPY package.json package-lock.json ./
RUN npm ci

COPY . .

RUN npm run build

# Production stage — clean PHP-FPM + Nginx image without Node/npm
FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    nginx \
    gettext \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libzip-dev \
    libpq-dev \
    libxml2-dev \
    icu-dev \
    oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        exif \
        gd \
        intl \
        mbstring \
        pdo_mysql \
        pdo_pgsql \
        pcntl \
        zip \
        opcache \
        sockets \
    && rm -rf /var/cache/apk/*

WORKDIR /var/www/html

COPY --from=build /var/www/html/vendor ./vendor
COPY --from=build /var/www/html/public/build ./public/build

COPY . .

COPY resources/demo-media/ ./storage/app/public/

RUN rm -rf public/storage && php artisan storage:link --no-interaction

RUN chown -R www-data:www-data storage bootstrap/cache

COPY nginx.conf /etc/nginx/http.d/default.conf.template
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["entrypoint.sh"]
