# Bugsnag Laravel 5.6 demo

This Laravel application demonstrates how to use Bugsnag with the Laravel web framework for PHP.

## Setup

Try this out with [your own Bugsnag account](https://app.bugsnag.com/user/new), and you'll be able to see how the errors are reported directly in the dashboard.

To get set up, follow the instructions below. Don't forget to replace the placeholder API token with your own!


1. Clone the repo and `cd` into this directory:
    ```shell
    git clone https://github.com/bugsnag/bugsnag-laravel.git
    cd bugsnag-laravel/example/laravel56
    ```

1. Install dependencies
    ```shell
    composer install
    ```

1. Ensure that your Bugsnag Api Key is set in the `.env` file within the application.  If you do not have a `.env` file present, move the `.env.example` file to `.env`, and add the `BUGSNAG_API_KEY` environment variable, setting it to your Api Key.

1. Run the application.
    ```shell
    php artisan serve
    ```

1. View the example page which will, by default, be served at: http://localhost:8000

For more information, see [our Laravel documentation](https://docs.bugsnag.com/platforms/php/laravel/).


## Running in Docker

1. As above, clone the repo and `cd` into this directory:
    ```shell
    git clone https://github.com/bugsnag/bugsnag-laravel.git
    cd bugsnag-laravel/example/laravel56
    ```

1. Then ensure that your Bugsnag Api Key is set in the `.env` file within the application.  If you do not have a `.env` file present, move the    `.env.example` file to `.env`, and add the `BUGSNAG_API_KEY` environment variable, setting it to your Api Key.

1. Build the container:
    ```shell
    docker build -t bugsnag-example-app .
    ```

1. Then start your newly built container, with a port forwarding from your local machine to port `8000` in the container, for example:

    ```shell
    docker run -d -p 8000:8000 bugsnag-example-app
    ```

1. View the example page which will, by default, be served at: http://localhost:8000