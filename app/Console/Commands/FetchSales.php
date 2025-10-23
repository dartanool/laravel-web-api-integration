<?php

namespace App\Console\Commands;

use App\Models\Sale;
use App\Services\WbApiService;
use Illuminate\Console\Command;

class FetchSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature =  'fetch:sales {dateFrom} {dateTo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch sales from WB API';
    protected WbApiService $wbApiService;
    public function __construct(WbApiService $wbApiService)
    {
        parent::__construct();
        $this->wbApiService = $wbApiService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dateFrom = $this->argument('dateFrom');
        $dateTo = $this->argument('dateTo');

        $page = 1;
        do {
            $response = $this->wbApiService->getSales($dateFrom, $dateTo);

            if(empty($response['data'])) {
                $this->info("Нет данных на странице {$page}");
                break;
            }

            foreach ($response['data'] as $sale) {
                Sale::updateOrCreate(
                    ['sale_id' => $sale['sale_id']],
                    [
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
                    ]
                );
            }

            $this->info("Страница {$page} обработана");
            $page++;
        }  while ($page <= ($response['last_page'] ?? 1));

        $this->info("Выгрузка продаж завершена.");
    }
}
