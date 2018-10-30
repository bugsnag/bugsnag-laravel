<?php

namespace App\Http\Controllers;

use Exception;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class TestController extends BaseController
{
    public function unhandledException()
    {
        throw new Exception('Crashing exception!');
    }

    public function unhandledError()
    {
        foo();
    }

    public function handledException()
    {
        Bugsnag::notifyException(new Exception('Handled exception'));
        return "done";
    }

    public function handledError()
    {
        Bugsnag::notifyError('Handled error', 'This is a handled error');
        return "done";
    }
}
