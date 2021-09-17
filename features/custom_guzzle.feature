Feature: A custom Guzzle client can be used

Scenario: A custom Guzzle client can be used
  Given I set environment variable "BUGSNAG_USE_CUSTOM_GUZZLE" to "true"
  And I start the laravel fixture
  When I navigate to the route "/unhandled_exception"
  And I wait to receive a request
  Then the request is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the "X-Custom-Guzzle" header equals "yes"

@requires-sessions
Scenario: A custom Guzzle client can be used for sessions
  Given I enable session tracking
  And I set environment variable "BUGSNAG_USE_CUSTOM_GUZZLE" to "true"
  And I start the laravel fixture
  When I navigate to the route "/unhandled_exception"
  And I wait to receive 2 requests
  Then the request is valid for the session reporting API version "1.0" for the "Bugsnag Laravel" notifier
  And the "X-Custom-Guzzle" header equals "yes"
  When I discard the oldest request
  Then the request is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the "X-Custom-Guzzle" header equals "yes"
