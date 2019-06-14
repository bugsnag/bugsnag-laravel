Feature: Reporting runtime versions

Background:
  Given I set environment variable "BUGSNAG_API_KEY" to "a35a2a72bd230ac0aa0f52715bbdc6aa"
  And I configure the bugsnag endpoint

Scenario: report for unhandled event contains runtime version information
  Given I set environment variable "BUGSNAG_API_KEY" to "a35a2a72bd230ac0aa0f52715bbdc6aa"
  And I configure the bugsnag endpoint
  And I start the laravel fixture
  And I wait for the app to respond on the appropriate port
  When I navigate to the route "/handled_exception"
  And I wait for 1 second
  Then I should receive a request
  And the request is a valid for the error reporting API
  And the event "unhandled" is false
  And the event "device.runtimeVersions.php" matches "(\d+\.){2}\d+"
  And the event "device.runtimeVersions.laravel" matches "(\d+\.){2}\d+"

Scenario: report for handled event contains runtime version information
  Given I set environment variable "BUGSNAG_API_KEY" to "a35a2a72bd230ac0aa0f52715bbdc6aa"
  And I configure the bugsnag endpoint
  And I start the laravel fixture
  And I wait for the app to respond on the appropriate port
  When I navigate to the route "/unhandled_exception"
  And I wait for 1 second
  Then I should receive a request
  And the request is a valid for the error reporting API
  And the event "unhandled" is true
  And the event "device.runtimeVersions.php" matches "(\d+\.){2}\d+"
  And the event "device.runtimeVersions.laravel" matches "(\d+\.){2}\d+"

Scenario: Sessions are correct in unhandled exceptions from controllers
  Given I set environment variable "BUGSNAG_API_KEY" to "a35a2a72bd230ac0aa0f52715bbdc6aa"
  And I configure the bugsnag endpoint
  And I enable session tracking
  And I start the laravel fixture
  And I wait for the app to respond on the appropriate port
  When I navigate to the route "/unhandled_controller_exception"
  And I wait for 1 second
  Then I should receive 2 requests
  And the request 0 is valid for the session tracking API
  And the payload has a valid sessions array for request 0
  And the payload field "device.runtimeVersions.php" matches the regex "(\d+\.){2}\d+"
  And the payload field "device.runtimeVersions.laravel" matches the regex "(\d+\.){2}\d+"
