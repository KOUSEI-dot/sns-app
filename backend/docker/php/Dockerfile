FROM php:8.2-fpm

# 必要パッケージと拡張のインストール
RUN apt-get update && apt-get install -y \
    zip unzip git vim libpng-dev libjpeg-dev libfreetype6-dev locales \
    && docker-php-ext-install pdo_mysql

WORKDIR /var/www
COPY php.ini /usr/local/etc/php/
