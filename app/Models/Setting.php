<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Support\Tenancy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Untyped key/value settings for the look of a tenant's public booking page
 * (theme colours).
 */
class Setting extends Model
{
    use BelongsToTenant, HasFactory;

    public const DEFAULT_ACCENT = '#C19A3E';

    public const DEFAULT_TEXT = '#211B14';

    public const DEFAULT_BACKGROUND = '#FBF7F0';

    protected $fillable = ['key', 'value'];

    /** @var array<int, array<string, self|null>> tenant id => key => row */
    private static array $cache = [];

    public static function get(string $key, mixed $default = null): mixed
    {
        $bucket = Tenancy::id() ?? 0;
        if (! array_key_exists($key, self::$cache[$bucket] ?? [])) {
            self::$cache[$bucket][$key] = static::query()->where('key', $key)->first();
        }

        $value = self::$cache[$bucket][$key]?->value;

        return ($value === null || $value === '') ? $default : $value;
    }

    public static function set(string $key, mixed $value): self
    {
        return self::$cache[Tenancy::id() ?? 0][$key] = static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function forgetCache(): void
    {
        self::$cache = [];
    }

    /** @return array{main_accent: string, text_color: string, booking_bg_color: string} */
    public static function getThemeColors(): array
    {
        return [
            'main_accent' => static::get('main_accent', self::DEFAULT_ACCENT),
            'text_color' => static::get('text_color', self::DEFAULT_TEXT),
            'booking_bg_color' => static::get('booking_bg_color', self::DEFAULT_BACKGROUND),
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn (self $setting) => self::$cache[$setting->tenant_id ?? 0][$setting->key] = $setting);
        static::deleted(fn (self $setting) => self::$cache[$setting->tenant_id ?? 0][$setting->key] = null);
    }
}
