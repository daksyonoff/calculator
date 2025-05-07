FROM php:8.1-fpm

LABEL maintainer="Denis Aksenov <daksyonoff@gmail.com>"

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install -j$(nproc) zip pgsql pdo_pgsql \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer


WORKDIR /var/www/html

EXPOSE 9000