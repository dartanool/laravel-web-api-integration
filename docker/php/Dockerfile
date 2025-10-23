FROM php:8.4-fpm

# Системные зависимости
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    libsqlite3-dev \
    libbrotli-dev \
    zlib1g-dev \
    libzstd-dev \
    pkg-config \
    build-essential \
    git \
    unzip \
    curl

# PHP расширения
RUN docker-php-ext-install pdo pdo_mysql gd intl zip sockets pcntl

# Установка Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Установка Swoole
RUN pecl install swoole \
    && docker-php-ext-enable swoole

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
