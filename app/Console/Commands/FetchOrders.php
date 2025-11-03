<?php

namespace App\Console\Commands;

use App\Services\WebApiService;
use App\Models\Order;

class FetchOrders extends FetchCommand
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

    protected string $modelClass = Order::class;
    protected string $apiMethod = 'getOrders';
    protected array $uniqueKeys = ['account_id', 'odid', 'nm_id'];

    public function __construct(WebApiService $apiService)
    {
        parent::__construct();
        $this->apiService = $apiService;
    }

    /**
     * Выполнение команды.
     *
     * @return void
     */
    protected function prepareRow(array $item, int $accountId): array
    {
        return [
            'account_id' => $accountId,
            'odid' => $item['odid'],
            'nm_id' => $item['nm_id'],
            'supplier_article' => $item['supplier_article'] ?? null,
            'tech_size' => $item['tech_size'] ?? null,
            'barcode' => $item['barcode'] ?? null,
            'total_price' => $item['total_price'] ?? 0,
            'discount_percent' => $item['discount_percent'] ?? 0,
            'warehouse_name' => $item['warehouse_name'] ?? null,
            'oblast' => $item['oblast'] ?? null,
            'subject' => $item['subject'] ?? null,
            'category' => $item['category'] ?? null,
            'brand' => $item['brand'] ?? null,
            'is_cancel' => $item['is_cancel'] ?? false,
            'cancel_dt' => $item['cancel_dt'] ?? null,
            'date' => $item['date'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
