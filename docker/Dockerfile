FROM php:fpm-alpine
MAINTAINER Igor Lazarev <strider2038@rambler.ru>

COPY ./conf/nginx.conf /etc/nginx/nginx.conf
COPY ./conf/supervisord.conf /etc/supervisord.conf
COPY ./conf/supervisor/* /etc/supervisord/
COPY ./conf/composer/auth.json /run/composer/auth.json

ENV COMPOSER_HOME=/run/composer

RUN apk --no-cache add --update \
    git \
    curl \
    nginx \
    supervisor && \
    rm -rf /var/cache/apk/* && \
    mkdir -p /services/imgcache /logs/nginx /logs/supervisor /run/composer && \
    chown -R www-data:www-data /logs && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer global require "fxp/composer-asset-plugin:^1.2.0"

#   rm -rf /run/composer

WORKDIR /services/imgcache

EXPOSE 80

CMD /usr/bin/supervisord --nodaemon --configuration /etc/supervisord.conf