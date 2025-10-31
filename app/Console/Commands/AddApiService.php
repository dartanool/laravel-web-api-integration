<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AddApiService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:api-service {name} {baseUrl}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $baseUrl = $this->argument('baseUrl')
    }
}
