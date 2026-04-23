<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key.
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return \Illuminate\Support\Facades\Cache::rememberForever('setting_' . $key, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value by key.
     * 
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set($key, $value)
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
        \Illuminate\Support\Facades\Cache::forget('setting_' . $key);
    }

    /**
     * Get and increment a setting value atomically.
     * 
     * @param string $key
     * @param int $default
     * @return int
     */
    public static function getAndIncrement($key, $default = 0)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($key, $default) {
            $setting = self::where('key', $key)->lockForUpdate()->first();
            
            if (!$setting) {
                $setting = self::create(['key' => $key, 'value' => (string)($default + 1)]);
                return $default + 1;
            }

            $newValue = (int)$setting->value + 1;
            $setting->update(['value' => (string)$newValue]);
            
            return $newValue;
        });
    }

    public static function getUnitPrice()
    {
        return (float) \Illuminate\Support\Facades\Cache::rememberForever('setting_unit_price', function() {
            return self::get('unit_price', config('settings.unit_price', 25.00));
        });
    }
}
