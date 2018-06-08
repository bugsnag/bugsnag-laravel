Feature: Add appropriate unhandled values to payload

Background:
  Given I set environment variable "BUGSNAG_API_KEY" to "a35a2a72bd230ac0aa0f52715bbdc6aa"
  And I configure the bugsnag endpoint

Scenario Outline: Unhandled-handled middleware
  Given I start the service "laravel"
  And I wait for the app to respond on port "62123"
  When I navigate to the route "<route>" on port "62123"
  Then I should receive a request
  And the request is valid for the error reporting API
  And the request used the Laravel notifier
  And the request contained the api key "a35a2a72bd230ac0aa0f52715bbdc6aa"
  And the payload field "events" is an array with 1 element
  And the event "unhandled" is <unhandled>
  And the exception "errorClass" equals "Exception"

  Examples:
    | route      | unhandled |
    | /unhandled | true      |
    | /handled   | false     |

