ARG PHP_VERSION
FROM php:$PHP_VERSION

# Only applicable to development environments running scaler
COPY .zscaler-root-ca.pem* /usr/local/share/ca-certificates/
RUN if [ -f /usr/local/share/ca-certificates/.zscaler-root-ca.pem ]; then cat /usr/local/share/ca-certificates/.zscaler-root-ca.pem >> /etc/ssl/certs/ca-certificates.crt; fi

RUN apt-get update && \
  apt-get install -y --no-install-recommends \
  git \
  unzip \
  wget \
  zip

WORKDIR /app

COPY . .
COPY --from=composer:2.2 /usr/bin/composer /usr/local/bin/composer

RUN composer install --no-dev
RUN php artisan key:generate

# create database & apply migrations
RUN touch database/database.sqlite && php artisan migrate --no-interaction

CMD php artisan serve --port=8000 --host=0.0.0.0
