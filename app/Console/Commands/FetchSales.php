<?php

namespace App\Console\Commands;

use App\Models\Sale;
use App\Services\WebApiService;

class FetchSales extends FetchCommand
{
    protected $signature = 'fetch:sales {dateFrom} {dateTo}';
    protected $description = 'Выгрузка продаж';
    protected string $modelClass = Sale::class;
    protected string $apiMethod = 'getSales';
    protected array $uniqueKeys = ['account_id', 'sale_id'];

    public function __construct(WebApiService $apiService)
    {
        parent::__construct();
        $this->apiService = $apiService;
    }

    protected function prepareRow(array $item, int $accountId): array
    {
        return [
            'account_id' => $accountId,
            'sale_id' => $item['sale_id'],
            'supplier_article' => $item['supplier_article'] ?? null,
            'tech_size' => $item['tech_size'] ?? null,
            'barcode' => $item['barcode'] ?? null,
            'total_price' => $item['total_price'] ?? 0,
            'discount_percent' => $item['discount_percent'] ?? 0,
            'is_supply' => $item['is_supply'] ?? false,
            'is_realization' => $item['is_realization'] ?? false,
            'promo_code_discount' => $item['promo_code_discount'] ?? null,
            'warehouse_name' => $item['warehouse_name'] ?? null,
            'country_name' => $item['country_name'] ?? null,
            'oblast_okrug_name' => $item['oblast_okrug_name'] ?? null,
            'region_name' => $item['region_name'] ?? null,
            'income_id' => $item['income_id'] ?? null,
            'odid' => $item['odid'] ?? null,
            'spp' => $item['spp'] ?? null,
            'for_pay' => $item['for_pay'] ?? null,
            'finished_price' => $item['finished_price'] ?? null,
            'price_with_disc' => $item['price_with_disc'] ?? null,
            'nm_id' => $item['nm_id'] ?? null,
            'subject' => $item['subject'] ?? null,
            'category' => $item['category'] ?? null,
            'brand' => $item['brand'] ?? null,
            'is_storno' => $item['is_storno'] ?? null,
            'date' => $item['date'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
