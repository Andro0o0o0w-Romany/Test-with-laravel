<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FetchRandomUser;

class FetchRandomUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:random-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch random user data and log the results object';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        FetchRandomUser::dispatch();
        $this->info('FetchRandomUser job dispatched successfully.');
        return 0;
    }
}
