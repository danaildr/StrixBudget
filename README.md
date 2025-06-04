# Финансово Приложение

Уеб базирано приложение за управление на лични финанси, разработено с Laravel. Приложението позволява управление на множество банкови сметки, проследяване на транзакции и трансфери между сметки.

## Основни функционалности

- Управление на множество банкови сметки
- Поддръжка на различни валути
- Проследяване на приходи и разходи
- Трансфери между сметки с автоматична конверсия на валута
- Категоризация на транзакции
- Управление на контрагенти
- Експорт на данни в различни формати (CSV, XLSX, PDF)
- Импорт на данни от файлове

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
git clone [repository-url]
cd [project-directory]
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

8. Компилиране на frontend ресурсите:
```bash
npm run build
```

9. Конфигуриране на планировчика (cron) за автоматични задачи:
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

## Лицензи

Този проект е лицензиран под MIT лиценз.
