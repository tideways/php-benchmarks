#!/bin/bash

set -ex

composer install
composer dump-env prod
