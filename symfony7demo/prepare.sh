#!/bin/bash

set -ex

cat << "EOT" > .env.local
# This variable is for the demo app only!
# See https://symfony.com/doc/current/reference/configuration/framework.html#configuration-framework-secret
APP_SECRET=not-a-secret--read-the-doc!
EOT
composer install
composer dump-env prod
composer dump-autoload --optimize
