Feature: Unhandled exceptions support

Scenario:
  Given I set environment variable "BUGSNAG_API_KEY" to "a35a2a72bd230ac0aa0f52715bbdc6aa"
  And I configure the bugsnag endpoint
  And I have built the service "laravel"
  And I start the service "laravel"
  And I wait for the app to respond on port "61280"
  When I navigate to the route "/unhandled" on port "61280"
  Then I should receive a request
  And the request is a valid for the error reporting API
  And the request contained the api key "a35a2a72bd230ac0aa0f52715bbdc6aa"
  And the payload field "events" is an array with 1 element
  And the exception "errorClass" equals "Exception"
  And the exception "message" starts with "Crashing exception!"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /unhandled"
  And the event "severity" equals "error"
  And the event "unhandled" is false
  And the event "severityReason.type" equals "log"
  And the event "severityReason.attributes.level" equals "error"