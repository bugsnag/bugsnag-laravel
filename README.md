# Bugsnag exception reporter for Laravel and Lumen
[![Build Status](https://img.shields.io/travis/bugsnag/bugsnag-laravel/master.svg?style=flat-square)](https://travis-ci.org/bugsnag/bugsnag-laravel)
[![StyleCI Status](https://styleci.io/repos/14268472/shield?branch=master)](https://styleci.io/repos/14268472)
[![Documentation](https://img.shields.io/badge/documentation-latest-blue.svg?style=flat-square)](https://docs.bugsnag.com/platforms/php/)

The Bugsnag Notifier for Laravel gives you instant notification of errors and exceptions in your Laravel PHP applications. We support both Laravel and Lumen.


### Looking for Laravel 4 support?

v1 of our Laravel package supports Laravel 4. You can find it on our [1.7 branch](https://github.com/bugsnag/bugsnag-laravel/tree/1.7).


## Features

* Automatically report unhandled exceptions and crashes
* Report handled exceptions
* Attach user information and custom diagnostic data to determine how many people are affected by a crash


## Getting started

1. [Create a Bugsnag account](https://bugsnag.com)
2. Complete the instructions in the integration guide for [Laravel](https://docs.bugsnag.com/platforms/php/laravel/) or [Lumen](https://docs.bugsnag.com/platforms/php/lumen/)
3. Report handled exceptions using [`Bugsnag::notify()`](https://docs.bugsnag.com/platforms/php/laravel/#reporting-handled-exceptions)
4. Customize your integration using the [configuration options](https://docs.bugsnag.com/platforms/php/laravel/configuration-options/)


## Support

* Check out the [configuration options](https://docs.bugsnag.com/platforms/php/laravel/configuration-options/)
* [Search open and closed issues](https://github.com/bugsnag/bugsnag-laravel/issues?utf8=✓&q=is%3Aissue) for similar problems
* [Report a bug or request a feature](https://github.com/bugsnag/bugsnag-laravel/issues/new)


## Contributing

All contributors are welcome! For information on how to build, test, and release, see our [contributing guide](CONTRIBUTING.md).


## License

The Bugsnag Laravel library is free software released under the MIT License. See [LICENSE.txt](LICENSE.txt) for details.
