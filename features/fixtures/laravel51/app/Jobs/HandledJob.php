<?php

namespace App\Jobs;

use Exception;
use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;

class HandledJob extends Job implements SelfHandling, ShouldQueue
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Bugsnag::leaveBreadcrumb(__METHOD__);

        Bugsnag::notifyException(new Exception('Handled :)'));
    }
}
