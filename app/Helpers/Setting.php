<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class Setting
{
    public static function get(string $key, $default = null)
    {
        $path = storage_path('app/settings.json');
        if (!file_exists($path)) {
            return $default;
        }

        $data = json_decode(file_get_contents($path), true);
        return $data[$key] ?? $default;
    }

    public static function set(string $key, $value)
    {
        $path = storage_path('app/settings.json');
        $data = [];
        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true) ?: [];
        }
        
        $data[$key] = $value;
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
