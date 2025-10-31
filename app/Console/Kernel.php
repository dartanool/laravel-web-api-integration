<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('update:api-data')->twiceDaily(6, 18)
            ->appendOutputTo(storage_path('logs/fetch_all.log'));

    }

}
