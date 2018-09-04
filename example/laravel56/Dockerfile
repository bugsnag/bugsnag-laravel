FROM php:latest

RUN apt-get update && \
  apt-get install -y --no-install-recommends git zip unzip wget

RUN mkdir /app
WORKDIR /app

COPY . /app/

RUN /app/install_composer.sh

RUN cd /app/ && \
  php composer.phar install

CMD php artisan serve --port=8000 --host=0.0.0.0
