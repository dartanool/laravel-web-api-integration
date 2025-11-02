<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateApiData extends Command
{
    /**
     * Имя и сигнатура Artisan-команды.
     *
     * @var string
     */
    protected $signature = 'update:api-data';
    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Выгрузка всех данных: orders, sales, incomes, stocks';
    /**
     * Выполнение команды.
     *
     * @return void
     */
    public function handle() : void
    {
        Log::info("Запуск выгрузки всех данных: " . now());

        $this->call('fetch:orders', [
            'dateFrom' => now()->subDay()->format('Y-m-d'),
            'dateTo' => now()->format('Y-m-d')
        ]);

        $this->call('fetch:sales', [
            'dateFrom' => now()->subDay()->format('Y-m-d'),
            'dateTo' => now()->format('Y-m-d')
        ]);

        $this->call('fetch:incomes', [
            'dateFrom' => now()->subDay()->format('Y-m-d'),
            'dateTo' => now()->format('Y-m-d')
        ]);

        $today = now()->format('Y-m-d');
        $this->call('fetch:stocks', [
            'dateFrom' => $today
        ]);

        Log::info("Выгрузка всех данных завершена: " . now());
    }
}
