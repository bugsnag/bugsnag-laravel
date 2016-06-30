<?php

namespace Bugsnag\BugsnagLaravel\Middleware;

use Bugsnag\Error;
use Exception;

class AddUserData
{
    /**
     * The user resolver.
     *
     * @var callable
     */
    protected $resolver;

    /**
     * Create a new user data middleware instance.
     *
     * @param callable $resolver the user resolver
     *
     * @return void
     */
    public function __construct(callable $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Execute the add user data middleware.
     *
     * @param \Bugsnag\Error $error
     * @param callable       $next
     *
     * @return bool
     */
    public function __invoke(Error $error, callable $next)
    {
        $resolver = $this->resolver;

        try {
            if ($user = $resolver()) {
                $error->setUser($user);
            }
        } catch (Exception $e) {
            // Ignore any errors.
        }

        return $next($error);
    }
}
