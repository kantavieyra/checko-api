# Checko API Proxy

Простой REST API на чистом PHP (ООП, без фреймворков) для получения данных организаций с [checko.ru](https://checko.ru).

## Требования
- PHP 8.1+
- ext-curl, ext-json

## Установка
```bash
composer install
cp .env.example .env
# впиши API-ключ в .env
```

API-ключ можно получить бесплатно после регистрации на checko.ru (до 100 запросов в сутки).

## Запуск
```bash
php -S localhost:8000 -t public
```

## Эндпоинты
- `GET /api/company/inn/{inn}`
- `GET /api/company/ogrn/{ogrn}`
- `GET /api/search?q=...`

### Примеры
```bash
curl http://localhost:8000/api/company/inn/7707049388
curl http://localhost:8000/api/company/ogrn/1027700198767
curl "http://localhost:8000/api/search?q=сбербанк"
```

## Структура
- `Http\Client` — внешние HTTP-запросы
- `Checko\CheckoService` — работа с целевым ресурсом + валидация + обработка ошибок
- `Api\CompanyController` — тонкий слой контроллера
