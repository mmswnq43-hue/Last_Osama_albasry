<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $primaryKey = 'key';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['key', 'value', 'label', 'type'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->first();
        if (! $setting) {
            return $default;
        }

        return match ($setting->type) {
            'integer' => (int) $setting->value,
            'float'   => (float) $setting->value,
            default   => $setting->value,
        };
    }

    public static function set(string $key, mixed $value): void
    {
        static::query()->where('key', $key)->update(['value' => $value]);
    }
}
