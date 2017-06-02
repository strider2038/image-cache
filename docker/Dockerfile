FROM php:fpm-alpine
MAINTAINER Igor Lazarev <strider2038@rambler.ru>

COPY ./conf/nginx.conf /etc/nginx/nginx.conf
COPY ./conf/supervisord.conf /etc/supervisord.conf
COPY ./conf/supervisor/* /etc/supervisord/

RUN apk --no-cache add --update \
    git \
    nginx \
    supervisor && \
    rm -rf /var/cache/apk/* && \
    mkdir -p /services/imgcache /logs/nginx /logs/supervisor && \
    chown -R www-data:www-data /logs

WORKDIR /services/imgcache

EXPOSE 80

CMD /usr/bin/supervisord --nodaemon --configuration /etc/supervisord.conf