ARG PHP_VERSION
FROM php:$PHP_VERSION

RUN apt-get update && \
  apt-get install -y --no-install-recommends \
  git \
  unzip \
  wget \
  zip

WORKDIR /app

COPY . .

RUN ./install_composer.sh

RUN php composer.phar install --prefer-dist

RUN php artisan key:generate

CMD php -S 0.0.0.0:8000 -t public
