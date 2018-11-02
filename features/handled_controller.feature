Feature: Handled exceptions in controllers support

Scenario: Handled exceptions are delivered from controllers
  Given I set environment variable "BUGSNAG_API_KEY" to "a35a2a72bd230ac0aa0f52715bbdc6aa"
  And I configure the bugsnag endpoint
  And I start the service "laravel"
  And I wait for the app to respond on port "61280"
  When I navigate to the route "/handled_controller_exception" on port "61280"
  And I wait for 1 second
  Then I should receive a request
  And the request is a valid for the error reporting API
  And the request contained the api key "a35a2a72bd230ac0aa0f52715bbdc6aa"
  And the payload field "events" is an array with 1 element
  And the exception "errorClass" equals "Exception"
  And the exception "message" starts with "Handled exception"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /handled_controller_exception"
  And the event "severity" equals "warning"
  And the event "unhandled" is false
  And the event "severityReason.type" equals "handledException"

Scenario: Handled errors are delivered from controllers
  Given I set environment variable "BUGSNAG_API_KEY" to "a35a2a72bd230ac0aa0f52715bbdc6aa"
  And I configure the bugsnag endpoint
  And I start the service "laravel"
  And I wait for the app to respond on port "61280"
  When I navigate to the route "/handled_controller_error" on port "61280"
  And I wait for 1 second
  Then I should receive a request
  And the request is a valid for the error reporting API
  And the request contained the api key "a35a2a72bd230ac0aa0f52715bbdc6aa"
  And the payload field "events" is an array with 1 element
  And the exception "errorClass" ends with "Handled error"
  And the exception "message" equals "This is a handled error"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /handled_controller_error"
  And the event "severity" equals "warning"
  And the event "unhandled" is false
  And the event "severityReason.type" equals "handledError"

Scenario: Sessions are correct in handled exceptions from controllers
  Given I set environment variable "BUGSNAG_API_KEY" to "a35a2a72bd230ac0aa0f52715bbdc6aa"
  And I configure the bugsnag endpoint
  And I enable session tracking
  And I start the service "laravel"
  And I wait for the app to respond on port "61280"
  When I navigate to the route "/handled_controller_exception" on port "61280"
  And I wait for 1 second
  Then I should receive 2 requests
  And the request 0 is valid for the session tracking API
  And the request 1 is a valid for the error reporting API
  And the payload has a valid sessions array for request 0
  And the payload field "events.0.session.events.unhandled" equals 0 for request 1
  And the payload field "events.0.session.events.handled" equals 1 for request 1