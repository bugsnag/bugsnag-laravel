Feature: Unhandled exceptions for middleware support

Scenario: Unhandled exceptions are delivered from middleware
  Given I start the laravel fixture
  When I navigate to the route "/unhandled_middleware_exception"
  Then I wait to receive a request
  And the request is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" equals "Exception"
  And the exception "message" starts with "Unhandled middleware exception"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /unhandled_middleware_exception"
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Laravel"

Scenario: Unhandled errors are delivered from middleware
  Given I start the laravel fixture
  When I navigate to the route "/unhandled_middleware_error"
  Then I wait to receive a request
  And the request is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" ends with "Error"
  And the exception "message" starts with "Call to undefined function"
  And the exception "message" ends with "foo()"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /unhandled_middleware_error"
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Laravel"

Scenario: Sessions are correct in unhandled exceptions from middleware
  Given I enable session tracking
  And I start the laravel fixture
  When I navigate to the route "/unhandled_middleware_exception"
  And I wait to receive 2 requests
  Then the request is valid for the session reporting API version "1.0" for the "Bugsnag Laravel" notifier
  When I discard the oldest request
  Then the request is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the payload field "events.0.session.events.unhandled" equals 1
  And the payload field "events.0.session.events.handled" equals 0
