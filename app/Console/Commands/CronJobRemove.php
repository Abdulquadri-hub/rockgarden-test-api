<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CronJobRemove extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-invoice:remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       $this->call('schedule:list');

    $this->info('Removing cron job...');
    $this->call('schedule:clear');
    $this->info('Cron job removed.');
    }
}
