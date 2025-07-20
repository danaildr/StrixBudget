# StrixBudget API Documentation

## Общо описание

StrixBudget API е RESTful API за управление на лични финанси, включващо банкови сметки, транзакции, трансфери, контрагенти и типове транзакции.

## Аутентикация

API използва Laravel Sanctum за аутентикация с Bearer токени.

### Base URL
```
http://localhost:8000/api
```

## Endpoints

### Аутентикация

#### POST /auth/login
Влизане в системата и получаване на токен.

**Request:**
```json
{
    "email": "user@example.com",
    "password": "password"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "User Name",
            "email": "user@example.com",
            "locale": "bg",
            "role": "user"
        },
        "token": "1|token_string",
        "token_type": "Bearer"
    }
}
```

#### POST /auth/logout
Излизане от системата (изтриване на токен).

**Headers:** `Authorization: Bearer {token}`

#### GET /auth/user
Получаване на данни за текущия потребител.

**Headers:** `Authorization: Bearer {token}`

#### PUT /auth/profile
Обновяване на профила на потребителя.

#### PUT /auth/password
Смяна на парола.

#### GET /auth/statistics
Получаване на статистики за потребителя.

### Банкови сметки

#### GET /bank-accounts
Получаване на всички банкови сметки на потребителя.

#### POST /bank-accounts
Създаване на нова банкова сметка.

**Request:**
```json
{
    "name": "My Account",
    "currency": "EUR",
    "balance": 1000.50,
    "is_active": true,
    "is_default": false
}
```

#### GET /bank-accounts/{id}
Получаване на конкретна банкова сметка.

#### PUT /bank-accounts/{id}
Обновяване на банкова сметка.

#### DELETE /bank-accounts/{id}
Изтриване на банкова сметка.

#### GET /bank-accounts/{id}/statistics
Получаване на статистики за банкова сметка.

### Транзакции

#### GET /transactions
Получаване на всички транзакции с филтри.

**Query параметри:**
- `bank_account_id` - филтър по банкова сметка
- `type` - филтър по тип (income/expense)
- `counterparty_id` - филтър по контрагент
- `transaction_type_id` - филтър по тип транзакция
- `from_date` - филтър от дата
- `to_date` - филтър до дата
- `per_page` - брой резултати на страница (макс 100)

#### POST /transactions
Създаване на нова транзакция.

**Request:**
```json
{
    "bank_account_id": 1,
    "counterparty_id": 1,
    "transaction_type_id": 1,
    "type": "income",
    "amount": 500.00,
    "currency": "EUR",
    "description": "Salary",
    "executed_at": "2025-06-18"
}
```

#### GET /transactions/{id}
Получаване на конкретна транзакция.

#### PUT /transactions/{id}
Обновяване на транзакция.

#### DELETE /transactions/{id}
Изтриване на транзакция.

### Трансфери

#### GET /transfers
Получаване на всички трансфери.

#### POST /transfers
Създаване на нов трансфер.

**Request:**
```json
{
    "from_account_id": 1,
    "to_account_id": 2,
    "amount_from": 100.00,
    "currency_from": "EUR",
    "amount_to": 110.00,
    "currency_to": "USD",
    "exchange_rate": 1.1,
    "description": "Currency exchange",
    "executed_at": "2025-06-18"
}
```

#### GET /transfers/{id}
Получаване на конкретен трансфер.

#### PUT /transfers/{id}
Обновяване на трансфер.

#### DELETE /transfers/{id}
Изтриване на трансфер.

### Контрагенти

#### GET /counterparties
Получаване на всички контрагенти.

**Query параметри:**
- `search` - търсене по име, email или телефон
- `per_page` - брой резултати на страница

#### POST /counterparties
Създаване на нов контрагент.

**Request:**
```json
{
    "name": "Company Name",
    "description": "Description",
    "email": "contact@company.com",
    "phone": "+359888123456"
}
```

#### GET /counterparties/{id}
Получаване на конкретен контрагент.

#### PUT /counterparties/{id}
Обновяване на контрагент.

#### DELETE /counterparties/{id}
Изтриване на контрагент.

#### GET /counterparties/{id}/statistics
Получаване на статистики за контрагент.

#### GET /counterparties/{id}/transactions
Получаване на транзакции за контрагент.

### Типове транзакции

#### GET /transaction-types
Получаване на всички типове транзакции.

#### POST /transaction-types
Създаване на нов тип транзакция.

**Request:**
```json
{
    "name": "Salary",
    "description": "Monthly salary"
}
```

#### GET /transaction-types/{id}
Получаване на конкретен тип транзакция.

#### PUT /transaction-types/{id}
Обновяване на тип транзакция.

#### DELETE /transaction-types/{id}
Изтриване на тип транзакция.

#### GET /transaction-types/{id}/statistics
Получаване на статистики за тип транзакция.

#### GET /transaction-types/{id}/transactions
Получаване на транзакции за тип транзакция.

### 🕒 Повтарящи се плащания (Recurring Payments)

| Метод | Endpoint | Описание |
|-------|----------|----------|
| GET | `/api/recurring-payments` | Списък с повтарящи се плащания |
| POST | `/api/recurring-payments` | Създаване на повтарящо се плащане |
| GET | `/api/recurring-payments/{id}` | Детайли за повтарящо се плащане |
| PUT | `/api/recurring-payments/{id}` | Обновяване на повтарящо се плащане |
| PATCH | `/api/recurring-payments/{id}` | Частично обновяване |
| DELETE | `/api/recurring-payments/{id}` | Изтриване |

**Пример за създаване:**
```json
{
  "bank_account_id": 1,
  "counterparty_id": 1,
  "transaction_type_id": 1,
  "amount": 100.00,
  "currency": "BGN",
  "description": "Месечен наем",
  "repeat_type": "monthly",
  "repeat_interval": 1,
  "repeat_unit": "months",
  "period_start_day": 1,
  "period_end_day": 10,
  "start_date": "2025-07-01",
  "end_date": null,
  "is_active": true
}
```

### 📅 Планирани плащания (Scheduled Payments)

| Метод | Endpoint | Описание |
|-------|----------|----------|
| GET | `/api/scheduled-payments` | Списък с планирани плащания |
| POST | `/api/scheduled-payments` | Създаване на планирано плащане |
| GET | `/api/scheduled-payments/{id}` | Детайли за планирано плащане |
| PUT | `/api/scheduled-payments/{id}` | Обновяване на планирано плащане |
| PATCH | `/api/scheduled-payments/{id}` | Частично обновяване |
| DELETE | `/api/scheduled-payments/{id}` | Изтриване |

**Пример за създаване:**
```json
{
  "bank_account_id": 1,
  "counterparty_id": 1,
  "transaction_type_id": 1,
  "amount": 50.00,
  "currency": "BGN",
  "description": "Плащане на ток",
  "scheduled_date": "2025-07-10",
  "period_start_date": "2025-07-01",
  "period_end_date": "2025-07-15",
  "is_active": true
}
```

**Query параметри за списъците:**
- `is_active` — филтрира по статус (true/false)
- `per_page` — брой на страница
- `page` — номер на страница

## Формат на отговорите

Всички отговори са в JSON формат със следната структура:

### Успешен отговор
```json
{
    "success": true,
    "message": "Success message",
    "data": { ... }
}
```

### Грешка
```json
{
    "success": false,
    "message": "Error message",
    "errors": { ... }
}
```

## HTTP Status кодове

- `200` - Успешна заявка
- `201` - Успешно създаване
- `400` - Невалидна заявка
- `401` - Неаутентикиран
- `403` - Забранен достъп
- `404` - Не е намерен
- `409` - Конфликт
- `422` - Валидационна грешка
- `500` - Сървърна грешка

## Поддържани валути

EUR, USD, BGN, GBP, CHF, JPY

## Примери за тестване

### Пълен workflow:

1. **Login:**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "test@example.com", "password": "password"}'
```

2. **Създаване на банкова сметка:**
```bash
curl -X POST http://localhost:8000/api/bank-accounts \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"name": "My Account", "currency": "EUR", "balance": 1000}'
```

3. **Създаване на транзакция:**
```bash
curl -X POST http://localhost:8000/api/transactions \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"bank_account_id": 1, "type": "income", "amount": 500, "currency": "EUR", "executed_at": "2025-06-18"}'
```

## Пълен списък с валидни API Endpoints

### � API Information (Публичен)

| Метод | Endpoint | Описание |
|-------|----------|----------|
| GET | `/api` | API health check и информация за endpoints |

### �🔐 Аутентикация (Публични)

| Метод | Endpoint | Описание |
|-------|----------|----------|
| POST | `/api/auth/login` | Login и получаване на токен |

### 🔒 Аутентикация (Защитени - изискват Bearer токен)

| Метод | Endpoint | Описание |
|-------|----------|----------|
| GET | `/api/auth/user` | Получаване на данни за текущия потребител |
| POST | `/api/auth/logout` | Logout и изтриване на токен |
| POST | `/api/auth/refresh` | Обновяване на токен |
| PUT | `/api/auth/profile` | Обновяване на потребителски профил |
| PUT | `/api/auth/password` | Смяна на парола |
| GET | `/api/auth/statistics` | Статистики за потребителя |

### 🏦 Банкови сметки

| Метод | Endpoint | Описание |
|-------|----------|----------|
| GET | `/api/bank-accounts` | Списък със сметки |
| POST | `/api/bank-accounts` | Създаване на нова сметка |
| GET | `/api/bank-accounts/{id}` | Получаване на конкретна сметка |
| PUT | `/api/bank-accounts/{id}` | Обновяване на сметка |
| PATCH | `/api/bank-accounts/{id}` | Частично обновяване на сметка |
| DELETE | `/api/bank-accounts/{id}` | Изтриване на сметка |
| GET | `/api/bank-accounts/{id}/statistics` | Статистики за сметка |

### 💰 Транзакции

| Метод | Endpoint | Описание |
|-------|----------|----------|
| GET | `/api/transactions` | Списък с транзакции |
| POST | `/api/transactions` | Създаване на нова транзакция |
| GET | `/api/transactions/{id}` | Получаване на конкретна транзакция |
| PUT | `/api/transactions/{id}` | Обновяване на транзакция |
| PATCH | `/api/transactions/{id}` | Частично обновяване на транзакция |
| DELETE | `/api/transactions/{id}` | Изтриване на транзакция |

### 🔄 Трансфери

| Метод | Endpoint | Описание |
|-------|----------|----------|
| GET | `/api/transfers` | Списък с трансфери |
| POST | `/api/transfers` | Създаване на нов трансфер |
| GET | `/api/transfers/{id}` | Получаване на конкретен трансфер |
| PUT | `/api/transfers/{id}` | Обновяване на трансфер |
| PATCH | `/api/transfers/{id}` | Частично обновяване на трансфер |
| DELETE | `/api/transfers/{id}` | Изтриване на трансфер |

### 👥 Контрагенти

| Метод | Endpoint | Описание |
|-------|----------|----------|
| GET | `/api/counterparties` | Списък с контрагенти |
| POST | `/api/counterparties` | Създаване на нов контрагент |
| GET | `/api/counterparties/{id}` | Получаване на конкретен контрагент |
| PUT | `/api/counterparties/{id}` | Обновяване на контрагент |
| PATCH | `/api/counterparties/{id}` | Частично обновяване на контрагент |
| DELETE | `/api/counterparties/{id}` | Изтриване на контрагент |
| GET | `/api/counterparties/{id}/statistics` | Статистики за контрагент |
| GET | `/api/counterparties/{id}/transactions` | Транзакции за контрагент |

### 📋 Типове транзакции

| Метод | Endpoint | Описание |
|-------|----------|----------|
| GET | `/api/transaction-types` | Списък с типове транзакции |
| POST | `/api/transaction-types` | Създаване на нов тип |
| GET | `/api/transaction-types/{id}` | Получаване на конкретен тип |
| PUT | `/api/transaction-types/{id}` | Обновяване на тип |
| PATCH | `/api/transaction-types/{id}` | Частично обновяване на тип |
| DELETE | `/api/transaction-types/{id}` | Изтриване на тип |
| GET | `/api/transaction-types/{id}/statistics` | Статистики за тип |
| GET | `/api/transaction-types/{id}/transactions` | Транзакции за тип |

### 🕒 Повтарящи се плащания (Recurring Payments)

| Метод | Endpoint | Описание |
|-------|----------|----------|
| GET | `/api/recurring-payments` | Списък с повтарящи се плащания |
| POST | `/api/recurring-payments` | Създаване на повтарящо се плащане |
| GET | `/api/recurring-payments/{id}` | Детайли за повтарящо се плащане |
| PUT | `/api/recurring-payments/{id}` | Обновяване на повтарящо се плащане |
| PATCH | `/api/recurring-payments/{id}` | Частично обновяване |
| DELETE | `/api/recurring-payments/{id}` | Изтриване |

**Пример за създаване:**
```json
{
  "bank_account_id": 1,
  "counterparty_id": 1,
  "transaction_type_id": 1,
  "amount": 100.00,
  "currency": "BGN",
  "description": "Месечен наем",
  "repeat_type": "monthly",
  "repeat_interval": 1,
  "repeat_unit": "months",
  "period_start_day": 1,
  "period_end_day": 10,
  "start_date": "2025-07-01",
  "end_date": null,
  "is_active": true
}
```

### 📅 Планирани плащания (Scheduled Payments)

| Метод | Endpoint | Описание |
|-------|----------|----------|
| GET | `/api/scheduled-payments` | Списък с планирани плащания |
| POST | `/api/scheduled-payments` | Създаване на планирано плащане |
| GET | `/api/scheduled-payments/{id}` | Детайли за планирано плащане |
| PUT | `/api/scheduled-payments/{id}` | Обновяване на планирано плащане |
| PATCH | `/api/scheduled-payments/{id}` | Частично обновяване |
| DELETE | `/api/scheduled-payments/{id}` | Изтриване |

**Пример за създаване:**
```json
{
  "bank_account_id": 1,
  "counterparty_id": 1,
  "transaction_type_id": 1,
  "amount": 50.00,
  "currency": "BGN",
  "description": "Плащане на ток",
  "scheduled_date": "2025-07-10",
  "period_start_date": "2025-07-01",
  "period_end_date": "2025-07-15",
  "is_active": true
}
```

**Query параметри за списъците:**
- `is_active` — филтрира по статус (true/false)
- `per_page` — брой на страница
- `page` — номер на страница

## Query параметри за филтриране

### Общи параметри за списъци:
- `per_page` - Брой записи на страница (макс 100, по подразбиране 15)
- `page` - Номер на страницата

### За транзакции (`/api/transactions`):
- `bank_account_id` - Филтър по банкова сметка
- `type` - Филтър по тип (`income` или `expense`)
- `counterparty_id` - Филтър по контрагент
- `transaction_type_id` - Филтър по тип транзакция
- `from_date` - Филтър от дата (формат: YYYY-MM-DD)
- `to_date` - Филтър до дата (формат: YYYY-MM-DD)

### За трансфери (`/api/transfers`):
- `from_account_id` - Филтър по изходяща сметка
- `to_account_id` - Филтър по входяща сметка
- `from_date` - Филтър от дата
- `to_date` - Филтър до дата

### За контрагенти и типове транзакции:
- `search` - Търсене по име, email, телефон или описание

## Примери за използване на query параметри

```bash
# Получаване на транзакции за конкретна сметка
GET /api/transactions?bank_account_id=1

# Получаване на приходи за последния месец
GET /api/transactions?type=income&from_date=2025-05-01&to_date=2025-05-31

# Пагинация - втора страница с 25 записа
GET /api/transactions?page=2&per_page=25

# Търсене в контрагенти
GET /api/counterparties?search=company

# Комбинирани филтри
GET /api/transactions?bank_account_id=1&type=expense&counterparty_id=5&per_page=50
```

## Общ брой endpoints: 37

**Забележка:** Всички endpoints освен `POST /api/auth/login` изискват Bearer токен в Authorization header.
