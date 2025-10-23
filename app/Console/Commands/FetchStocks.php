<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Services\WebApiService;
use Exception;
use Illuminate\Console\Command;

class FetchStocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:stocks {dateFrom}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    protected WebApiService $wbApiService;
    public function __construct(WebApiService $wbApiService)
    {
        parent::__construct();
        $this->wbApiService = $wbApiService;
    }
    public function handle()
    {
        $dateFrom = $this->argument('dateFrom');

        // проверка, что дата не больше текущей
        if (strtotime($dateFrom) >= strtotime('tomorrow')) {
            $this->error("Дата не может быть больше сегодняшней");
            return;
        }
        $page = 1;

        do {
            $attempt = 0;
            $maxAttempts = 3;
            $response = null;

            while ($attempt < $maxAttempts) {
                try {
                    $response = $this->wbApiService->getStocks($dateFrom, $page);

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

            if (empty($response['data'])) {
                $this->info("Нет данных на странице {$page}");
                break;
            }

            foreach ($response['data'] as $item) {
                Stock::updateOrCreate(
                    [
                        'supplier_article' => $item['supplier_article'],
                        'tech_size' => $item['tech_size'],
                        'nm_id' => $item['nm_id'],
                        'warehouse_name' => $item['warehouse_name'],
                        'date' => $item['date'],
                    ],
                    [
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
                    ]
                );
            }
            $this->info("Страница {$page} обработана");
            $page++;
        } while ($page <= ($response['last_page'] ?? 1));

        $this->info("Выгрузка остатков завершена.");
    }
}
