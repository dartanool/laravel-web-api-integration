<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Services\WebApiService;

class FetchStocks extends FetchCommand
{
    protected $signature = 'fetch:stocks {dateFrom}';
    protected $description = 'Выгрузка остатков со склада';
    protected string $modelClass = Stock::class;
    protected string $apiMethod = 'getStocks';
    protected array $uniqueKeys = ['account_id', 'supplier_article', 'tech_size', 'nm_id', 'warehouse_name', 'date'];

    public function __construct(WebApiService $apiService)
    {
        parent::__construct();
        $this->apiService = $apiService;
    }

    protected function prepareRow(array $item, int $accountId): array
    {
        return [
            'account_id' => $accountId,
            'supplier_article' => $item['supplier_article'],
            'tech_size' => $item['tech_size'],
            'nm_id' => $item['nm_id'],
            'warehouse_name' => $item['warehouse_name'],
            'date' => $item['date'],
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
}
