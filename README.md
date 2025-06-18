# StrixBudget - Финансово Приложение

Уеб базирано приложение за управление на лични финанси, разработено с Laravel. Приложението позволява управление на множество банкови сметки, проследяване на транзакции и трансфери между сметки.

**🚀 Новост: Приложението вече включва пълноценен REST API за интеграция с външни приложения!**

## Основни функционалности

### 🌐 Уеб интерфейс
- Управление на множество банкови сметки
- Поддръжка на различни валути
- Проследяване на приходи и разходи
- Трансфери между сметки с автоматична конверсия на валута
- Категоризация на транзакции
- Управление на контрагенти
- Експорт на данни в различни формати (CSV, XLSX, PDF)
- Импорт на данни от файлове

### 🔌 REST API
- Пълноценен REST API за всички функционалности
- JWT аутентикация с Laravel Sanctum
- 37+ API endpoints за пълно управление на данните
- Поддръжка за mobile и desktop приложения
- Интеграция с трети страни
- Подробна API документация

## Системни изисквания

### Софтуерни изисквания
- PHP >= 8.1
- MySQL >= 8.0 или MariaDB >= 10.3
- Composer >= 2.0
- Node.js >= 16.0
- npm >= 8.0

### PHP разширения
- BCMath PHP Extension
- Ctype PHP Extension
- cURL PHP Extension
- DOM PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PCRE PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- intl PHP Extension

## Инсталация

1. Клониране на хранилището:
```bash
git clone https://github.com/danaildr/StrixBudget.git
cd StrixBudget
```

2. Инсталиране на PHP зависимости:
```bash
composer install
```

3. Инсталиране на JavaScript зависимости:
```bash
npm install
```

4. Копиране на примерния конфигурационен файл:
```bash
cp .env.example .env
```

5. Генериране на ключ на приложението:
```bash
php artisan key:generate
```

6. Конфигуриране на базата данни в `.env` файла:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

7. Изпълнение на миграциите:
```bash
php artisan migrate
```

8. Публикуване на Sanctum миграциите за API:
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

9. Компилиране на frontend ресурсите:
```bash
npm run build
```

10. Конфигуриране на планировчика (cron) за автоматични задачи:
Добавете следния ред към crontab:
```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Конфигурация след инсталация

### Сесии
Приложението използва база данни за съхранение на сесии. Уверете се, че следните настройки са конфигурирани в `.env`:
```
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
```

### API Конфигурация
За правилна работа на API, добавете следните настройки в `.env`:
```
# API настройки
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,127.0.0.1,127.0.0.1:8000
SESSION_DRIVER=database

# За production добавете вашия домейн:
# SANCTUM_STATEFUL_DOMAINS=yourdomain.com,api.yourdomain.com
```

**⚠️ Важно за production:**
- Използвайте HTTPS за всички API заявки
- Конфигурирайте правилно CORS настройките
- Добавете rate limiting за API endpoints

### Права за достъп
Уверете се, че следните директории и техните поддиректории имат правилните права за достъп:
```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chown -R www-data:www-data storage/ bootstrap/cache/
```

## Разработка

За стартиране на development сървър:
```bash
php artisan serve
```

За наблюдение на frontend промени:
```bash
npm run dev
```

## Тестване

За изпълнение на тестовете:
```bash
php artisan test
```

## 🔌 API Използване

### Бърз старт с API

1. **Стартиране на сървъра:**
```bash
php artisan serve
```

2. **Login и получаване на токен:**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "your@email.com", "password": "your_password"}'
```

3. **Използване на API с токен:**
```bash
# Получаване на банкови сметки
curl -X GET http://localhost:8000/api/bank-accounts \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"

# Създаване на нова банкова сметка
curl -X POST http://localhost:8000/api/bank-accounts \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{"name": "My Account", "currency": "EUR", "balance": 1000}'
```

### JavaScript пример

```javascript
// Login
const response = await fetch('/api/auth/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        email: 'user@example.com',
        password: 'password'
    })
});

const { data } = await response.json();
const token = data.token;

// Използване на API
const accounts = await fetch('/api/bank-accounts', {
    headers: { 'Authorization': `Bearer ${token}` }
});

const accountsData = await accounts.json();
console.log(accountsData.data); // Списък с банкови сметки
```

### Python пример

```python
import requests

# Login
login_response = requests.post('http://localhost:8000/api/auth/login',
    json={'email': 'user@example.com', 'password': 'password'})

token = login_response.json()['data']['token']

# Използване на API
headers = {'Authorization': f'Bearer {token}'}

# Получаване на банкови сметки
accounts = requests.get('http://localhost:8000/api/bank-accounts', headers=headers)
print(accounts.json())

# Създаване на транзакция
transaction_data = {
    'bank_account_id': 1,
    'type': 'income',
    'amount': 500.00,
    'currency': 'EUR',
    'description': 'Salary',
    'executed_at': '2025-06-18'
}

transaction = requests.post('http://localhost:8000/api/transactions',
    json=transaction_data, headers=headers)
print(transaction.json())
```

### Налични API endpoints

- **Аутентикация**: `/api/auth/*`
- **Банкови сметки**: `/api/bank-accounts`
- **Транзакции**: `/api/transactions`
- **Трансфери**: `/api/transfers`
- **Контрагенти**: `/api/counterparties`
- **Типове транзакции**: `/api/transaction-types`

**📖 Пълна API документация**: Вижте `API_DOCUMENTATION.md` за подробна документация на всички endpoints.

## Лицензи

Този проект е лицензиран под MIT лиценз.
