<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\ApiService;
use App\Models\ApiToken;
use App\Models\TokenType;
use Illuminate\Console\Command;

class AddApiToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:api-token {accountId} {apiServiceId} {tokenTypeId} {tokenValue}';

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
        $accountId = $this->argument('accountId');
        $apiServiceId = $this->argument('apiServiceId');
        $tokenTypeId = $this->argument('tokenTypeId');
        $tokenValue = $this->argument('tokenValue');

        if (!Account::find($accountId)) {
            $this->error("Аккаунт с ID {$accountId} не найден!");
            return 1;
        }
        if (!ApiService::find($apiServiceId)) {
            $this->error("API-сервис с ID {$apiServiceId} не найден!");
            return 1;
        }
        if (!TokenType::find($tokenTypeId)) {
            $this->error("Тип токена с ID {$tokenTypeId} не найден!");
            return 1;
        }

        $apiToken = ApiToken::create([
            'account_id' => $accountId,
            'api_service_id' => $apiServiceId,
            'token_type_id' => $tokenTypeId,
            'token_value' => $tokenValue,
        ]);

        $this->info(" Токен успешно добавлен для аккаунта {$accountId}");
        return 0;
    }
}
