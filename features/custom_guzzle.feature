Feature: A custom Guzzle client can be used

Scenario: A custom Guzzle client can be used
  Given I set environment variable "BUGSNAG_USE_CUSTOM_GUZZLE" to "true"
  And I start the laravel fixture
  When I navigate to the route "/unhandled_exception"
  And I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the error "X-Custom-Guzzle" header equals "yes"

@requires-sessions
Scenario: A custom Guzzle client can be used for sessions
  Given I enable session tracking
  And I set environment variable "BUGSNAG_USE_CUSTOM_GUZZLE" to "true"
  And I start the laravel fixture
  When I navigate to the route "/unhandled_exception"
  And I wait to receive a session
  Then the session is valid for the session reporting API version "1.0" for the "Bugsnag Laravel" notifier
  And the session "X-Custom-Guzzle" header equals "yes"
  When I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the error "X-Custom-Guzzle" header equals "yes"
