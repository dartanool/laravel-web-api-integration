<?php

namespace App\Console\Commands;

use App\Models\Income;
use App\Services\WbApiService;
use Illuminate\Console\Command;

class FetchIncomes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:incomes {dateFrom} {dateTo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected WbApiService $wbApiService;

    /**
     * Execute the console command.
     */
    public function __construct(){
        parent::__construct();
        $this->wbApiService = new WbApiService();
    }
    public function handle()
    {
        $dateFrom = $this->argument('dateFrom');
        $dateTo = $this->argument('dateTo');

        $page = 1;

        do {
            $response = $this->wbApiService->getIncomes($dateFrom, $dateTo, $page);

//            dd($response);
            if (empty($response['data'])) {
                $this->info("Нет данных на странице {$page}");
                break;
            }

            foreach ($response['data'] as $income) {
                Income::updateOrCreate(
                    [
                        'income_id' => $income['income_id'],
                        'nm_id' => $income['nm_id'],
                        'supplier_article' => $income['supplier_article'],
                        'tech_size' => $income['tech_size'],
                    ],
                    [
                        'number' => $income['number'] ?? null,
                        'date' => $income['date'] ?? null,
                        'last_change_date' => $income['last_change_date'] ?? null,
                        'barcode' => $income['barcode'] ?? null,
                        'quantity' => $income['quantity'] ?? 0,
                        'total_price' => $income['total_price'] ?? 0,
                        'date_close' => $income['date_close'] ?? null,
                        'warehouse_name' => $income['warehouse_name'] ?? null,
                    ]
                );
            }

            $this->info("Страница {$page} обработана");
            $page++;
        } while ($page <= ($response['meta']['last_page'] ?? 1));

        $this->info("Выгрузка доходов завершена.");
    }
}
