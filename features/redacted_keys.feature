Feature: Redacted keys

Scenario: Keys won't be redacted with no redacted keys
  Given I start the laravel fixture
  When I navigate to the route "/unhandled_controller_exception"
  Then I wait to receive a request
  And the request is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "metaData.request.url" ends with "/unhandled_controller_exception"
  And the event "metaData.request.userAgent" equals "Ruby"

Scenario: Keys can be redacted from metadata
  Given I set environment variable "BUGSNAG_REDACTED_KEYS" to "HTTPmethod,/^url$/"
  And I start the laravel fixture
  When I navigate to the route "/unhandled_controller_exception"
  Then I wait to receive a request
  And the request is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the event "metaData.request.httpMethod" equals "[FILTERED]"
  And the event "metaData.request.url" equals "[FILTERED]"
  And the event "metaData.request.userAgent" equals "Ruby"
