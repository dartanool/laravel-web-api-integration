<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

    public function getOrders(string $dateFrom, string $dateTo, int $page = 1, int $limit = 500): array
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
    public function getSales(string $dateFrom, string $dateTo, int $page = 1, int $limit = 500): array
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
    public function getStocks(string $date, int $page = 1, int $limit = 500): array
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
    public function getIncomes(string $dateFrom, string $dateTo, int $page = 1, int $limit = 500): array
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
        $url = $this->baseUrl . $endpoint;

        return retry(5, function () use ($url, $params) {
            $response = Http::get($url, $params);

            if ($response->status() === 429) {
                $retryAfter = $response->header('Retry-After', 10);

                Log::warning("Too many requests. Ждём {$retryAfter} секунд");

                sleep($retryAfter);
                throw new \Exception('Too Many Requests');
            }

            if (!$response->successful()) {
                throw new \Exception("Ошибка: {$response->status()}");
            }

            Log::info(" Успешный запрос: {$url}");
            return $response->json();
        }, 5 * 1000);
    }
    /**
     * Задаёт токен для API.
     *
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }
}
