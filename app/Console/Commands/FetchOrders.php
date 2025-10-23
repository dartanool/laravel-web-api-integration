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

    public function handle() : void
    {
        $dateFrom = $this->argument('dateFrom');
        $dateTo = $this->argument('dateTo');
        $page = 1;

        do {
            $response = null;
            $response = retry(3, fn() => $this->wbApiService->getOrders($dateFrom, $dateTo, $page),
                1000
            );

            if (empty($response['data'])) {
                $this->info("Нет данных на странице {$page}");
                break;
            }

            foreach ($response['data'] as $order) {
                $ordersToInsert[] = [
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
                    ['odid', 'nm_id'],
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
            $this->info("Страница {$page} обработана");

            $page++;
        } while ($page <= ($response['last_page'] ?? 1));

        $this->info("Выгрузка заказов завершена.");
    }
}
