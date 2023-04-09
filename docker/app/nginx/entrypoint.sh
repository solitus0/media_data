#!/usr/bin/env bash
php-fpm &
until echo 'ping' > /dev/tcp/localhost/9000;
do
echo "Cannot connect to PHP-FPM... Trying again"
sleep 2
done
nginx -g 'daemon off;'
