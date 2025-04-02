#!/bin/bash

set -ex

composer install
mv .env.dist .env
php artisan migrate
php artisan optimize
