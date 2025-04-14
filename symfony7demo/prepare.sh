#!/bin/bash

set -ex

composer install
composer dump-env prod
composer dump-autoload --optimize
