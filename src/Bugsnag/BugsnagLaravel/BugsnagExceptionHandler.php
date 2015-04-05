<?php namespace Bugsnag\BugsnagLaravel;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class BugsnagExceptionHandler extends ExceptionHandler {

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        $this->reportToBugsnag($e);

        return parent::report($e);
    }

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
