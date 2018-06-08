rm -rf features/fixtures/laravel/vendor/
rm -f features/fixtures/laravel/composer.lock
(cd features/fixtures/laravel && composer install --prefer-source)