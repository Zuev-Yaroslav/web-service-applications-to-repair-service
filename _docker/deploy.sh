#!/bin/bash

composer update --no-scripts
php artisan key:generate
php artisan migrate
composer update
npm i
php artisan ziggy:generate
npm run build
chmod -R 777 ./

exec php-fpm
