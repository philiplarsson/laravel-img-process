FROM php:7.2-apache

LABEL maintainer="Philip Larsson"

COPY .docker/php/php.ini /usr/local/etc/php
COPY .docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY . /srv/app

RUN apt-get update && apt-get install -y libpng-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
    && docker-php-ext-install pdo_mysql \
    && a2enmod rewrite negotiation \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd
