FROM php:latest

RUN apt-get -y update && apt-get -y upgrade

RUN mkdir /app
WORKDIR /app

COPY . /app/

CMD php artisan serve --port=8000 --host=0.0.0.0
