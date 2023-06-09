FROM php:7.4-apache

WORKDIR /var/www/html

RUN apt-get update --fix-missing && \
    apt-get install -y libpng-dev zlib1g-dev libxml2-dev libzip-dev zip curl unzip && \
    apt-get clean

RUN pecl install redis && docker-php-ext-enable redis

RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Xdebug
# RUN pecl install xdebug-3.1.2
# ADD docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

COPY ./vhost.conf /etc/apache2/sites-available/000-default.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY src /var/www/html

RUN chmod -R 777 /var/www/html/storage

ADD setup.sh /var/www
RUN chmod +x /var/www/setup.sh

RUN composer install --ignore-platform-reqs --no-scripts

RUN a2enmod rewrite && \
    service apache2 restart

ENTRYPOINT /var/www/setup.sh
