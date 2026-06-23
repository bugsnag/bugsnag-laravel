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
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

RUN cp .env.example .env
# Allow composer to install packages that have security advisories, since
# we're testing against older Laravel 11.x versions that are flagged
ENV COMPOSER_NO_SECURITY_BLOCKING=1
RUN composer install --no-dev
RUN php artisan key:generate

# create database & apply migrations
RUN touch database/database.sqlite && php artisan migrate --no-interaction

CMD php -S 0.0.0.0:8000 -t public
