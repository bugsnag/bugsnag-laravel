#!/usr/bin/env sh

## Script to require a given Laravel version, e.g. "7.16.1"
##
## The version number is passed to composer as-is, so any syntax supported by
## composer is also supported by this script (e.g. "^7", "7.*" etc...)
##
## This script also supports Laravel's "7.x" branch by passing "latest-v7"
## instead of a version number, or the master branch by passing "latest"

set -e

if [ $# -eq 0 ]; then
    printf "Error: No Laravel version given\n\n"
    printf "Usage:\n"
    printf "  $ %s <version>\n\n" "$0"
    printf "Examples:\n"
    printf "  $ %s latest\n" "$0"
    printf "  $ %s latest-v7\n" "$0"
    printf "  $ %s 7.*\n" "$0"
    printf "  $ %s 5.3.0\n" "$0"

    exit 64
fi

LARAVEL_VERSION=$1

if [ "$LARAVEL_VERSION" = "latest" ]; then
    composer require "laravel/framework:dev-master as 7" --no-update
elif [ "$LARAVEL_VERSION" = "latest-v7" ]; then
    composer require "laravel/framework:dev-7.x as 7" --no-update
else
    composer require "laravel/framework:${LARAVEL_VERSION}" --no-update
fi
