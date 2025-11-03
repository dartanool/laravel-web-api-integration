<?php

namespace App\Console\Commands;

use App\Models\TokenType;
use Illuminate\Console\Command;

class AddTokenType extends Command
{
    /**
     * Имя и сигнатура Artisan-команды.
     *
     * @var string
     */
    protected $signature = 'add:token-type {tokenType}';
    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Добавляет новый тип токена в систему';

    /**
     * Выполнение консольной команды.
     *
     * @return boolean
     */
    public function handle(): bool
    {
        $tokenType = $this->argument('tokenType');

        if (TokenType::find($tokenType)) {
            $this->error("Тип токена '{$tokenType}' уже существует.");
            return 0;
        }

        $tokenType = TokenType::create([
            'name' => $tokenType,
        ]);

        $this->info("Тип токена '{$tokenType->name}' успешно добавлен.");
        return 0;
    }
}
