Changelog
=========

## 2.0.2 (2016-07-18)

### Bug Fixes

* Removed support for using `HTTP_PROXY` environment variable for non-CLI apps
  per [CVE-2016-5385 (httpoxy)](https://httpoxy.org/).
  [Graham Campbell](https://github.com/GrahamCampbell)
  [#143](https://github.com/bugsnag/bugsnag-laravel/pull/143)
  [#145](https://github.com/bugsnag/bugsnag-laravel/pull/145)

* Convert `BUGSNAG_NOTIFY_RELEASE_STAGES` to a comma-delimited array
  [Jason](https://github.com/fire015)
  [Graham Campbell](https://github.com/GrahamCampbell)
  [#142](https://github.com/bugsnag/bugsnag-laravel/pull/142)
  [#144](https://github.com/bugsnag/bugsnag-laravel/pull/144)

## 2.0.1 (2016-07-08)

### Bug Fixes

* Lowered the minimum PHP version to 5.5.0
  [Graham Campbell](https://github.com/GrahamCampbell)
  [#138](https://github.com/bugsnag/bugsnag-laravel/pull/138)

## 2.0.0 (2016-07-07)

Our library has gone through some major improvements. The primary change to watch out for is we're no longer overriding your exception handler.

### Enhancements

* Since we're no longer overriding your exception handler, you'll need to restore your original handler, and then see our docs for how to bind our new logger to the container.

* If you'd like access to all our new configuration, you'll need to re-publish our config file.

### Bug Fixes

* Every bug

## 1.7.0 (2016-06-24)

## Enhancements

* Let Laravel decide whether to report or not
  [Phil Bates](https://github.com/philbates35)
  [#97](https://github.com/bugsnag/bugsnag-laravel/pull/97)

## Bug Fixes

* Fixed version constraint
  [Graham Campbell](https://github.com/GrahamCampbell)
  [#111](https://github.com/bugsnag/bugsnag-laravel/pull/111)

* Ensure the api key is a string
  [Graham Campbell](https://github.com/GrahamCampbell)
  [57afd32](https://github.com/bugsnag/bugsnag-laravel/commit/57afd321273486d4f24a96d9eb3f0938278c9f4d)

## 1.6.4 (2016-03-09)

### Bug Fixes

* Add missing 'config' tag in service provider
  [Dan Smith](https://github.com/DanSmith83)
  [#73](https://github.com/bugsnag/bugsnag-laravel/pull/73)

1.6.3
-----

### Bug Fixes

- Avoid initializing Bugsnag when no API key is set
  | [Dries Vints](https://github.com/driesvints)
  | [#72](https://github.com/bugsnag/bugsnag-laravel/pull/72)

1.6.2
-----

### Enhancements

- Add support for configuring the notifier completely from
[environment variables](https://github.com/bugsnag/bugsnag-laravel#environment-variables)
  | [Andrew](https://github.com/browner12)
  | [#71](https://github.com/bugsnag/bugsnag-laravel/pull/71)

1.6.1
-----
-   Fix array syntax for older php

1.6.0
-----
-   Move to using .env for api key in laravel 5+
-   Support for artisan vendor:publish

1.5.1
-----
-   Lumen Service Provider use statement

1.5.0
-----
-   Lumen support
-   Fix bug in instructions
-   Fix bug with reading settings from service file

1.4.2
-----
-   Try/catch for missing/nonstandard auth service

1.4.1
-----
-   Default severity to 'error'

1.4.0
-----
-   Better laravel 5 support!

1.3.0
-----
-   Laravel 5 support!

1.2.1
-----
-   Protect against missing configuration variables (thanks @jasonlfunk!)

1.2.0
-----
-   Update `bugsnag-php` dependency to enable support for code snippets on
    your Bugsnag dashboard
-   Allow configuring of more Bugsnag settings from your `config.php`
    (thanks @jacobmarshall!)

1.1.1
-----
-   Fix issue where sending auth information with complex users could fail (thanks @hannesvdvreken!)

1.1.0
-----
-   Send user/auth information if available (thanks @hannesvdvreken!)

1.0.10
------
-   Laravel 5 support

1.0.9
------
-   Split strip paths from `inProject`

1.0.8
-----
-   Bump the bugsnag-php dependency to include recent fixes

1.0.7
-----
-   Fix major notification bug introduced in 1.0.6

1.0.6
-----
-   Fix incompatibility with PHP 5.3

1.0.5
-----
-   Identify as Laravel notifier instead of PHP

1.0.4
-----
-   Allow configuration of notify_release_stages from config file

1.0.3
-----
-   Fix bug when setting releaseStage in the ServiceProvider

1.0.2
-----
-   Fix laravel requirement to work with 4.1
-   Add a `Bugsnag` facade for quick access to $app["bugsnag"]

1.0.1
-----
-   Fixed fatal error handling
-   Set release stage based on laravel's `App::environment` setting

1.0.0
-----
-   Initial release
