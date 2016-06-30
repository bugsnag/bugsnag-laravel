Contributing
============

-   [Fork](https://help.github.com/articles/fork-a-repo) the [notifier on github](https://github.com/bugsnag/bugsnag-laravel)
-   Build and test your changes:
```
composer install && ./vendor/bin/phpunit
```

-   Commit and push until you are happy with your contribution
-   [Make a pull request](https://help.github.com/articles/using-pull-requests)
-   Thanks!


Releasing
=========

1. Commit all outstanding changes
2. Bump the version in `src/Bugsnag/BugsnagLaravel/BugsnagServiceProvider.php`
3. Update the CHANGELOG.md, and README if appropriate.
4. Commit, tag push

    git commit -am v1.x.x
    git tag v1.x.x
    git push origin master v1.x.x
5. Update the setup guide for Laravel on docs.bugsnag.com with any new content
