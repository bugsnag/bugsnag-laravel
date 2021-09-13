Feature: Reporting runtime versions

@not-lumen
Scenario: report for handled event contains runtime version information
  Given I start the laravel fixture
  When I navigate to the route "/handled_exception"
  Then I wait to receive a request
  And the request is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the event "unhandled" is false
  And the event "device.runtimeVersions.php" matches "(\d+\.){2}\d+"
  And the event "device.runtimeVersions.laravel" matches "((\d+\.){2}\d+|\d\.x-dev)"

@not-lumen
Scenario: report for unhandled event contains runtime version information
  Given I start the laravel fixture
  When I navigate to the route "/unhandled_exception"
  Then I wait to receive a request
  And the request is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the event "unhandled" is true
  And the event "device.runtimeVersions.php" matches "(\d+\.){2}\d+"
  And the event "device.runtimeVersions.laravel" matches "((\d+\.){2}\d+|\d\.x-dev)"

@not-lumen
Scenario: session payload contains runtime version information
  Given I enable session tracking
  And I start the laravel fixture
  When I navigate to the route "/unhandled_controller_exception"
  And I wait to receive 2 requests
  Then the request is valid for the session reporting API version "1.0" for the "Bugsnag Laravel" notifier
  And the payload field "device.runtimeVersions.php" matches the regex "(\d+\.){2}\d+"
  And the payload field "device.runtimeVersions.laravel" matches the regex "((\d+\.){2}\d+|\d\.x-dev)"
  When I discard the oldest request
  Then the request is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the payload field "events.0.session.events.unhandled" equals 1
  And the payload field "events.0.session.events.handled" equals 0
  And the event "device.runtimeVersions.php" matches "(\d+\.){2}\d+"
  And the event "device.runtimeVersions.laravel" matches "((\d+\.){2}\d+|\d\.x-dev)"
