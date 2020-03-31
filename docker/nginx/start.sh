#!/bin/sh

sed -i -E "s/PHP_FPM_HOST/${PHP_FPM_HOST:-php}/" /etc/nginx/conf.d/*.conf
sed -i -E "s/PHP_FPM_PORT/${PHP_FPM_PORT:-9000}/" /etc/nginx/conf.d/*.conf

cat /etc/nginx/conf.d/payments_api.conf

nginx -g "daemon off;"
