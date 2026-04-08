Contributing
============

-   [Fork](https://help.github.com/articles/fork-a-repo) the [notifier on github](https://github.com/bugsnag/bugsnag-laravel)
-   Build and test your changes using `make build` and `make test`
-   Commit and push until you are happy with your contribution
-   [Make a pull request](https://help.github.com/articles/using-pull-requests)
-   Thanks!

## End-to-end tests

These tests are implemented with our notifier testing tool [Maze Runner](https://github.com/bugsnag/maze-runner).

End-to-end tests are written in Cucumber-style `.feature` files, and need Ruby-backed "steps" in order to know what to run. The tests are located in the top level [`features`](/features/) directory.

Maze runner's CLI and the test fixtures are containerised so you'll need Docker (and Docker Compose) to run them.

### Running the end-to-end tests

Install Maze Runner:

```
$ bundle install
```

Configure the tests to be run in the following way:

- Determine the PHP and Laravel versions to be tested using the environment variables, e.g: 
  - `PHP_VERSION=7.4`
  - `LARAVEL_FIXTURE=laravel66`

Run the tests, for example:
```
PHP_VERSION=7.4 LARAVEL_FIXTURE=laravel66 bundle exec maze-runner
```

Releasing
=========

1. Create PRs updating the release version in the installation instructions on app.bugsnag.com and docs.bugsnag.com
2. Commit all outstanding changes
3. Bump the version in `src/BugsnagServiceProvider.php`
4. Update the CHANGELOG.md, and README if appropriate.
5. Commit, tag push
    ```
    git commit -am v2.x.x
    git tag v2.x.x
    git push origin master && git push --tags
    ```
5. Bump the branch alias to the next minor version
