<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \App\Helpers\SettingsHelper::getSiteName() }}</title>

        <!-- Favicon -->
        @php
            $faviconUrl = \App\Helpers\SettingsHelper::getFaviconUrl();
        @endphp
        @if($faviconUrl)
            <link rel="icon" href="{{ $faviconUrl }}">
            <link rel="shortcut icon" href="{{ $faviconUrl }}">
            <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/" class="flex flex-col items-center">
                    @php
                        $siteIconUrl = \App\Helpers\SettingsHelper::getSiteIconUrl();
                        $siteName = \App\Helpers\SettingsHelper::getSiteName();
                    @endphp
                    @if($siteIconUrl)
                        <img src="{{ $siteIconUrl }}" alt="{{ $siteName }}" class="w-20 h-20 object-contain rounded-lg">
                    @else
                        <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                    @endif
                    <span class="mt-2 text-lg font-semibold text-gray-700">{{ $siteName }}</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
