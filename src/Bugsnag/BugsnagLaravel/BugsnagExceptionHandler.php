<?php namespace Bugsnag\BugsnagLaravel;

use App;
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
         $shouldReport = true;
         foreach ($this->dontReport as $type)
         {
             if ($e instanceof $type)
                 $shouldReport = false;
         }

         if ($shouldReport) {
            $bugsnag = App::make('bugsnag');
            $bugsnag->notifyException($e);
         }
         return parent::report($e);
    }
}
