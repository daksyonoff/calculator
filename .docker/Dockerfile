FROM php:8.1-fpm

LABEL maintainer="Denis Aksenov <daksyonoff@gmail.com>"

RUN apt-get update \
    && apt-get install -y \
        apt-utils \
        iputils-ping \
        libpq-dev \
        libzip-dev \
    && rm -rf /var/lib/apt/lists/* \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install -j$(nproc) zip \
    && docker-php-ext-install -j$(nproc) pgsql \
    && docker-php-ext-install -j$(nproc) pdo_pgsql

WORKDIR /var/www/html

EXPOSE 9000