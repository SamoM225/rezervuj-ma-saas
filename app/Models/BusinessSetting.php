<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Support\Tenancy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Typed key/value settings of a tenant's business (booking rules, contact
 * data, feature switches). Reads are cached for the lifetime of the request
 * per tenant because a single page can ask for the same key dozens of times.
 */
class BusinessSetting extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = ['key', 'value', 'type', 'description'];

    /** @var array<int, array<string, self|null>> tenant id => key => row */
    private static array $cache = [];

    public static function get(string $key, mixed $default = null): mixed
    {
        $bucket = Tenancy::id() ?? 0;
        if (! array_key_exists($key, self::$cache[$bucket] ?? [])) {
            self::$cache[$bucket][$key] = static::query()->where('key', $key)->first();
        }

        $setting = self::$cache[$bucket][$key];

        return $setting ? static::castValue($setting->value, $setting->type) : $default;
    }

    public static function set(string $key, mixed $value, string $type = 'string', ?string $description = null): self
    {
        if ($type === 'json' && is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        if (is_bool($value)) {
            $value = $value ? '1' : '0';
        }

        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value ?? '', 'type' => $type, 'description' => $description]
        );

        return self::$cache[Tenancy::id() ?? 0][$key] = $setting;
    }

    public static function forgetCache(): void
    {
        self::$cache = [];
    }

    protected static function castValue(mixed $value, ?string $type): mixed
    {
        return match ($type) {
            'integer' => (int) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'float', 'decimal' => (float) $value,
            'json' => is_string($value) ? json_decode($value, true) : $value,
            default => $value,
        };
    }

    /** How many days ahead a customer may book. */
    public static function getMaxBookingAdvanceDays(): int
    {
        return max(1, (int) static::get('booking_advance_days', 60));
    }

    protected static function booted(): void
    {
        static::saved(fn (self $setting) => self::$cache[$setting->tenant_id ?? 0][$setting->key] = $setting);
        static::deleted(fn (self $setting) => self::$cache[$setting->tenant_id ?? 0][$setting->key] = null);
    }
}
