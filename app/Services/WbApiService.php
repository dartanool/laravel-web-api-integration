<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WbApiService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = env('BASE_URL');
        $this->apiKey = env('API_KEY');
    }

    protected function get($endpoint, $params = [])
    {
        $params['key'] = $this->apiKey;
        $response = Http::get($this->baseUrl . $endpoint, $params);
        return $response->json();
    }

    public function getOrders($dateFrom, $dateTo, $page = 1, $limit = 500)
    {
        return $this->get('/orders', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    public function getSales($dateFrom, $dateTo, $page = 1, $limit = 500)
    {
        return $this->get('/sales', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    public function getStocks($date, $page = 1, $limit = 500)
    {
        return $this->get('/stocks', [
            'dateFrom' => $date,
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    public function getIncomes($dateFrom, $dateTo, $page = 1, $limit = 500)
    {
        return $this->get('/incomes', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'page' => $page,
            'limit' => $limit,
        ]);
    }
}
