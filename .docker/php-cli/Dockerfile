FROM php:8.1-cli-alpine

RUN apk update \
    && apk add --no-cache curl g++ make autoconf bash \
    && docker-php-ext-install mysqli \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && docker-php-source extract \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-source delete;

COPY ./php-cli/configurations/* /usr/local/etc/php/conf.d/