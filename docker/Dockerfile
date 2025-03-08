FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY docker/php.ini /usr/local/etc/php/php.ini
COPY src /var/www/html/

EXPOSE 80
