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
COPY --from=composer:2.2 /usr/bin/composer /usr/local/bin/composer

RUN composer install

CMD php -S 0.0.0.0:8000 -t public
