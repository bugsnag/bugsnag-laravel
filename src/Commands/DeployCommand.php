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
    public function handle()
    {
        Bugsnag::deploy($this->option('repository'), $this->option('branch'), $this->option('revision'));

        $this->info('Notified Bugsnag of the deploy!');
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->handle();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['repository', null, InputOption::VALUE_OPTIONAL, 'The repository from which you are deploying the code.', null],
            ['branch', null, InputOption::VALUE_OPTIONAL, 'The source control branch from which you are deploying.', null],
            ['revision', null, InputOption::VALUE_OPTIONAL, 'The source control revision you are currently deploying.', null],
        ];
    }
}
