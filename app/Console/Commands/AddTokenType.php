<?php

namespace App\Console\Commands;

use App\Models\TokenType;
use Illuminate\Console\Command;

class AddTokenType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:token-type {tokenType}';

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
        $tokenType= $this->argument('tokenType');

        if (TokenType::find($tokenType)) {
            $this->info("Тип токена '{$tokenType}' уже существует.");
            return 0;
        }

        $tokenType= TokenType::create([
            'name' => $tokenType,
        ]);

        $this->info("Тип токена '{$tokenType->name}' успешно добавлен.");
        return 0;
    }
}
