<?php

namespace App\Console\Commands;

use App\Models\Income;
use App\Services\WebApiService;
use Exception;
use Illuminate\Console\Command;

class FetchIncomes extends Command
{
    protected $signature = 'fetch:incomes {dateFrom} {dateTo}';
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
        $dateTo = $this->argument('dateTo');
        $page = 1;

        do {
            $response = null;

            $response = retry(3, fn() => $this->wbApiService->getIncomes($dateFrom, $dateTo, $page),
                1000
            );

            if (empty($response['data'])) {
                $this->info("Нет данных на странице {$page}");
                break;
            }

            foreach ($response['data'] as $income) {
                $incomesToInsert[] = [
                    'income_id' => $income['income_id'],
                    'nm_id' => $income['nm_id'],
                    'supplier_article' => $income['supplier_article'] ?? null,
                    'tech_size' => $income['tech_size'] ?? null,
                    'number' => $income['number'] ?? null,
                    'date' => $income['date'] ?? null,
                    'last_change_date' => $income['last_change_date'] ?? null,
                    'barcode' => $income['barcode'] ?? null,
                    'quantity' => $income['quantity'] ?? 0,
                    'total_price' => $income['total_price'] ?? 0,
                    'date_close' => $income['date_close'] ?? null,
                    'warehouse_name' => $income['warehouse_name'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($incomesToInsert)) {
                Income::upsert(
                    $incomesToInsert,
                    ['income_id', 'nm_id', 'supplier_article', 'tech_size'],
                    [
                        'number',
                        'date',
                        'last_change_date',
                        'barcode',
                        'quantity',
                        'total_price',
                        'date_close',
                        'warehouse_name',
                        'updated_at',
                    ]
                );
            }
            $this->info("Страница {$page} обработана");
            $page++;
        } while ($page <= ($response['meta']['last_page'] ?? 1));

        $this->info("Выгрузка доходов завершена.");
    }
}
