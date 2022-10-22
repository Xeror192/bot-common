#!/usr/bin/env sh

composer run-script --no-cache post-install-cmd
chmod -R 777 var
chown -R www-data:www-data var

php-fpm