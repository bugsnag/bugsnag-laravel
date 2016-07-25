<?php

namespace Bugsnag\BugsnagLaravel\Commands;

use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class DeployCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'bugsnag:deploy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifies Bugsnag of a deployment';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        Bugsnag::deploy($this->option('repository'), $this->option('branch'), $this->option('revision'));
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['repository', null, InputOption::VALUE_OPTIONAL, 'The desired namespace.', null],
            ['branch', null, InputOption::VALUE_OPTIONAL, 'The desired namespace.', null],
            ['revision', null, InputOption::VALUE_OPTIONAL, 'The desired namespace.', null],
        ];
    }
}
