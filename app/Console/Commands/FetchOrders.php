<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WebApiService;
use App\Models\Order;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class FetchOrders extends Command
{
    /**
     * Имя и сигнатура Artisan-команды.
     *
     * @var string
     */
    protected $signature = 'fetch:orders {dateFrom} {dateTo}';
    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Загружает заказы за указанный период';

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

            $this->wbApiService->setApiKey($token->token_value);

            $page = 1;
            do {
                $response = retry(3, fn() => $this->wbApiService->getOrders($dateFrom, $dateTo, $page), 1000);

                if (empty($response['data'])) {
                    $this->info("Нет данных на странице {$page}");
                    break;
                }

                $count = isset($response['data']) ? count($response['data']) : 0;
                $this->info("Получено записей: {$count} на странице {$page}");

                $ordersToInsert = [];
                foreach ($response['data'] as $order) {
                    $ordersToInsert[] = [
                        'account_id' => $account->id,
                        'odid' => $order['odid'],
                        'nm_id' => $order['nm_id'],
                        'supplier_article' => $order['supplier_article'] ?? null,
                        'tech_size' => $order['tech_size'] ?? null,
                        'barcode' => $order['barcode'] ?? null,
                        'total_price' => $order['total_price'] ?? 0,
                        'discount_percent' => $order['discount_percent'] ?? 0,
                        'warehouse_name' => $order['warehouse_name'] ?? null,
                        'oblast' => $order['oblast'] ?? null,
                        'subject' => $order['subject'] ?? null,
                        'category' => $order['category'] ?? null,
                        'brand' => $order['brand'] ?? null,
                        'is_cancel' => $order['is_cancel'] ?? false,
                        'cancel_dt' => $order['cancel_dt'] ?? null,
                        'date' => $order['date'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (!empty($ordersToInsert)) {
                    Order::upsert(
                        $ordersToInsert,
                        ['account_id', 'odid', 'nm_id'],
                        [
                            'supplier_article',
                            'tech_size',
                            'barcode',
                            'total_price',
                            'discount_percent',
                            'warehouse_name',
                            'oblast',
                            'subject',
                            'category',
                            'brand',
                            'is_cancel',
                            'cancel_dt',
                            'date',
                            'updated_at',
                        ]
                    );
                }

                $this->info("Страница {$page} обработана для аккаунта {$account->id}");
                $page++;
            } while ($page <= ($response['last_page'] ?? 1));

            $this->info(" Выгрузка заказов завершена для аккаунта {$account->id}");
        }
        $this->info(" Все аккаунты обработаны");
    }
}
