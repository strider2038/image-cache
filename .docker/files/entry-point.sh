#!/bin/sh

config_file='/etc/nginx/nginx.conf'
template_file="$config_file.template"

if [ ! -f ${config_file} ]; then
    sed -e "
        s|__NGINX_CLIENT_MAX_BODY_SIZE__|$NGINX_CLIENT_MAX_BODY_SIZE|
        s|__APP_CONFIGURATION_FILENAME__|$APP_CONFIGURATION_FILENAME|
    " "$template_file" >> "$config_file"

    rm "$template_file"

    echo "Server configuration file $config_file created from template $template_file."
fi

exec "$@"
