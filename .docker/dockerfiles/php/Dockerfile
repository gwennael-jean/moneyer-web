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
        graphviz

RUN docker-php-ext-configure gd --enable-gd --with-jpeg --with-freetype

ADD ./conf/uploads.ini /usr/local/etc/php/conf.d/uploads.ini
ADD ./conf/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
ADD ./conf/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
ADD conf/core.ini /usr/local/etc/php/conf.d/core.ini
ADD conf/date.ini /usr/local/etc/php/conf.d/date.ini

RUN pecl install apcu

RUN pecl install xdebug

RUN docker-php-ext-install pdo pdo_mysql zip gd intl opcache

RUN docker-php-ext-enable apcu xdebug

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"

ENV TZ Europe/Paris
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

WORKDIR /var/www

RUN usermod -u 1000 www-data

USER www-data
