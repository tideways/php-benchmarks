#!/bin/bash

set -ex

php wp-cli.phar config create --dbname=wordpress67 --dbuser=www-data
php wp-cli.phar db reset --yes
php wp-cli.phar core install --url=wordpress67.localhost --title="WP-CLI" --admin_user=wpcli --admin_password=wpcli --admin_email=info@wp-cli.org
