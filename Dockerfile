FROM php:fpm-alpine
LABEL maintainer="Igor Lazarev <strider2038@yandex.ru>"

ENV APPLICATION_NAME=image-cache
ENV APPLICATION_VERSION=dev
ENV COMPOSER_HOME=/var/run/composer
ENV APP_CONFIGURATION_FILENAME=config/parameters.yml
ENV NGINX_CLIENT_MAX_BODY_SIZE=4M

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
    rm /etc/nginx/nginx.conf && \
    rm -rf /var/cache/apk/* && \
    rm -rf /usr/local/etc/php-fpm.d/* && \
    mkdir -p \
        /var/log/nginx \
        /var/log/supervisor \
        /var/run/composer && \
    chown -R www-data:www-data \
        /var/log \
        /tmp \
        /var/tmp/nginx && \
    chmod -R 0775 /var/tmp/nginx

WORKDIR /app

COPY .docker/files/ /
COPY . /app

RUN chmod +x /entry-point.sh && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer install --no-dev && \
    rm -rf /var/run/composer && \
    rm /usr/local/bin/composer && \
    rm -rf /app/.docker && \
    rm -rf /tmp/* && \
    chown -R www-data:www-data /app

EXPOSE 80

ENTRYPOINT ["/entry-point.sh"]
CMD ["/usr/bin/supervisord", "--configuration", "/etc/supervisord.conf"]
