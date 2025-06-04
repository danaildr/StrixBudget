<?php

namespace Database\Seeders;

use App\Models\TransactionType;
use App\Models\Counterparty;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Вземаме последно създадения потребител
        $user = User::latest()->first();

        // Типове транзакции
        $transactionTypes = [
            ['name' => 'Заплата', 'description' => 'Месечна заплата и бонуси', 'user_id' => $user->id],
            ['name' => 'Наем', 'description' => 'Разходи за наем на жилище', 'user_id' => $user->id],
            ['name' => 'Комунални услуги', 'description' => 'Ток, вода, отопление, интернет', 'user_id' => $user->id],
            ['name' => 'Хранителни стоки', 'description' => 'Разходи за храна и напитки', 'user_id' => $user->id],
            ['name' => 'Транспорт', 'description' => 'Гориво, билети, такси', 'user_id' => $user->id],
            ['name' => 'Развлечения', 'description' => 'Ресторанти, кино, концерти', 'user_id' => $user->id],
            ['name' => 'Инвестиции', 'description' => 'Дивиденти и лихви от инвестиции', 'user_id' => $user->id],
            ['name' => 'Здраве', 'description' => 'Лекарства, прегледи, застраховки', 'user_id' => $user->id],
            ['name' => 'Образование', 'description' => 'Курсове, книги, обучения', 'user_id' => $user->id],
            ['name' => 'Хонорари', 'description' => 'Приходи от freelance проекти', 'user_id' => $user->id],
        ];

        foreach ($transactionTypes as $type) {
            TransactionType::create($type);
        }

        // Контрагенти
        $counterparties = [
            // Работодатели
            ['name' => 'ТехноКорп ООД', 'description' => 'IT компания', 'user_id' => $user->id],
            ['name' => 'Иновейшън АД', 'description' => 'Софтуерна компания', 'user_id' => $user->id],
            ['name' => 'ДиджиталПро ЕООД', 'description' => 'Дигитална агенция', 'user_id' => $user->id],
            
            // Наемодатели
            ['name' => 'Имоти Инвест ООД', 'description' => 'Агенция за недвижими имоти', 'user_id' => $user->id],
            ['name' => 'Георги Петров', 'description' => 'Наемодател', 'user_id' => $user->id],
            
            // Комунални услуги
            ['name' => 'ЧЕЗ Електро България', 'description' => 'Електроразпределение', 'user_id' => $user->id],
            ['name' => 'Софийска вода АД', 'description' => 'ВиК услуги', 'user_id' => $user->id],
            ['name' => 'Топлофикация София', 'description' => 'Топлофикация', 'user_id' => $user->id],
            ['name' => 'А1 България', 'description' => 'Телекомуникации', 'user_id' => $user->id],
            ['name' => 'Виваком', 'description' => 'Интернет и телевизия', 'user_id' => $user->id],
            
            // Супермаркети
            ['name' => 'Кауфланд България', 'description' => 'Верига супермаркети', 'user_id' => $user->id],
            ['name' => 'Лидл България', 'description' => 'Верига супермаркети', 'user_id' => $user->id],
            ['name' => 'Била България', 'description' => 'Верига супермаркети', 'user_id' => $user->id],
            ['name' => 'Фантастико', 'description' => 'Верига супермаркети', 'user_id' => $user->id],
            
            // Транспорт
            ['name' => 'ОМВ България', 'description' => 'Бензиностанции', 'user_id' => $user->id],
            ['name' => 'Шел България', 'description' => 'Бензиностанции', 'user_id' => $user->id],
            ['name' => 'Център за градска мобилност', 'description' => 'Градски транспорт', 'user_id' => $user->id],
            
            // Развлечения
            ['name' => 'Синема Сити', 'description' => 'Кино верига', 'user_id' => $user->id],
            ['name' => 'НДК', 'description' => 'Културен център', 'user_id' => $user->id],
            ['name' => 'Ресторант Панорама', 'description' => 'Ресторант', 'user_id' => $user->id],
            
            // Инвестиции
            ['name' => 'Българска фондова борса', 'description' => 'Борса', 'user_id' => $user->id],
            ['name' => 'УниКредит Булбанк', 'description' => 'Банка', 'user_id' => $user->id],
            ['name' => 'ДСК Банк', 'description' => 'Банка', 'user_id' => $user->id],
            
            // Здравеопазване
            ['name' => 'Аджибадем Сити Клиник', 'description' => 'Болница', 'user_id' => $user->id],
            ['name' => 'ДЗИ', 'description' => 'Застрахователна компания', 'user_id' => $user->id],
            ['name' => 'Софарма Трейдинг', 'description' => 'Аптечна верига', 'user_id' => $user->id],
            
            // Образование
            ['name' => 'Софтуни', 'description' => 'Образователен център', 'user_id' => $user->id],
            ['name' => 'Британски съвет', 'description' => 'Езиков център', 'user_id' => $user->id],
            ['name' => 'Нов български университет', 'description' => 'Университет', 'user_id' => $user->id],
            
            // Freelance клиенти
            ['name' => 'WebDev Solutions Ltd', 'description' => 'Уеб разработка', 'user_id' => $user->id],
        ];

        foreach ($counterparties as $counterparty) {
            Counterparty::create($counterparty);
        }
    }
} 