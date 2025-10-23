<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WebApiService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = env('BASE_URL');
        $this->apiKey = env('API_KEY');
    }


    public function getOrders(string $dateFrom, string $dateTo, int $page = 1, int $limit = 500)
    {
        return $this->get('/orders', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    public function getSales(string $dateFrom, string $dateTo, int $page = 1, int $limit = 500)
    {
        return $this->get('/sales', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    public function getStocks(string $date, int $page = 1, int $limit = 500)
    {
        return $this->get('/stocks', [
            'dateFrom' => $date,
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    public function getIncomes(string $dateFrom, string $dateTo, int $page = 1, int $limit = 500)
    {
        return $this->get('/incomes', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    protected function get(string $endpoint, array $params = [])
    {
        $params['key'] = $this->apiKey;
        $response = Http::get($this->baseUrl . $endpoint, $params);
        return $response->json();
    }
}
