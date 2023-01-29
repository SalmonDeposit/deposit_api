FROM php:7.4-apache

WORKDIR /var/www/html

RUN apt-get update --fix-missing && \
    apt-get install -y libpng-dev zlib1g-dev libxml2-dev libzip-dev zip curl unzip && \
    apt-get clean

RUN pecl install redis && docker-php-ext-enable redis

RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

COPY ./vhost.conf /etc/apache2/sites-available/000-default.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY src/* /var/www/html

RUN composer install --ignore-platform-reqs --no-scripts

RUN a2enmod rewrite && \
    service apache2 restart \

