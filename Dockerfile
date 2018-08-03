FROM php:7.2-apache

LABEL maintainer="Philip Larsson"

COPY .docker/php/php.ini /usr/local/etc/php
COPY .docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY . /srv/app

RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite negotiation
