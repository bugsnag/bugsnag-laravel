Feature: Queue support

Background:
  # disable automatic query breadcrumbs as we assert against the specific number
  # of breadcrumbs in these tests
  Given I set environment variable "BUGSNAG_QUERY" to "false"

@not-laravel-latest @not-lumen8
Scenario: Unhandled exceptions are delivered from queues when running the queue worker as a daemon
  Given I start the laravel fixture
  And I start the laravel queue worker
  When I navigate to the route "/queue/unhandled"
  And I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" equals "RuntimeException"
  And the exception "message" equals "uh oh :o"
  And on Laravel versions >= 5.2 the event "metaData.job.name" equals "Illuminate\Queue\CallQueuedHandler@call"
  And on Laravel versions >= 5.2 the event "metaData.job.queue" equals "default"
  And on Laravel versions >= 5.2 the event "metaData.job.attempts" equals 1
  And on Laravel versions >= 5.2 the event "metaData.job.connection" equals "database"
  And on Laravel versions >= 5.2 the event "metaData.job.resolved" equals "App\Jobs\UnhandledJob"
  And on Laravel versions >= 5.2 the event "app.type" equals "Queue"
  And on Laravel versions >= 5.2 the event "context" equals "App\Jobs\UnhandledJob"
  And on Laravel versions < 5.2 the event "metaData.job" is null
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Laravel"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "before"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "App\Jobs\UnhandledJob::handle"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "exceptionOccurred"
  And on Laravel versions >= 9.0 the event has 3 breadcrumbs

@not-laravel-latest @not-lumen8
Scenario: Unhandled exceptions are delivered from queued jobs with multiple attmpts when running the queue worker as a daemon
  Given I start the laravel fixture
  And I start the laravel queue worker with --tries=3
  When I navigate to the route "/queue/unhandled"
  And I wait to receive 3 errors

  # attempt 1
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" equals "RuntimeException"
  And the exception "message" equals "uh oh :o"
  And on Laravel versions >= 5.2 the event "metaData.job.name" equals "Illuminate\Queue\CallQueuedHandler@call"
  And on Laravel versions >= 5.2 the event "metaData.job.queue" equals "default"
  And on Laravel versions >= 5.2 the event "metaData.job.attempts" equals 1
  And on Laravel versions >= 5.2 the event "metaData.job.connection" equals "database"
  And on Laravel versions >= 5.2 the event "metaData.job.resolved" equals "App\Jobs\UnhandledJob"
  And on Laravel versions >= 5.2 the event "app.type" equals "Queue"
  And on Laravel versions >= 5.2 the event "context" equals "App\Jobs\UnhandledJob"
  And on Laravel versions < 5.2 the event "metaData.job" is null
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Laravel"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "before"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "App\Jobs\UnhandledJob::handle"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "exceptionOccurred"
  And on Laravel versions >= 9.0 the event has 3 breadcrumbs

  # attempt 2
  When I discard the oldest error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" equals "RuntimeException"
  And the exception "message" equals "uh oh :o"
  And on Laravel versions >= 5.2 the event "metaData.job.name" equals "Illuminate\Queue\CallQueuedHandler@call"
  And on Laravel versions >= 5.2 the event "metaData.job.queue" equals "default"
  And on Laravel versions >= 5.2 the event "metaData.job.attempts" equals 2
  And on Laravel versions >= 5.2 the event "metaData.job.connection" equals "database"
  And on Laravel versions >= 5.2 the event "metaData.job.resolved" equals "App\Jobs\UnhandledJob"
  And on Laravel versions >= 5.2 the event "app.type" equals "Queue"
  And on Laravel versions >= 5.2 the event "context" equals "App\Jobs\UnhandledJob"
  And on Laravel versions < 5.2 the event "metaData.job" is null
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Laravel"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "before"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "App\Jobs\UnhandledJob::handle"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "exceptionOccurred"
  And on Laravel versions >= 9.0 the event has 3 breadcrumbs

  # attempt 3
  When I discard the oldest error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" equals "RuntimeException"
  And the exception "message" equals "uh oh :o"
  And on Laravel versions >= 5.2 the event "metaData.job.name" equals "Illuminate\Queue\CallQueuedHandler@call"
  And on Laravel versions >= 5.2 the event "metaData.job.queue" equals "default"
  And on Laravel versions >= 5.2 the event "metaData.job.attempts" equals 3
  And on Laravel versions >= 5.2 the event "metaData.job.connection" equals "database"
  And on Laravel versions >= 5.2 the event "metaData.job.resolved" equals "App\Jobs\UnhandledJob"
  And on Laravel versions >= 5.2 the event "app.type" equals "Queue"
  And on Laravel versions >= 5.2 the event "context" equals "App\Jobs\UnhandledJob"
  And on Laravel versions < 5.2 the event "metaData.job" is null
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Laravel"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "before"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "App\Jobs\UnhandledJob::handle"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "exceptionOccurred"
  And on Laravel versions >= 9.0 the event has 3 breadcrumbs

@not-laravel-latest @not-lumen8
Scenario: Handled exceptions are delivered from queues when running the queue worker as a daemon
  Given I start the laravel fixture
  And I start the laravel queue worker
  When I navigate to the route "/queue/handled"
  And I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" equals "Exception"
  And the exception "message" equals "Handled :)"
  And on Laravel versions >= 5.2 the event "metaData.job.name" equals "Illuminate\Queue\CallQueuedHandler@call"
  And on Laravel versions >= 5.2 the event "metaData.job.queue" equals "default"
  And on Laravel versions >= 5.2 the event "metaData.job.attempts" equals 1
  And on Laravel versions >= 5.2 the event "metaData.job.connection" equals "database"
  And on Laravel versions >= 5.2 the event "metaData.job.resolved" equals "App\Jobs\HandledJob"
  And on Laravel versions >= 5.2 the event "app.type" equals "Queue"
  And on Laravel versions >= 5.2 the event "context" equals "App\Jobs\HandledJob"
  And on Laravel versions < 5.2 the event "metaData.job" is null
  And the event "severity" equals "warning"
  And the event "unhandled" is false
  And the event "severityReason.type" equals "handledException"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "before"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "App\Jobs\HandledJob::handle"
  And on Laravel versions >= 9.0 the event has 2 breadcrumbs

@not-laravel-latest @not-lumen8
Scenario: Unhandled exceptions are delivered from queues when running the queue worker once
  Given I start the laravel fixture
  When I navigate to the route "/queue/unhandled"
  Then I should receive no errors
  When I run the laravel queue worker
  And I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" equals "RuntimeException"
  And the exception "message" equals "uh oh :o"
  And on Laravel versions >= 5.2 the event "metaData.job.name" equals "Illuminate\Queue\CallQueuedHandler@call"
  And on Laravel versions >= 5.2 the event "metaData.job.queue" equals "default"
  And on Laravel versions >= 5.2 the event "metaData.job.attempts" equals 1
  And on Laravel versions >= 5.2 the event "metaData.job.connection" equals "database"
  And on Laravel versions >= 5.2 the event "metaData.job.resolved" equals "App\Jobs\UnhandledJob"
  And on Laravel versions >= 5.2 the event "app.type" equals "Queue"
  And on Laravel versions >= 5.2 the event "context" equals "App\Jobs\UnhandledJob"
  And on Laravel versions < 5.2 the event "metaData.job" is null
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Laravel"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "App\Providers\AppServiceProvider::boot"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "before"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "App\Jobs\UnhandledJob::handle"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "exceptionOccurred"
  And on Laravel versions >= 9.0 the event has 4 breadcrumbs

@not-laravel-latest @not-lumen8
Scenario: Unhandled exceptions are delivered from queued jobs with multiple attmpts when running the queue worker once
  Given I start the laravel fixture
  When I navigate to the route "/queue/unhandled"
  Then I should receive no errors

  # attempt 1
  When I run the laravel queue worker with --tries=3
  And I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" equals "RuntimeException"
  And the exception "message" equals "uh oh :o"
  And on Laravel versions >= 5.2 the event "metaData.job.name" equals "Illuminate\Queue\CallQueuedHandler@call"
  And on Laravel versions >= 5.2 the event "metaData.job.queue" equals "default"
  And on Laravel versions >= 5.2 the event "metaData.job.attempts" equals 1
  And on Laravel versions >= 5.2 the event "metaData.job.connection" equals "database"
  And on Laravel versions >= 5.2 the event "metaData.job.resolved" equals "App\Jobs\UnhandledJob"
  And on Laravel versions >= 5.2 the event "app.type" equals "Queue"
  And on Laravel versions >= 5.2 the event "context" equals "App\Jobs\UnhandledJob"
  And on Laravel versions < 5.2 the event "metaData.job" is null
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Laravel"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "App\Providers\AppServiceProvider::boot"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "before"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "App\Jobs\UnhandledJob::handle"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "exceptionOccurred"
  And on Laravel versions >= 9.0 the event has 4 breadcrumbs

  # attempt 2
  When I discard the oldest error
  And I run the laravel queue worker with --tries=3
  And I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" equals "RuntimeException"
  And the exception "message" equals "uh oh :o"
  And on Laravel versions >= 5.2 the event "metaData.job.name" equals "Illuminate\Queue\CallQueuedHandler@call"
  And on Laravel versions >= 5.2 the event "metaData.job.queue" equals "default"
  And on Laravel versions >= 5.2 the event "metaData.job.attempts" equals 2
  And on Laravel versions >= 5.2 the event "metaData.job.connection" equals "database"
  And on Laravel versions >= 5.2 the event "metaData.job.resolved" equals "App\Jobs\UnhandledJob"
  And on Laravel versions >= 5.2 the event "app.type" equals "Queue"
  And on Laravel versions >= 5.2 the event "context" equals "App\Jobs\UnhandledJob"
  And on Laravel versions < 5.2 the event "metaData.job" is null
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Laravel"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "App\Providers\AppServiceProvider::boot"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "before"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "App\Jobs\UnhandledJob::handle"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "exceptionOccurred"
  And on Laravel versions >= 9.0 the event has 4 breadcrumbs

  # attempt 3
  When I discard the oldest error
  And I run the laravel queue worker with --tries=3
  And I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" equals "RuntimeException"
  And the exception "message" equals "uh oh :o"
  And on Laravel versions >= 5.2 the event "metaData.job.name" equals "Illuminate\Queue\CallQueuedHandler@call"
  And on Laravel versions >= 5.2 the event "metaData.job.queue" equals "default"
  And on Laravel versions >= 5.2 the event "metaData.job.attempts" equals 3
  And on Laravel versions >= 5.2 the event "metaData.job.connection" equals "database"
  And on Laravel versions >= 5.2 the event "metaData.job.resolved" equals "App\Jobs\UnhandledJob"
  And on Laravel versions >= 5.2 the event "app.type" equals "Queue"
  And on Laravel versions >= 5.2 the event "context" equals "App\Jobs\UnhandledJob"
  And on Laravel versions < 5.2 the event "metaData.job" is null
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Laravel"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "App\Providers\AppServiceProvider::boot"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "before"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "App\Jobs\UnhandledJob::handle"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "exceptionOccurred"
  And on Laravel versions >= 9.0 the event has 4 breadcrumbs

@not-laravel-latest @not-lumen8
Scenario: Handled exceptions are delivered from queues when running the queue worker once
  Given I start the laravel fixture
  When I navigate to the route "/queue/handled"
  Then I should receive no errors
  When I run the laravel queue worker
  And I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Laravel" notifier
  And the exception "errorClass" equals "Exception"
  And the exception "message" equals "Handled :)"
  And on Laravel versions >= 5.2 the event "metaData.job.name" equals "Illuminate\Queue\CallQueuedHandler@call"
  And on Laravel versions >= 5.2 the event "metaData.job.queue" equals "default"
  And on Laravel versions >= 5.2 the event "metaData.job.attempts" equals 1
  And on Laravel versions >= 5.2 the event "metaData.job.connection" equals "database"
  And on Laravel versions >= 5.2 the event "metaData.job.resolved" equals "App\Jobs\HandledJob"
  And on Laravel versions >= 5.2 the event "app.type" equals "Queue"
  And on Laravel versions >= 5.2 the event "context" equals "App\Jobs\HandledJob"
  And on Laravel versions < 5.2 the event "metaData.job" is null
  And the event "severity" equals "warning"
  And the event "unhandled" is false
  And the event "severityReason.type" equals "handledException"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "App\Providers\AppServiceProvider::boot"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "before"
  And on Laravel versions >= 9.0 the event has a "manual" breadcrumb named "App\Jobs\HandledJob::handle"
  And on Laravel versions >= 9.0 the event has 3 breadcrumbs
