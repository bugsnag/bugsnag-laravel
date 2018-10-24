Feature: Unhandled exceptions support

Scenario:
  Given I set environment variable "BUGSNAG_API_KEY" to "a35a2a72bd230ac0aa0f52715bbdc6aa"
  #And I configure the bugsnag endpoint
  And I start the service "laravel"
  And I wait for the app to respond on port "61280"
  When I navigate to the route "/unhandled" on port "61280"
  Then I should receive a request
  And the request is a valid for the error reporting API
  And the request used the Laravel notifier
  And the request contained the api key "a35a2a72bd230ac0aa0f52715bbdc6aa"
  And the payload field "events" is an array with 1 element
