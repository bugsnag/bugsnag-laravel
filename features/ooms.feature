Feature: Out of memory error support

Scenario: Big OOM with the OOM bootstrapper
  Given I set environment variable "BUGSNAG_REGISTER_OOM_BOOTSTRAPPER" to "true"
  And I start the laravel fixture
  When I navigate to the route "/oom/big"
  Then the Laravel response matches "Allowed memory size of \d+ bytes exhausted \(tried to allocate \d+ bytes\)"
  When I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" matches one of the following:
    | Symfony\Component\ErrorHandler\Error\FatalError       |
    | Symfony\Component\Debug\Exception\FatalErrorException |
  And the exception "message" matches "Allowed memory size of \d+ bytes exhausted \(tried to allocate \d+ bytes\)"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /oom/big"
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Laravel"

Scenario: Small OOM with the OOM bootstrapper
  Given I set environment variable "BUGSNAG_REGISTER_OOM_BOOTSTRAPPER" to "true"
  And I start the laravel fixture
  When I navigate to the route "/oom/small"
  Then the Laravel response matches "Allowed memory size of \d+ bytes exhausted \(tried to allocate \d+ bytes\)"
  When I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
    And the exception "errorClass" matches one of the following:
    | Symfony\Component\ErrorHandler\Error\FatalError       |
    | Symfony\Component\Debug\Exception\FatalErrorException |
  And the exception "message" matches "Allowed memory size of \d+ bytes exhausted \(tried to allocate \d+ bytes\)"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /oom/small"
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Laravel"
