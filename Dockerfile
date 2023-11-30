FROM php:8.0-apache

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apt-get update \
    && apt-get install -y zlib1g-dev libpng-dev libjpeg-dev vim libxml2-dev \
    && docker-php-ext-install soap sockets mysqli pdo pdo_mysql \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install gd \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && a2enmod rewrite