FROM php:8.1-fpm

WORKDIR /var/www

RUN apt-get update \
    && apt-get upgrade -y \
    && apt-get install -y git zip unzip \
    && php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

ENV PATH "$PATH:~/.composer/vendor/bin"
