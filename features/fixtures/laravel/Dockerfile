FROM php:latest

RUN apt-get update && \
  apt-get install -y --no-install-recommends \
  git \
  unzip \
  wget \
  zip

WORKDIR /app

COPY . .

RUN ./install_composer.sh

RUN cd bugsnag-lib && php /app/composer.phar install 

RUN php composer.phar install --prefer-source

RUN php artisan key:generate

CMD php artisan serve --port=8000 --host=0.0.0.0
