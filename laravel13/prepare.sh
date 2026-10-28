#!/bin/bash

set -ex

composer install
cp .env.dist .env
php artisan migrate:fresh --seed
php artisan optimize
