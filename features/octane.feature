Feature: Handled exceptions from routing support for Octane

Scenario: Octane - Handled exceptions are delivered from routing
  When I start the service "laravelfp"
  And I wait for the host "localhost" to open port "61311"  # must be the same as laravel11
  When I navigate to the route "/handled_exception"
  Then I wait to receive an error
  And the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" equals "Exception"
  And the exception "message" starts with "Handled exception!"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /handled_exception"
  And the event "severity" equals "warning"
  And the event "unhandled" is false
  And the event "severityReason.type" equals "handledException"

Scenario: Octane - Handled errors are delivered from routing
  When I start the service "laravelfp"
  And I wait for the host "localhost" to open port "61311"
  When I navigate to the route "/handled_error"
  Then I wait to receive an error
  And the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" equals "Handled Error"
  And the exception "message" equals "This is a handled error"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /handled_error"
  And the event "severity" equals "warning"
  And the event "unhandled" is false
  And the event "severityReason.type" equals "handledError"

@requires-sessions
Scenario: Octane - Sessions are correct in handled exceptions from routing
  Given I enable session tracking
  When I start the service "laravelfp"
  And I wait for the host "localhost" to open port "61311"
  When I navigate to the route "/handled_exception"
  And I wait to receive a session
  Then the session is valid for the session reporting API version "1.0" for the "Bugsnag Laravel" notifier
  When I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the error payload field "events.0.session.events.unhandled" equals 0
  And the error payload field "events.0.session.events.handled" equals 1

Scenario: Octane - Handled exceptions are delivered from views
  When I start the service "laravelfp"
  And I wait for the host "localhost" to open port "61311"
  When I navigate to the route "/handled_view_exception"
  Then I wait to receive an error
  And the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" equals "Exception"
  And the exception "message" starts with "Handled view exception"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /handled_view_exception"
  And the event "severity" equals "warning"
  And the event "unhandled" is false
  And the event "severityReason.type" equals "handledException"

Scenario: Octane - Handled errors are delivered from views
  When I start the service "laravelfp"
  And I wait for the host "localhost" to open port "61311"
  When I navigate to the route "/handled_view_error"
  Then I wait to receive an error
  And the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" equals "Handled error"
  And the exception "message" equals "This is a handled error"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /handled_view_error"
  And the event "severity" equals "warning"
  And the event "unhandled" is false
  And the event "severityReason.type" equals "handledError"

@requires-sessions
Scenario: Sessions are correct in Handled exceptions from views
  Given I enable session tracking
  When I start the service "laravelfp"
  And I wait for the host "localhost" to open port "61311"
  When I navigate to the route "/handled_view_exception"
  And I wait to receive a session
  Then the session is valid for the session reporting API version "1.0" for the "Bugsnag Laravel" notifier
  When I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the error payload field "events.0.session.events.unhandled" equals 0
  And the error payload field "events.0.session.events.handled" equals 1
