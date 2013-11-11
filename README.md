Bugsnag Notifier for Laravel
============================

The Bugsnag Notifier for Laravel gives you instant notification of errors and
exceptions in your Laravel PHP applications.

[Bugsnag](https://bugsnag.com) captures errors in real-time from your web, 
mobile and desktop applications, helping you to understand and resolve them 
as fast as possible. [Create a free account](https://bugsnag.com) to start 
capturing errors from your applications.


How to Install
--------------

### Using [Composer](http://getcomposer.org/) (Recommended)

1.  Add `bugsnag/bugsnag-laravel` to your `composer.json` requirements

    ```json
    "require": {
        "bugsnag/bugsnag-laravel": "1.*"
    }
    ```

2.  Install the package

    ```shell
    $ composer install
    ```

3.  Generate a template Bugsnag config file

    ```shell
    $ php artisan config:publish bugsnag/bugsnag-laravel
    ```

4.  Update `app/config/packages/bugsnag/bugsnag-laravel/config.php` with your
    Bugsnag API key:

    ```php
    return array(
        'api_key' => 'YOUR-API-KEY-HERE'
    );
    ```

5.  Finally update `app/config/app.php` and add a new item to the providers array:

    ```
    'Bugsnag\BugsnagLaravel\BugsnagLaravelServiceProvider'
    ```


Configuration
-------------

The [Bugsnag PHP Client](https://bugsnag.com/docs/notifiers/php)
is available as `$app['bugsnag']`, which allows you to set various
configuration options, for example:

```php
$app['bugsnag']->setReleaseStage("production");
```

See the [Bugsnag Notifier for PHP documentation](https://bugsnag.com/docs/notifiers/php#additional-configuration)
for full configuration details.
