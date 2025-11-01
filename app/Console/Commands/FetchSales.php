<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Sale;
use App\Services\WebApiService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FetchSales extends Command
{
    /**
     * Имя и сигнатура Artisan-команды.
     *
     * @var string
     */
    protected $signature = 'fetch:sales {dateFrom} {dateTo}';
    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Загружает продажи за указанный период';
    protected WebApiService $wbApiService;

    public function __construct(WebApiService $wbApiService)
    {
        parent::__construct();
        $this->wbApiService = $wbApiService;
    }
    /**
     * Выполнение команды.
     *
     * @return void
     */
    public function handle(): void
    {
        $dateFrom = $this->argument('dateFrom');
        $dateTo = $this->argument('dateTo');



        // определяет последнюю дату, которая есть в таблице incomes
        $lastDate = DB::table('incomes')->max('date');

        if ($lastDate) {
            $dateFrom = max($dateFrom, $lastDate); // чтобы не уйти назад по времени
            $this->info(" Загружаем только свежие данные, начиная с {$dateFrom}");
        } else {
            $this->info(" В таблице пока нет данных — загружаем всё с {$dateFrom}");
        }



        $accounts = Account::with('tokens')->get();
        foreach ($accounts as $account) {
            $this->info("🔹 Обрабатываем аккаунт {$account->id} ({$account->name})");

            $token = $account->tokens->first();
            if (!$token) {
                $this->warn(" Токен не найден для аккаунта {$account->id}");
                continue;
            }
            $this->wbApiService->setApiKey($token->token_value); // если в сервисе есть метод setApiKey()

            $page = 1;

            do {
                $response = retry(3, fn() => $this->wbApiService->getSales($dateFrom, $dateTo, $page),
                    1000
                );

                if (empty($response['data'])) {
                    $this->info("Нет данных на странице {$page}");
                    break;
                }

                $salesToInsert = [];
                foreach ($response['data'] as $sale) {
                    $salesToInsert[] = [
                        'account_id' => $account->id,
                        'sale_id' => $sale['sale_id'],
                        'supplier_article' => $sale['supplier_article'] ?? null,
                        'tech_size' => $sale['tech_size'] ?? null,
                        'barcode' => $sale['barcode'] ?? null,
                        'total_price' => $sale['total_price'] ?? 0,
                        'discount_percent' => $sale['discount_percent'] ?? 0,
                        'is_supply' => $sale['is_supply'] ?? false,
                        'is_realization' => $sale['is_realization'] ?? false,
                        'promo_code_discount' => $sale['promo_code_discount'] ?? null,
                        'warehouse_name' => $sale['warehouse_name'] ?? null,
                        'country_name' => $sale['country_name'] ?? null,
                        'oblast_okrug_name' => $sale['oblast_okrug_name'] ?? null,
                        'region_name' => $sale['region_name'] ?? null,
                        'income_id' => $sale['income_id'] ?? null,
                        'odid' => $sale['odid'] ?? null,
                        'spp' => $sale['spp'] ?? null,
                        'for_pay' => $sale['for_pay'] ?? null,
                        'finished_price' => $sale['finished_price'] ?? null,
                        'price_with_disc' => $sale['price_with_disc'] ?? null,
                        'nm_id' => $sale['nm_id'] ?? null,
                        'subject' => $sale['subject'] ?? null,
                        'category' => $sale['category'] ?? null,
                        'brand' => $sale['brand'] ?? null,
                        'is_storno' => $sale['is_storno'] ?? null,
                        'date' => $sale['date'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (!empty($salesToInsert)) {
                    \App\Models\Sale::upsert(
                        $salesToInsert,
                        ['account_id', 'sale_id'],
                        [
                            'supplier_article',
                            'tech_size',
                            'barcode',
                            'total_price',
                            'discount_percent',
                            'is_supply',
                            'is_realization',
                            'promo_code_discount',
                            'warehouse_name',
                            'country_name',
                            'oblast_okrug_name',
                            'region_name',
                            'income_id',
                            'odid',
                            'spp',
                            'for_pay',
                            'finished_price',
                            'price_with_disc',
                            'nm_id',
                            'subject',
                            'category',
                            'brand',
                            'is_storno',
                            'date',
                            'updated_at',
                        ]
                    );
                }

                $this->info("Страница {$page} обработана");
                $page++;
            } while ($page <= ($response['last_page'] ?? 1));

            $this->info(" Выгрузка продаж завершена для аккаунта {$account->id}");
        }
        $this->info("Все аккаунты обработаны");
    }
}
