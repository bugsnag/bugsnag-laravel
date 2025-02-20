<?php
 
namespace Bugsnag\BugsnagLaravel;
 
use Illuminate\Contracts\Container\Container;
use Illuminate\Events\Dispatcher;
use Laravel\Octane\Events\RequestTerminated;
use Laravel\Octane\Events\TaskTerminated;
use Laravel\Octane\Events\WorkerStopping;
use Bugsnag\BugsnagLaravel\Queue\Tracker;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
 
class OctaneEventSubscriber
{
    /**
     * Reset all data between requests, tasks and worker resets in Octane.
     *
     * @return void
     */
    protected function cleanup()
    {
        Bugsnag::flush();
        Bugsnag::clearBreadcrumbs();
        Bugsnag::clearFeatureFlags();
        // Reset metadata
        // Bugsnag's default values are set in a report callback
        // so it will be filled again on the next report
        Bugsnag::setMetaData([], false);
    }

    public function handleRequestTerminated(RequestTerminated $event): void
    {
        $this->cleanup();
    }

    public function handleTaskTerminated(TaskTerminated $event): void
    {
        $this->cleanup();
    }

    public function handleWorkerStopping(WorkerStopping $event): void
    {
        $this->cleanup();
    }
 
    /**
     * Register the listeners for the subscriber.
     *
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            RequestTerminated::class => 'handleRequestTerminated',
            TaskTerminated::class => 'handleTaskTerminated',
            WorkerStopping::class => 'handleWorkerStopping',
        ];
    }
}