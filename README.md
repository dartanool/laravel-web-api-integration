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
3. Настроить переменные окружения (доступы к БД и ключ API):
    ```bash
    DB_DATABASE=web_integration
    DB_USERNAME=web_user
    DB_PASSWORD=web_password
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

## Структура базы данны
 - orders
 - sales
 - incomes
 - stocks

---

# Структура проекта 
app/
├── Console/
│   └── Commands/
│       ├── FetchOrders.php
│       ├── FetchSales.php
│       ├── FetchStocks.php
│       └── FetchIncomes.php
├── Models/
│   ├── Order.php
│   ├── Sale.php
│   ├── Stock.php
│   └── Income.php
├── Services/
│   └── WbApiService.php
database/
├── migrations/
└── seeders/

