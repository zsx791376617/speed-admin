FROM php:8.1-fpm-alpine

LABEL maintainer="SpeedAdmin <support@speedadmin.cn>"

RUN set -ex \
    && apk add --no-cache \
        nginx \
        supervisor \
        curl \
        wget \
        libzip-dev \
        zip \
        unzip \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        icu-dev \
        openssl-dev \
        oniguruma-dev \
        zlib-dev \
        pcre-dev \
        libxml2-dev \
        g++ \
        make \
        autoconf \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        gd \
        pdo_mysql \
        mysqli \
        zip \
        intl \
        mbstring \
        opcache \
        bcmath \
        xml \
        pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del g++ make autoconf \
    && rm -rf /tmp/* /var/cache/apk/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

RUN composer install --no-dev --optimize-autoloader

COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf

EXPOSE 80

CMD ["supervisord", "-n"]