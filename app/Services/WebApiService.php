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

    /**
     * Получить заказы за период
     *
     * @param string $dateFrom Дата начала
     * @param string $dateTo Дата конца
     * @param int $page Номер страницы для постраничной загрузки
     * @param int $limit Количество записей на страницу
     * @return array Данные API в виде массива
     */

    public function getOrders(string $dateFrom, string $dateTo, int $page = 1, int $limit = 500) : array
    {
        return $this->get('/orders', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    /**
     * Получить продажи за период
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getSales(string $dateFrom, string $dateTo, int $page = 1, int $limit = 500) : array
    {
        return $this->get('/sales', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    /**
     * Получить со склада на дату
     *
     * @param string $date
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getStocks(string $date, int $page = 1, int $limit = 500) : array
    {
        return $this->get('/stocks', [
            'dateFrom' => $date,
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    /**
     * Получить доходы за период
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getIncomes(string $dateFrom, string $dateTo, int $page = 1, int $limit = 500) : array
    {
        return $this->get('/incomes', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    /**
     * Выполнить GET-запрос к API
     *
     * @param string $endpoint
     * @param array $params Параметры запроса
     * @return array Ответ API в виде массива
     */
    protected function get(string $endpoint, array $params = [])
    {
        $params['key'] = $this->apiKey;
        $response = Http::get($this->baseUrl . $endpoint, $params);
        return $response->json();
    }
}
