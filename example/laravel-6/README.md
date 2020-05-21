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

1. Copy the `.env.example` file to `.env`
    ```shell
    cp .env.example .env
    ```

1. Set the `BUGSNAG_API_KEY` in `.env` to your Bugsnag project's API Key

1. Generate Laravel's application key
    ```shell
    php artisan key:generate
    ```

1. Run the application.
    ```shell
    php artisan serve
    ```

1. View the example page which will, by default, be served at: http://localhost:8000

For more information, see [our Laravel documentation](https://docs.bugsnag.com/platforms/php/laravel/).
