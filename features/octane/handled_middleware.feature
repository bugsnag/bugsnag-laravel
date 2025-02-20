Feature: Handled exceptions for middleware support

Scenario Outline: Handled exceptions are delivered from middleware
  When I start the service <octanesrv>
  And I wait for the host "localhost" to open port "61311"
  When I navigate to the route "/handled_middleware_exception"
  Then I wait to receive an error
  And the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" equals "Exception"
  And the exception "message" starts with "Handled middleware exception"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /handled_middleware_exception"
  And the event "severity" equals "warning"
  And the event "unhandled" is false
  And the event "severityReason.type" equals "handledException"

  Examples:
    | octanesrv   |
    | "laravelrr" |
    | "laravelfp" |
    | "laravelsw" |

Scenario Outline: Handled errors are delivered from middleware
  When I start the service <octanesrv>
  And I wait for the host "localhost" to open port "61311"
  When I navigate to the route "/handled_middleware_error"
  Then I wait to receive an error
  And the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" ends with "Handled middleware error"
  And the exception "message" equals "This is a handled error"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /handled_middleware_error"
  And the event "severity" equals "warning"
  And the event "unhandled" is false
  And the event "severityReason.type" equals "handledError"

  Examples:
    | octanesrv   |
    | "laravelrr" |
    | "laravelfp" |
    | "laravelsw" |

@requires-sessions
Scenario Outline: Sessions are correct in handled exceptions from middleware
  Given I enable session tracking
  When I start the service <octanesrv>
  And I wait for the host "localhost" to open port "61311"
  When I navigate to the route "/handled_middleware_exception"
  And I wait to receive a session
  Then the session is valid for the session reporting API version "1.0" for the "Bugsnag Laravel" notifier
  When I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the error payload field "events.0.session.events.unhandled" equals 0
  And the error payload field "events.0.session.events.handled" equals 1

  Examples:
    | octanesrv   |
    | "laravelrr" |
    | "laravelfp" |
    | "laravelsw" |
