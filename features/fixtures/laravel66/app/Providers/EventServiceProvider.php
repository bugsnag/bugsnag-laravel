<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobExceptionOccurred;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Queue::before(function (JobProcessing $event) {
            Bugsnag::leaveBreadcrumb('before');
        });

        Queue::after(function (JobProcessed $event) {
            Bugsnag::leaveBreadcrumb('after');
        });

        Queue::exceptionOccurred(function (JobExceptionOccurred $event) {
            Bugsnag::leaveBreadcrumb('exceptionOccurred');
        });
    }
}
