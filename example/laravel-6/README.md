# Bugsnag Laravel 6 demo

This Laravel application demonstrates how to use Bugsnag with version 6 of the Laravel web framework for PHP.

## Setup

Try this out with [your own Bugsnag account](https://app.bugsnag.com/user/new), and you'll be able to see how the errors are reported directly in the dashboard.

To get set up, follow the instructions below. Don't forget to replace the placeholder API token with your own!


1. Clone the repo and `cd` into this directory:
    ```shell
    git clone https://github.com/bugsnag/bugsnag-laravel.git
    cd bugsnag-laravel/example/laravel-6
    ```

1. Install dependencies
    ```shell
    composer install
    ```

1. Ensure that your Bugsnag API Key is set in the `.env` file within the application.  If you do not have a `.env` file present, copy the `.env.example` file to `.env`, and add the `BUGSNAG_API_KEY` environment variable, setting it to your API Key.

1. Run the application.
    ```shell
    php artisan serve
    ```

1. View the example page which will, by default, be served at: http://localhost:8000

For more information, see [our Laravel documentation](https://docs.bugsnag.com/platforms/php/laravel/).
