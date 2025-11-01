<?php

namespace App\Console\Commands;

use App\Models\ApiService;
use Illuminate\Console\Command;

class AddApiService extends Command
{
    /**
     * Имя и сигнатура Artisan-команды.
     *
     * @var string
     */
    protected $signature = 'add:api-service {name} {baseUrl}';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Добавляет новый api_service';

    /**
     * Выполнение консольной команды.
     *
     * @return void
     */
    public function handle() : void
    {
        $name = $this->argument('name');
        $baseUrl = $this->argument('baseUrl');

        $apiService = ApiService::create([
            'name' => $name,
            'base_url' => $baseUrl,
        ]);
        $this->info("API-сервис '{$apiService->name}' успешно добавлен (ID: {$apiService->id})");
    }
}
