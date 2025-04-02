#!/bin/bash

set -ex

composer install
cp .env.dist .env
php artisan migrate
php artisan optimize
