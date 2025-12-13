<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PlatformSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'is_sensitive',
    ];

    protected $casts = [
        'is_sensitive' => 'boolean',
    ];

    /**
     * Get setting value with type casting
     */
    public function getCastedValue()
    {
        return match($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->value,
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    /**
     * Static helper to get setting by key
     */
    public static function get(string $key, $default = null)
    {
        $cacheKey = "platform_setting_{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->getCastedValue() : $default;
        });
    }

    /**
     * Static helper to set setting
     */
    public static function set(string $key, $value): void
    {
        $setting = self::where('key', $key)->first();
        
        if ($setting) {
            $setting->update(['value' => $value]);
        } else {
            self::create([
                'key' => $key,
                'value' => $value,
            ]);
        }

        // Clear cache
        Cache::forget("platform_setting_{$key}");
    }

    /**
     * Get all Billplz settings
     */
    public static function getBillplzSettings(): array
    {
        return [
            'enabled' => self::get('billplz_enabled', false),
            'api_key' => self::get('billplz_api_key', ''),
            'collection_id' => self::get('billplz_collection_id', ''),
            'x_signature' => self::get('billplz_x_signature', ''),
            'sandbox_mode' => self::get('billplz_sandbox_mode', true),
        ];
    }

    /**
     * Check if Billplz is configured and enabled
     */
    public static function isBillplzReady(): bool
    {
        return self::get('billplz_enabled', false) 
            && !empty(self::get('billplz_api_key')) 
            && !empty(self::get('billplz_collection_id'));
    }
}

