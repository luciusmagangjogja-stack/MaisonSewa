<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SettingsService
{
    private static ?array $cache = null;

    public static function get(string $key, mixed $default = null): mixed
    {
        if (self::$cache === null) {
            self::$cache = DB::table('settings')->pluck('value', 'key')->toArray();
        }

        return array_key_exists($key, self::$cache) ? self::$cache[$key] : $default;
    }

    public static function all(): array
    {
        if (self::$cache === null) {
            self::$cache = DB::table('settings')->pluck('value', 'key')->toArray();
        }

        return self::$cache;
    }

    public static function forget(): void
    {
        self::$cache = null;
    }
}
