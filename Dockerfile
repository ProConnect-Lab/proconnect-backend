# syntax=docker/dockerfile:1.6

FROM composer:2.7 AS vendor
WORKDIR /var/www/html
COPY . .
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-ansi \
    --prefer-dist \
    --optimize-autoloader

FROM node:20 AS frontend
WORKDIR /var/www/html
COPY package.json package-lock.json* ./
RUN npm ci --legacy-peer-deps || npm install
COPY resources ./resources
COPY vite.config.js .
RUN npm run build

FROM php:8.2-cli AS app
WORKDIR /var/www/html

ENV APP_ENV=production
ENV PORT=8000

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql bcmath \
    && rm -rf /var/lib/apt/lists/*

COPY --from=vendor /var/www/html /var/www/html
COPY --from=frontend /var/www/html/public/build ./public/build

COPY docker/start.sh /usr/local/bin/start.sh
RUN rm -f public/hot \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs \
    && chmod +x /usr/local/bin/start.sh \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8000

CMD ["/usr/local/bin/start.sh"]
