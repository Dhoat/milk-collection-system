<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Cache duration in seconds (24 hours).
     */
    protected const CACHE_TTL = 86400;

    /**
     * Get a setting value by key, with optional default fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $all = static::getAllFromCache();

        return $all[$key] ?? $default;
    }

    /**
     * Set a single key-value setting entry.
     */
    public static function set(string $key, mixed $value, string $group = 'system'): self
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget('app_settings_key_value_map');

        return $setting;
    }

    /**
     * Set multiple key-value settings at once.
     */
    public static function setMany(array $settings, string $group = 'system'): void
    {
        foreach ($settings as $key => $value) {
            static::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group]
            );
        }

        Cache::forget('app_settings_key_value_map');
    }

    /**
     * Get all settings as key => value array from cache.
     */
    public static function getAllFromCache(): array
    {
        return Cache::remember('app_settings_key_value_map', static::CACHE_TTL, function () {
            return static::pluck('value', 'key')->all();
        });
    }

    /**
     * Get default settings blueprint with initial system defaults.
     */
    public static function getDefaults(): array
    {
        return [
            // Business Info
            'business_name' => 'Dhoat Dairy Farm & Processing Center',
            'business_logo' => null,
            'business_phone' => '+91 98765 43210',
            'business_email' => 'contact@dhoatdairy.com',
            'business_address' => 'Main Highway, Milk Collection Hub',
            'business_city' => 'Amritsar',
            'business_state' => 'Punjab',
            'business_pincode' => '143001',
            'business_website' => 'https://dhoatdairy.com',
            'business_gst' => '03AAAAA0000A1Z5',
            'business_description' => 'Premium Quality Dairy Products & Fresh Milk Collection Center.',

            // Milk Business Configuration
            'milk_default_unit' => 'Litre',
            'milk_default_currency' => 'INR',
            'milk_currency_symbol' => '₹',
            'milk_base_fat' => '4.5',
            'milk_base_snf' => '8.5',
            'milk_default_shift' => 'morning',

            // Order & Delivery Configuration
            'order_number_prefix' => 'ORD-',
            'delivery_number_prefix' => 'DEL-',
            'default_order_status' => 'pending',
            'default_delivery_status' => 'pending',

            // System Configuration
            'system_timezone' => 'Asia/Kolkata',
            'system_date_format' => 'Y-m-d',
            'system_pagination_limit' => '15',
            'customer_ordering_enabled' => '1',
            'maintenance_mode_enabled' => '0',
        ];
    }
}
