<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    /**
     * Retrieve a setting value by key, with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $value = Cache::remember("setting.{$key}", 3600, function () use ($key) {
                return static::where('key', $key)->value('value');
            });

            return $value ?? $default;
        } catch (\Throwable) {
            return $default;
        }
    }

    /**
     * Store or update a setting value.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting.{$key}");
    }

    /**
     * Retrieve all settings as an associative key => value array.
     *
     * @return array<string, mixed>
     */
    public static function allAsMap(): array
    {
        try {
            return static::pluck('value', 'key')->toArray();
        } catch (\Throwable) {
            return [];
        }
    }
}
