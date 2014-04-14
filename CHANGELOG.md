Changelog
=========

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
