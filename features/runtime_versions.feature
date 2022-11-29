Feature: Reporting runtime versions

Scenario: report for handled event contains runtime version information
  Given I start the laravel fixture
  When I navigate to the route "/handled_exception"
  Then I wait to receive an error
  And the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the event "unhandled" is false
  And the event "device.runtimeVersions.php" matches "(\d+\.){2}\d+"
  And the event "device.runtimeVersions.laravel" matches the current major Laravel version
  And the event "device.runtimeVersions.lumen" matches the current major Lumen version

Scenario: report for unhandled event contains runtime version information
  Given I start the laravel fixture
  When I navigate to the route "/unhandled_exception"
  Then I wait to receive an error
  And the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the event "unhandled" is true
  And the event "device.runtimeVersions.php" matches "(\d+\.){2}\d+"
  And the event "device.runtimeVersions.laravel" matches the current major Laravel version
  And the event "device.runtimeVersions.lumen" matches the current major Lumen version

@requires-sessions
Scenario: session payload contains runtime version information
  Given I enable session tracking
  And I start the laravel fixture
  When I navigate to the route "/unhandled_controller_exception"
  And I wait to receive a session
  Then the session is valid for the session reporting API version "1.0" for the "Bugsnag Laravel" notifier
  And the session payload field "device.runtimeVersions.php" matches the regex "(\d+\.){2}\d+"
  And the session payload field "device.runtimeVersions.laravel" matches the current major Laravel version
  And the session payload field "device.runtimeVersions.lumen" matches the current major Lumen version
  When I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the error payload field "events.0.session.events.unhandled" equals 1
  And the error payload field "events.0.session.events.handled" equals 0
  And the event "device.runtimeVersions.php" matches "(\d+\.){2}\d+"
  And the event "device.runtimeVersions.laravel" matches the current major Laravel version
  And the event "device.runtimeVersions.lumen" matches the current major Lumen version
