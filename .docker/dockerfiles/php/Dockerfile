FROM php:8.0-fpm

RUN apt-get update

RUN apt-get install -y \
        zlib1g-dev \
        libicu-dev \
        git-core \
        libxrender1 \
        libfontconfig1 \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev \
        graphviz \
        libxslt-dev

RUN docker-php-ext-configure gd --enable-gd --with-jpeg --with-freetype

ADD ./conf/uploads.ini /usr/local/etc/php/conf.d/uploads.ini
ADD ./conf/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
ADD conf/core.ini /usr/local/etc/php/conf.d/core.ini
ADD conf/date.ini /usr/local/etc/php/conf.d/date.ini
ADD ./conf/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
ADD ./conf/error_reporting.ini /usr/local/etc/php/conf.d/error_reporting.ini

RUN pecl install apcu

RUN pecl install xdebug

RUN docker-php-ext-install pdo pdo_mysql zip gd intl opcache xsl

RUN docker-php-ext-enable apcu xdebug

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"

ENV TZ Europe/Paris
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

ADD .ssh/jwtRS256.key /var/ssh/jwtRS256
ADD .ssh/jwtRS256.key.pub /var/ssh/jwtRS256.pub
RUN chmod 660 /var/ssh/*
RUN chgrp www-data /var/ssh/*

WORKDIR /var/www

RUN usermod -u 1000 www-data

USER www-data
