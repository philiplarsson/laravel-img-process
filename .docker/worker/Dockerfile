FROM php:7.2-alpine

LABEL maintainer="Philip Larsson"

COPY .docker/php/php.ini /usr/local/etc/php
COPY . /srv/app

RUN apk --update add wget \
  curl \
  git \
  build-base \
  libmemcached-dev \
  libmcrypt-dev \
  libxml2-dev \
  libpng-dev \
  freetype-dev \
  libjpeg-turbo-dev \
  zlib-dev \
  autoconf \
  cyrus-sasl-dev \
  libgsasl-dev \
  supervisor

RUN docker-php-ext-install mysqli mbstring pdo pdo_mysql tokenizer xml pcntl
RUN pecl channel-update pecl.php.net && pecl install memcached mcrypt-1.0.1 && docker-php-ext-enable memcached

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) \
    && docker-php-ext-install -j${NPROC} gd

#Install BCMath package:
ARG INSTALL_BCMATH=false
RUN if [ ${INSTALL_BCMATH} = true ]; then \
    docker-php-ext-install bcmath \
;fi

COPY .docker/worker/supervisord.conf /etc/supervisord.conf

ENTRYPOINT ["/usr/bin/supervisord", "--nodaemon", "--logfile", "/srv/app/storage/logs/supervisord.log", "--pidfile", "/var/run/supervisord.pid", "--configuration",  "/etc/supervisord.conf"]

