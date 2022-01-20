FROM php:7.3-fpm

LABEL maintainer="Mubasher Iqbal mubasheriqbal693@gmail.com"

ENV DOCKER_DIR /var/www/centra
ARG XDEBUG_VERSION=2.5.5

RUN apt update \
    && apt install -y zlib1g-dev g++ libicu-dev zip libzip-dev zip \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

WORKDIR $DOCKER_DIR

#Installing composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#xdebug install xdebug
RUN apt update \
    && pecl install xdebug
RUN docker-php-ext-enable xdebug
COPY docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/