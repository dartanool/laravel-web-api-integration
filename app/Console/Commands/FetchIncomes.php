<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Income;
use App\Services\WebApiService;
use Exception;
use Illuminate\Console\Command;

class FetchIncomes extends FetchCommand
{
    /**
     * Имя и сигнатура Artisan-команды.
     *
     * @var string
     */
    protected $signature = 'fetch:incomes {dateFrom} {dateTo}';
    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Загружает доходы за указанный период';
    protected string $modelClass = Income::class;
    protected string $apiMethod = 'getIncomes';
    protected array $uniqueKeys = ['account_id', 'income_id', 'nm_id', 'supplier_article', 'tech_size'];

    public function __construct(WebApiService $apiService)
    {
        parent::__construct();
        $this->apiService = $apiService;
    }

    protected function prepareRow(array $item, int $accountId): array
    {
        return [
            'account_id' => $accountId,
            'income_id' => $item['income_id'],
            'nm_id' => $item['nm_id'],
            'supplier_article' => $item['supplier_article'] ?? null,
            'tech_size' => $item['tech_size'] ?? null,
            'number' => $item['number'] ?? null,
            'date' => $item['date'] ?? null,
            'last_change_date' => $item['last_change_date'] ?? null,
            'barcode' => $item['barcode'] ?? null,
            'quantity' => $item['quantity'] ?? 0,
            'total_price' => $item['total_price'] ?? 0,
            'date_close' => $item['date_close'] ?? null,
            'warehouse_name' => $item['warehouse_name'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
