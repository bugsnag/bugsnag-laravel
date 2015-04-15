<?php namespace Bugsnag\BugsnagLaravel;

trait BugsnagExceptionTrait {
    /**
     * Report exception to Bugsnag.
     *
     * @param \Exception $e
     */
    protected function reportToBugsnag(Exception $e)
    {
        $shouldReport = true;
        foreach ($this->dontReport as $type)
        {
            if ($e instanceof $type)
                $shouldReport = false;
        }

        global $app;

        if ($app->bound('bugsnag') && $shouldReport)
        {
            $app->make('bugsnag')->notifyException($e, null, 'error');
        }
    }
}
