FROM php:5.6-apache

ENV TZ=Europe/Warsaw
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
RUN echo "date.timezone=$TZ" >> /usr/local/etc/php/conf.d/default.ini
RUN apt-get update -y
RUN a2enmod rewrite
RUN a2enmod headers
RUN a2enmod expires
RUN docker-php-ext-install mysqli
RUN pecl install xdebug-2.5.5
RUN docker-php-ext-enable xdebug
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
	libpng-dev \
    libzip-dev \
	libicu-dev
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install zip
RUN docker-php-ext-install gd
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl
RUN apt-get install -y mc vim nano
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN echo "xdebug.remote_enable=1" >> /usr/local/etc/php/php.ini
