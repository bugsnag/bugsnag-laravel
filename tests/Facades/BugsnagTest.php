<?php

namespace Bugsnag\BugsnagLaravel\Tests\Facades;

use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Bugsnag\BugsnagLaravel\Tests\AbstractTestCase;
use Bugsnag\Client;
use GrahamCampbell\TestBenchCore\FacadeTrait;

class BugsnagTest extends AbstractTestCase
{
    use FacadeTrait;

    /**
     * Get the facade accessor.
     *
     * @return string
     */
    protected function getFacadeAccessor()
    {
        return 'bugsnag';
    }

    /**
     * Get the facade class.
     *
     * @return string
     */
    protected function getFacadeClass()
    {
        return Bugsnag::class;
    }

    /**
     * Get the facade root.
     *
     * @return string
     */
    protected function getFacadeRoot()
    {
        return Client::class;
    }
}
