<?php
 
namespace Bugsnag\BugsnagLaravel;
 
use Illuminate\Contracts\Container\Container;
use Illuminate\Events\Dispatcher;
use Laravel\Octane\Events\RequestHandled;
use Laravel\Octane\Events\RequestReceived;
use Laravel\Octane\Events\RequestTerminated;
use Laravel\Octane\Events\TaskReceived;
use Laravel\Octane\Events\TaskTerminated;
use Laravel\Octane\Events\TickReceived;
use Laravel\Octane\Events\TickTerminated;
use Laravel\Octane\Events\WorkerStarting;
use Laravel\Octane\Events\WorkerErrorOccurred;
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

    public function handleRequestHandled(RequestHandled $event) : void {
        Bugsnag::leaveBreadcrumb('Octane request handled');
    }
    public function handleRequestReceived(RequestReceived $event) : void {
        Bugsnag::leaveBreadcrumb('Octane request received');
    }
    public function handleRequestTerminated(RequestTerminated $event): void
    {
        Bugsnag::leaveBreadcrumb('Octane request terminated');
        $this->cleanup();
    }

    public function handleTaskReceived(TaskReceived $event) : void {
        Bugsnag::leaveBreadcrumb('Octane task received');
    }
    public function handleTaskTerminated(TaskTerminated $event): void
    {
        Bugsnag::leaveBreadcrumb('Octane task terminated');
        $this->cleanup();
    }

    public function handleTickReceived(TickReceived $event) : void {
        Bugsnag::leaveBreadcrumb('Octane tick received');
    }
    public function handleTickTerminated(TickTerminated $event) : void {
        Bugsnag::leaveBreadcrumb('Octane tick terminated');
    }

    public function handleWorkerStarting(WorkerStarting $event) : void {
        Bugsnag::leaveBreadcrumb('Octane worker starting');
    }
    public function handleWorkerErrorOccurred(WorkerErrorOccurred $event) : void {
        Bugsnag::leaveBreadcrumb('Octane worker error occurred');
    }
    public function handleWorkerStopping(WorkerStopping $event): void
    {
        Bugsnag::leaveBreadcrumb('Octane worker stopping');
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
            RequestHandled::class => 'handleRequestHandled',
            RequestReceived::class => 'handleRequestReceived',
            RequestTerminated::class => 'handleRequestTerminated',
            TaskReceived::class => 'handleTaskReceived',
            TaskTerminated::class => 'handleTaskTerminated',
            TickReceived::class => 'handleTickReceived',
            TickTerminated::class => 'handleTickTerminated',
            WorkerStarting::class => 'handleWorkerStarting',
            WorkerErrorOccurred::class => 'handleWorkerErrorOccurred',
            WorkerStopping::class => 'handleWorkerStopping',
        ];
    }
}