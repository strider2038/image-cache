FROM php:fpm-alpine
MAINTAINER Igor Lazarev <strider2038@rambler.ru>

COPY ./conf/nginx.conf /etc/nginx/nginx.conf
COPY ./conf/nginx/service-prod.conf /etc/nginx/conf.d/service.conf
COPY ./conf/supervisord.conf /etc/supervisord.conf
COPY ./conf/supervisor/* /etc/supervisord/
COPY ./conf/composer/auth.json /var/run/composer/auth.json

ENV COMPOSER_HOME=/var/run/composer

RUN apk --no-cache add --update \
    git \
    curl \
    nginx \
    supervisor && \
    rm -rf /var/cache/apk/* && \
    mkdir -p /services/imgcache /var/log/nginx /var/log/supervisor /var/run/composer && \
    chown -R www-data:www-data /var/log && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer global require "fxp/composer-asset-plugin:^1.2.0"

#   rm -rf /run/composer && \
#   rm /usr/local/bin/composer

WORKDIR /services/imgcache

EXPOSE 80

CMD /usr/bin/supervisord --nodaemon --configuration /etc/supervisord.conf