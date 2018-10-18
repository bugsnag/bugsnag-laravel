FROM php:latest

RUN mkdir /app
WORKDIR /app

COPY . /app/

CMD php artisan serve --port=8000 --host=0.0.0.0
