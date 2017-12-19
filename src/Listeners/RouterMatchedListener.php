<?php

namespace Bugsnag\BugsnagLaravel\Listeners;

use Illuminate\Routing\Events;

class RouterMatchedListener
{
    public function onRouteMatched($event)
    {
        app()['bugsnag']->getSessionTracker()->createSession();
    }

    public function subscribe($events)
    {
        $events->listen(
            Events\RouteMatched::class,
            'Bugsnag\BugsnagLaravel\Listeners\RouterMatchedListener@onRouteMatched'
        );
    }
}