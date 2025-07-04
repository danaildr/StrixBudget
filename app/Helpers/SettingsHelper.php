<?php

namespace App\Helpers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class SettingsHelper
{
    /**
     * Get a setting value with caching
     */
    public static function get($key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            return SystemSetting::get($key, $default);
        });
    }

    /**
     * Clear settings cache
     */
    public static function clearCache()
    {
        $keys = ['site_name', 'site_icon', 'favicon'];
        foreach ($keys as $key) {
            Cache::forget("setting_{$key}");
        }
    }

    /**
     * Get site icon URL
     */
    public static function getSiteIconUrl()
    {
        $icon = self::get('site_icon');
        return $icon ? asset('storage/' . $icon) : null;
    }

    /**
     * Get site name
     */
    public static function getSiteName()
    {
        return self::get('site_name', 'StrixBudget');
    }

    /**
     * Get favicon URL
     */
    public static function getFaviconUrl()
    {
        $favicon = self::get('favicon');
        return $favicon ? asset('storage/' . $favicon) : null;
    }
}
