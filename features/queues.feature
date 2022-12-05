Feature: Queue support

@not-laravel-latest @not-laravel51 @not-laravel56 @not-laravel58 @not-lumen8
Scenario: Unhandled exceptions are delivered from queues
  Given I start the laravel fixture
  And I start the laravel queue worker
  When I navigate to the route "/queue/unhandled"
  And I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" equals "RuntimeException"
  And the exception "message" equals "uh oh :o"
  And the event "metaData.job.name" equals "Illuminate\Queue\CallQueuedHandler@call"
  And the event "metaData.job.queue" equals "default"
  And the event "metaData.job.attempts" equals 1
  And the event "metaData.job.connection" equals "database"
  And the event "metaData.job.resolved" equals "App\Jobs\UnhandledJob"
  And the event "app.type" equals "Queue"
  And the event "context" equals "App\Jobs\UnhandledJob"
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Laravel"

@not-laravel-latest @not-laravel51 @not-laravel56 @not-laravel58 @not-lumen8
Scenario: Unhandled exceptions are delivered from queued jobs with multiple attmpts
  Given I start the laravel fixture
  And I start the laravel queue worker
  When I navigate to the route "/queue/unhandled?tries=3"
  And I wait to receive 3 errors

  # attempt 1
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" equals "RuntimeException"
  And the exception "message" equals "uh oh :o"
  And the event "metaData.job.name" equals "Illuminate\Queue\CallQueuedHandler@call"
  And the event "metaData.job.queue" equals "default"
  And the event "metaData.job.attempts" equals 1
  And the event "metaData.job.connection" equals "database"
  And the event "metaData.job.resolved" equals "App\Jobs\UnhandledJob"
  And the event "app.type" equals "Queue"
  And the event "context" equals "App\Jobs\UnhandledJob"
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Laravel"

  # attempt 2
  When I discard the oldest error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" equals "RuntimeException"
  And the exception "message" equals "uh oh :o"
  And the event "metaData.job.name" equals "Illuminate\Queue\CallQueuedHandler@call"
  And the event "metaData.job.queue" equals "default"
  And the event "metaData.job.attempts" equals 2
  And the event "metaData.job.connection" equals "database"
  And the event "metaData.job.resolved" equals "App\Jobs\UnhandledJob"
  And the event "app.type" equals "Queue"
  And the event "context" equals "App\Jobs\UnhandledJob"
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Laravel"

  # attempt 3
  When I discard the oldest error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" equals "RuntimeException"
  And the exception "message" equals "uh oh :o"
  And the event "metaData.job.name" equals "Illuminate\Queue\CallQueuedHandler@call"
  And the event "metaData.job.queue" equals "default"
  And the event "metaData.job.attempts" equals 3
  And the event "metaData.job.connection" equals "database"
  And the event "metaData.job.resolved" equals "App\Jobs\UnhandledJob"
  And the event "app.type" equals "Queue"
  And the event "context" equals "App\Jobs\UnhandledJob"
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Laravel"

@not-laravel-latest @not-laravel51 @not-laravel56 @not-laravel58 @not-lumen8
Scenario: Handled exceptions are delivered from queues
  Given I start the laravel fixture
  And I start the laravel queue worker
  When I navigate to the route "/queue/handled"
  And I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" equals "Exception"
  And the exception "message" equals "Handled :)"
  And the event "metaData.job.name" equals "Illuminate\Queue\CallQueuedHandler@call"
  And the event "metaData.job.queue" equals "default"
  And the event "metaData.job.attempts" equals 1
  And the event "metaData.job.connection" equals "database"
  And the event "metaData.job.resolved" equals "App\Jobs\HandledJob"
  And the event "app.type" equals "Queue"
  And the event "context" equals "App\Jobs\HandledJob"
  And the event "severity" equals "warning"
  And the event "unhandled" is false
  And the event "severityReason.type" equals "handledException"
