<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class SetLocale
{
    protected $allowedLocales = ['en', 'bg'];

    public function handle(Request $request, Closure $next)
    {
        $locale = null;

        // Първо проверяваме за локал в сесията
        $sessionLocale = Session::get('locale');
        Log::debug('Locale from session: ' . ($sessionLocale ?? 'null'));

        // Ако има валиден локал в сесията, използваме го
        if ($sessionLocale && in_array($sessionLocale, $this->allowedLocales)) {
            $locale = $sessionLocale;
            Log::debug('Using valid locale from session: ' . $locale);
        }
        // Ако няма валиден локал в сесията и има логнат потребител, вземаме от базата
        elseif (Auth::check()) {
            $userLocale = Auth::user()->locale;
            Log::debug('Locale from database: ' . ($userLocale ?? 'null'));

            if ($userLocale && in_array($userLocale, $this->allowedLocales)) {
                $locale = $userLocale;
                Session::put('locale', $locale);
                Log::debug('Using locale from database and saved to session: ' . $locale);
            }
        }

        // Ако все още няма валиден локал, използваме конфигурацията
        if (!$locale) {
            $locale = config('app.locale', 'en');
            Log::debug('Using default locale from config: ' . $locale);

            // Проверяваме дали конфигурационният локал е валиден
            if (!in_array($locale, $this->allowedLocales)) {
                $locale = 'en'; // Fallback към английски ако конфигурацията е невалидна
                Log::debug('Config locale not allowed, using fallback: en');
            }

            // Запазваме default locale-а в базата за логнатия потребител
            if (Auth::check()) {
                $user = Auth::user();
                $user->locale = $locale;
                $user->save();
                Log::debug('Updated user locale in database to: ' . $locale);
            }
            Session::put('locale', $locale);
            Log::debug('Saved default locale to session: ' . $locale);
        }

        App::setLocale($locale);
        Log::debug('Final locale set to: ' . App::getLocale());

        return $next($request);
    }
} 