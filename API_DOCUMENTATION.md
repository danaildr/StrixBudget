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
