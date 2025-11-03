<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;

class AddCompany extends Command
{
    /**
     * Имя и сигнатура Artisan-команды.
     *
     * @var string
     */
    protected $signature = 'add:company {name}';
    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Создает новую компанию с указанным именем';

    /**
     * Выполнение консольной команды.
     *
     * @return void
     */
    public function handle() : void
    {
        $name = $this->argument('name');

        $company = Company::create(['name' => $name]);

        $this->info("Компания '{$company->name}' успешно создана ");
    }
}
