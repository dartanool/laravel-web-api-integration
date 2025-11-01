<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Account;
use Illuminate\Support\Facades\Log;
use Exception;

abstract class FetchCommand extends Command
{
    /**
     * Имя модели, куда сохраняем данные
     * @var string
     */
    protected string $modelClass;

    /**
     * Сервис API (WebApiService)
     * @var \App\Services\WebApiService
     */
    protected $apiService;

    /**
     * Имя метода в сервисе, который выполняет выгрузку
     * @var string
     */
    protected string $apiMethod;

    /**
     * Обязательные поля для уникальности (upsert)
     * @var array
     */
    protected array $uniqueKeys = [];

    /**
     * Выполняем команду.
     */
    public function handle(): void
    {
        $dateFrom = $this->argument('dateFrom');
        $dateTo = $this->argument('dateTo');

        $accounts = Account::with('tokens')->get();

        foreach ($accounts as $account) {
            $this->info("🔹 Обрабатываем аккаунт {$account->id} ({$account->name})");

            $token = $account->tokens->first();
            if (!$token) {
                $this->warn(" Токен не найден для аккаунта {$account->id}");
                continue;
            }

            $this->apiService->setApiKey($token->token_value);

            $model = $this->modelClass;
            $lastDate = $model::where('account_id', $account->id)->max('date');

            if ($lastDate) {
                $dateFrom = max($dateFrom, $lastDate);
                $this->info(" Загружаем только новые данные с {$dateFrom}");
            }

            $page = 1;
            $totalInserted = 0;

            do {
                $this->line("   → Загружаем страницу {$page}...");

                $response = retry(3, function () use ($dateFrom, $dateTo, $page) {
                    try {
                        return $this->apiService->{$this->apiMethod}($dateFrom, $dateTo, $page);
                    } catch (Exception $e) {
                        Log::error("Ошибка API: " . $e->getMessage());
                        throw $e;
                    }
                }, 1000);

                if (empty($response['data'])) {
                    $this->warn(" Нет данных на странице {$page}");
                    break;
                }

                $rows = [];
                foreach ($response['data'] as $item) {
                    $rows[] = $this->prepareRow($item, $account->id);
                }

                if (!empty($rows)) {
                    $model::upsert($rows, $this->uniqueKeys, array_keys($rows[0]));
                    $totalInserted += count($rows);
                }

                $this->info(" Страница {$page} обработана, записей добавлено: " . count($rows));
                $page++;

            } while ($page <= ($response['meta']['last_page'] ?? 1));

            $this->info("Выгрузка для аккаунта {$account->name} завершена. Всего добавлено: {$totalInserted}");
        }
    }

    /**
     * Подготовка строки для upsert
     */
    abstract protected function prepareRow(array $item, int $accountId): array;
}
