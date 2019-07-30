FROM php:5.6-apache
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
RUN curl -sS https://getcomposer.org/installer | php -- --version=1.6.4 --install-dir=/usr/local/bin --filename=composer
RUN echo "xdebug.remote_enable=1" >> /usr/local/etc/php/php.ini
