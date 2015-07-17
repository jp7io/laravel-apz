<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class DbTruncate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:truncate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate tables on testing environment.';

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
     * @return mixed
     */
    public function handle()
    {
        if (!\App::environment('testing')) {
            echo 'Truncate aborted. You are not on a testing environment.';
            return;
        }
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

        foreach ($tables as $name) {
            //if you don't want to truncate migrations
            if ($name == 'migrations') {
                continue;
            }
            DB::table($name)->truncate();
        }
    }
}
