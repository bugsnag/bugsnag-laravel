Feature: Handled exceptions in controllers support

Scenario: Handled exceptions are delivered from controllers
  Given I start the laravel fixture
  When I navigate to the route "/handled_controller_exception"
  Then I wait to receive a request
  And the request is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" equals "Exception"
  And the exception "message" starts with "Handled exception"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /handled_controller_exception"
  And the event "severity" equals "warning"
  And the event "unhandled" is false
  And the event "severityReason.type" equals "handledException"

Scenario: Handled errors are delivered from controllers
  Given I start the laravel fixture
  When I navigate to the route "/handled_controller_error"
  Then I wait to receive a request
  And the request is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" ends with "Handled error"
  And the exception "message" equals "This is a handled error"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /handled_controller_error"
  And the event "severity" equals "warning"
  And the event "unhandled" is false
  And the event "severityReason.type" equals "handledError"

@not-lumen
Scenario: Sessions are correct in handled exceptions from controllers
  Given I enable session tracking
  And I start the laravel fixture
  When I navigate to the route "/handled_controller_exception"
  And I wait to receive 2 requests
  Then the request is valid for the session reporting API version "1.0" for the "Bugsnag Laravel" notifier
  When I discard the oldest request
  Then the request is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the payload field "events.0.session.events.unhandled" equals 0
  And the payload field "events.0.session.events.handled" equals 1
