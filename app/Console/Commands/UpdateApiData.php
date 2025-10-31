<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateApiData extends Command
{
    protected $signature = 'update:api-data';

    protected $description = 'Выгрузка всех данных: orders, sales, incomes, stocks';

    public function handle()
    {
        $this->info("Запуск выгрузки всех данных: " . now());

        // 1️⃣ Заказы
        $this->call('fetch:orders', [
            'dateFrom' => now()->subMonth()->format('Y-m-d'),
            'dateTo' => now()->format('Y-m-d')
        ]);

        // 2️⃣ Продажи
        $this->call('fetch:sales', [
            'dateFrom' => now()->subMonth()->format('Y-m-d'),
            'dateTo' => now()->format('Y-m-d')
        ]);

        // 3️⃣ Доходы
        $this->call('fetch:incomes', [
            'dateFrom' => now()->subMonth()->format('Y-m-d'),
            'dateTo' => now()->format('Y-m-d')
        ]);

        // 4️⃣ Склады — текущий день
        $today = now()->format('Y-m-d');
        $this->call('fetch:stocks', [
            'dateFrom' => $today
        ]);

        $this->info("Выгрузка всех данных завершена: " . now());
    }
}
