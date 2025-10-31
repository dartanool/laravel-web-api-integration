<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Stock;
use App\Services\WebApiService;
use Exception;
use Illuminate\Console\Command;

class FetchStocks extends Command
{
    protected $signature = 'fetch:stocks {dateFrom}';
    protected $description = 'Command description';
    protected WebApiService $wbApiService;

    public function __construct(WebApiService $wbApiService)
    {
        parent::__construct();
        $this->wbApiService = $wbApiService;
    }

    public function handle(): void
    {
        $dateFrom = $this->argument('dateFrom');
        if (strtotime($dateFrom) >= strtotime('tomorrow')) {
            $this->error("Дата не может быть больше сегодняшней");
            return;
        }

        $accounts = Account::with('tokens')->get();
        foreach ($accounts as $account) {
            $this->info("🔹 Обрабатываем аккаунт {$account->id} ({$account->name})");

            $token = $account->tokens->first();
            if (!$token) {
                $this->warn(" Токен не найден для аккаунта {$account->id}");
                continue;
            }
            $this->wbApiService->setApiKey($token->token_value);

            $page = 1;
            do {
                $response = null;
                $response = retry(3, fn() => $this->wbApiService->getStocks($dateFrom, $page, 500),1000);

                if (empty($response['data'])) {
                    $this->info("Нет данных на странице {$page}");
                    break;
                }

                foreach ($response['data'] as $item) {
                    $stocksToInsert[] = [
                        'account_id' => $account->id,
                        'supplier_article' => $item['supplier_article'],
                        'tech_size' => $item['tech_size'],
                        'nm_id' => $item['nm_id'],
                        'warehouse_name' => $item['warehouse_name'],
                        'date' => $item['date'] ?? $dateFrom,
                        'barcode' => $item['barcode'] ?? null,
                        'quantity' => $item['quantity'] ?? 0,
                        'quantity_full' => $item['quantity_full'] ?? 0,
                        'is_supply' => $item['is_supply'] ?? false,
                        'is_realization' => $item['is_realization'] ?? false,
                        'in_way_to_client' => $item['in_way_to_client'] ?? 0,
                        'in_way_from_client' => $item['in_way_from_client'] ?? 0,
                        'subject' => $item['subject'] ?? null,
                        'category' => $item['category'] ?? null,
                        'brand' => $item['brand'] ?? null,
                        'sc_code' => $item['sc_code'] ?? null,
                        'price' => $item['price'] ?? 0,
                        'discount' => $item['discount'] ?? 0,
                        'last_change_date' => $item['last_change_date'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (!empty($stocksToInsert)) {
                    Stock::upsert(
                        $stocksToInsert,
                        ['account_id', 'supplier_article', 'tech_size', 'nm_id', 'warehouse_name', 'date'],
                        [
                            'barcode',
                            'quantity',
                            'quantity_full',
                            'is_supply',
                            'is_realization',
                            'in_way_to_client',
                            'in_way_from_client',
                            'subject',
                            'category',
                            'brand',
                            'sc_code',
                            'price',
                            'discount',
                            'last_change_date',
                            'updated_at',
                        ]
                    );
                }
                $this->info("Страница {$page} обработана");
                $page++;
            } while ($page <= ($response['last_page'] ?? 1));
            $this->info(" Выгрузка остатков для аккаунта {$account->id}");
        }
        $this->info("Все аккаунты обработаны");
    }
}
