#!/usr/bin/env sh

set -ex

if [ $# -eq 0 ]; then
    printf "Error: No Laravel version given\n\n"
    printf "Usage:\n"
    printf "  $ %s <version>\n\n" "$0"
    printf "Examples:\n"
    printf "  $ %s 8.0.0\n" "$0"
    printf "  $ %s 8.x-dev as 8\n" "$0"

    exit 64
fi

LARAVEL_VERSION=$1

cd features/fixtures

rm -rf laravel-latest

# Ignore dev dependencies because we don't need them to run the Maze Runner tests
# and they will only introduce more failure points
composer create-project laravel/laravel laravel-latest --no-dev "$LARAVEL_VERSION"

cd laravel-latest

composer require laravel/framework:"$LARAVEL_VERSION" --update-with-dependencies --no-update
composer config repositories.bugsnag-laravel '{ "type": "path", "url": "../../../", "options": { "symlink": false } }'
composer config minimum-stability dev
composer require bugsnag/bugsnag-laravel '*' --no-update

composer update --no-dev

printf "\nCreated Laravel project using these versions:\n"

composer show --direct

printf "\nApplying patches...\n"

for patch in ../../../.ci/patches/*.patch; do
    patch -p1 < "$patch"
done
