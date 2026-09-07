<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

/**
 * A business (salon, studio, clinic…) using the platform. Everything the
 * business owns hangs off `tenant_id`; see BelongsToTenant.
 */
class Tenant extends Model
{
    use HasFactory;

    public const PLAN_FREE = 'free';

    public const PLAN_PRO = 'pro';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_SUSPENDED = 'suspended';

    public const STATUS_DELETED = 'deleted';

    protected $fillable = [
        'slug', 'name', 'category', 'country', 'city', 'address', 'phone', 'email',
        'description', 'locale', 'timezone', 'currency', 'plan', 'status', 'is_public',
        'owner_user_id', 'pro_until', 'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'pro_until' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Public listings are cached; any tenant change invalidates them. */
    protected static function booted(): void
    {
        $flush = function (): void {
            foreach (['directory:listed', 'sitemap:xml', 'site:stats'] as $key) {
                Cache::forget($key);
            }
        };
        static::saved($flush);
        static::deleted($flush);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    #[Scope]
    protected function listed(Builder $query): Builder
    {
        return $query->active()->where('is_public', true);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /** Pro access is plan-based and, once billing exists, time-boxed by `pro_until`. */
    public function isPro(): bool
    {
        if ($this->plan !== self::PLAN_PRO) {
            return false;
        }

        return $this->pro_until === null || $this->pro_until->isFuture();
    }

    /** @return array<string, mixed> */
    public function planLimits(): array
    {
        $plan = $this->isPro() ? self::PLAN_PRO : self::PLAN_FREE;

        return config("tenancy.plans.{$plan}", config('tenancy.plans.free'));
    }

    public function schemaType(): string
    {
        return config("tenancy.categories.{$this->category}", 'LocalBusiness');
    }

    public static function isReservedSlug(string $slug): bool
    {
        return in_array(strtolower($slug), config('tenancy.reserved_slugs', []), true);
    }
}
