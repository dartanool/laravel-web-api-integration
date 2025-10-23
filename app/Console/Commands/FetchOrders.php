<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WbApiService;
use App\Models\Order;

class FetchOrders extends Command
{
    protected $signature = 'fetch:orders {dateFrom} {dateTo}';
    protected $description = 'Fetch orders from WB API';

    protected $wbApiService;

    public function __construct(WbApiService $wbApiService)
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
            $response = $this->wbApiService->getOrders($dateFrom, $dateTo, $page);

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
