FROM php:fpm-alpine
MAINTAINER Igor Lazarev <strider2038@yandex.ru>

RUN apk --no-cache add --update \
    curl \
    nginx \
    supervisor \
    imagemagick-dev \
    pcre-dev \
    libtool \
    autoconf \
    g++ \
    make && \
    pecl install imagick && \
    docker-php-ext-enable imagick && \
    rm -rf /var/cache/apk/* && \
    mkdir -p /var/log/nginx /var/log/supervisor /var/run/composer && \
    chown -R www-data:www-data /var/log && \
    chown -R www-data:www-data /tmp

WORKDIR /app

ENV COMPOSER_HOME=/var/run/composer

COPY ./.docker/nginx.conf /etc/nginx/nginx.conf
COPY ./.docker/supervisord.conf /etc/supervisord.conf
COPY ./.docker/supervisor/nginx.conf /etc/supervisord/nginx.conf
COPY ./.docker/supervisor/php-fpm.conf /etc/supervisord/php-fpm.conf
COPY ./.docker/composer/auth.json /var/run/composer/auth.json
COPY . /app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer install --no-dev && \
    rm -rf /var/run/composer && \
    rm /usr/local/bin/composer && \
    rm -rf /app/.docker && \
    rm -rf /tmp/* && \
    chown -R www-data:www-data /app

EXPOSE 80

ENTRYPOINT ["/usr/bin/supervisord", "--configuration", "/etc/supervisord.conf"]
