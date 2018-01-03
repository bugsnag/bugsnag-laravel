<?php

namespace Bugsnag\BugsnagLaravel\Listeners;

use Illuminate\Routing\Events;

class RouteMatchedListener
{
    public function onRouteMatched($event)
    {
        app()['bugsnag']->getSessionTracker()->createSession();
    }

    public function subscribe($events)
    {
        $events->listen(
            Events\RouteMatched::class,
            'Bugsnag\BugsnagLaravel\Listeners\RouteMatchedListener@onRouteMatched'
        );
    }
}
