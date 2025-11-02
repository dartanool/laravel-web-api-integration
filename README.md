# API Integration Project

Проект на Laravel 8 для интеграции с внешним API и сохранения данных в MySQL.

---

## 🔹 Стек

- PHP 8.1
- Laravel 8 + Laravel Octane
- MySQL
- Docker / Docker Compose

---

## 🔹 Настройка проекта

1. Клонируем репозиторий:
    ```bash
    git clone https://github.com/dartanool/laravel-web-api-integration.git
    cd laravel
2. Создать файл .env
    ```bash
    cp .env.example .env
3. Настроить переменные окружения:
    ```bash
    DB_HOST=sdartar5.beget.tech
    DB_PORT=3306
    DB_DATABASE=sdartar5_s
    DB_USERNAME=sdartar5_s
    DB_PASSWORD=wb_pasS5
    API_KEY=секретный_ключ
    BASE_URL=host
4. Поднять контейнеры:
    ```bash
    docker-compose up -d --build
5. Выполнить миграции:
    ```bash
   docker exec -it php-fpm bash 
   php artisan migrate

---

## Доступы для БД

- Host: sdartar5.beget.tech
- Port: 3306
- Database: sdartar5_s
- Username: sdartar5_s
- Password: wb_pasS5

---

## Структура базы данны

- orders
- sales
- incomes
- stocks
- companies
- accounts
- token_types
- api_services
- api_services_token_type
- api_tokens

---

## Команды Artisan

1. Выгрузка заказов
    ```bash
    php artisan fetch:orders {dateFrom} {dateTo}
2. Выгрузка продаж
    ```bash
    php artisan fetch:sales {dateFrom} {dateTo}
3. Выгрузка остатков на складе
    ```bash
    php artisan fetch:stocks {dateFrom}
4. Выгрузка доходов
    ```bash
    php artisan fetch:incomes {dateFrom} {dateTo}

---

# Структура проекта
    app/
    ├── Console/
    │ └── Commands/
    │ ├── FetchOrders.php
    │ ├── FetchSales.php
    │ ├── FetchStocks.php
    │ └── FetchIncomes.php
    ├── Models/
    │ ├── Order.php
    │ ├── Sale.php
    │ ├── Stock.php
    │ └── Income.php
    ├── Services/
    │ └── WbApiService.php
    database/
    ├── migrations/
    └── seeders/

