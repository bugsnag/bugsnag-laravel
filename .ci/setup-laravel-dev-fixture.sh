#!/usr/bin/env sh

set -e

cd features/fixtures

rm -rf laravel-latest

# Ignore dev dependencies because we don't need them to run the Maze Runner tests
# and they will only introduce more failure points
composer create-project laravel/laravel laravel-latest --no-dev

cd laravel-latest

composer require 'laravel/framework:dev-master as 8' --update-with-dependencies --no-update
composer config repositories.bugsnag-laravel '{ "type": "path", "url": "../../../", "options": { "symlink": false } }'
composer require bugsnag/bugsnag-laravel '*' --no-update

composer update --no-dev

printf "\nCreated Laravel project using these versions:\n"

composer show --direct

printf "\nApplying patches...\n"

for patch in ../../../.ci/patches/*.patch; do
    patch -p1 < "$patch"
done
