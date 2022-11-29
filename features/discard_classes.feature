Feature: Discard classes

Scenario: Exceptions can be discarded by name
  Given I set environment variable "BUGSNAG_DISCARD_CLASSES" to "Exception"
  And I start the laravel fixture
  When I navigate to the route "/unhandled_controller_exception"
  Then I should receive no requests

Scenario: Exceptions can be discarded by regex
  Given I set environment variable "BUGSNAG_DISCARD_CLASSES" to "/Exception$/"
  And I start the laravel fixture
  When I navigate to the route "/unhandled_controller_exception"
  Then I should receive no requests

Scenario: Exceptions will be delivered when discard classes does not match
  Given I set environment variable "BUGSNAG_DISCARD_CLASSES" to "DifferentException,/^NotThatException$/"
  And I start the laravel fixture
  When I navigate to the route "/unhandled_controller_exception"
  Then I wait to receive an error
  And the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" equals "Exception"
  And the exception "message" starts with "Crashing exception!"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /unhandled_controller_exception"
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Laravel"
