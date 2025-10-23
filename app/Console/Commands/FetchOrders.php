<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use App\Services\WebApiService;
use App\Models\Order;

class FetchOrders extends Command
{
    protected $signature = 'fetch:orders {dateFrom} {dateTo}';
    protected $description = 'Fetch orders from WB API';

    protected WebApiService $wbApiService;

    public function __construct(WebApiService $wbApiService)
    {
        parent::__construct();
        $this->wbApiService = $wbApiService;
    }

    public function handle()
    {
        $dateFrom = $this->argument('dateFrom');
        $dateTo = $this->argument('dateTo');

        $page = 1;

        do {
            $attempt = 0;
            $maxAttempts = 3;
            $response = null;

            while ($attempt < $maxAttempts) {
                try {
                    $response = $this->wbApiService->getOrders($dateFrom, $dateTo, $page);

                    if (!empty($response['data'])) {
                        break; // Успешно — выходим из retry
                    }

                    throw new Exception("Пустой ответ от API");
                } catch (Exception $e) {
                    $attempt++;
                    $this->warn("Ошибка при загрузке страницы {$page} (попытка {$attempt}/{$maxAttempts}): {$e->getMessage()}");
                    sleep(2); // 🕐 задержка между попытками
                }
            }

            if(empty($response['data'])) {
                $this->info("Нет данных на странице {$page}");
                break;
            }

            foreach ($response['data'] as $order) {
                Order::updateOrCreate(
                    [
                        'odid' => $order['odid'],
                        'nm_id' => $order['nm_id']
                    ],
                    [
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
                        'date' => $order['date'],
                    ]
                );
            }


            $this->info("Страница {$page} обработана");

            $page++;
        } while ($page <= ($response['last_page'] ?? 1));

        $this->info("Выгрузка заказов завершена.");
    }
}
