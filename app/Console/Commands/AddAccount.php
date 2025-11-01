<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Company;
use Illuminate\Console\Command;

class AddAccount extends Command
{
    /**
     * Имя и сигнатура Artisan-команды.
     *
     * @var string
     */
    protected $signature = 'add:account {companyId} {name}';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Добавляет новый аккаунт к существующей компании';

    /**
     * Выполнение консольной команды.
     *
     * @return int
     */
    public function handle()
    {
        $companyId = $this->argument('companyId');
        $name = $this->argument('name');

        if (!Company::find($companyId)) {
            $this->error("Компания с ID {$companyId} не найдена!");
            return 0;
        }

        $account = Account::create([
            'company_id' => $companyId,
            'name' => $name
        ]);

        $this->info("Аккаунт '{$account->name}' добавлен к компании ID {$companyId}");
        return 0;
    }
}
