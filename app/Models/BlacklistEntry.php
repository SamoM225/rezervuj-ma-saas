<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class BlacklistEntry extends Model
{
    use BelongsToTenant, HasFactory;

    protected $table = 'blacklist_entries';

    protected $fillable = [
        'identifier_type',
        'identifier_value',
        'severity',
        'reason_category',
        'reason',
        'internal_notes',
        'expires_at',
        'is_active',
        'created_by',
        'updated_by',
        'violation_count',
        'last_violation_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_violation_at' => 'datetime',
        'is_active' => 'boolean',
        'violation_count' => 'integer',
    ];

    // Severity levels
    const SEVERITY_WARNING = 'warning';

    const SEVERITY_SOFT_BAN = 'soft_ban';

    const SEVERITY_HARD_BAN = 'hard_ban';

    // Identifier types
    const TYPE_EMAIL = 'email';

    const TYPE_PHONE = 'phone';

    // Reason categories
    const REASON_NO_SHOW = 'no_show';

    const REASON_LATE_CANCELLATION = 'late_cancellation';

    const REASON_REPEATED_CANCELLATION = 'repeated_cancellation';

    const REASON_BAD_BEHAVIOR = 'bad_behavior';

    const REASON_PAYMENT_ISSUE = 'payment_issue';

    const REASON_FRAUD = 'fraud';

    const REASON_SPAM = 'spam';

    const REASON_OTHER = 'other';

    /**
     * Get severity options with labels
     */
    public static function getSeverityOptions(): array
    {
        return [
            self::SEVERITY_WARNING => 'Len upozornenie',
            self::SEVERITY_SOFT_BAN => 'Blokovať online rezervácie',
            self::SEVERITY_HARD_BAN => 'Blokovať úplne',
        ];
    }

    /**
     * Get identifier type options
     */
    public static function getIdentifierTypeOptions(): array
    {
        return [
            self::TYPE_EMAIL => 'Email',
            self::TYPE_PHONE => 'Telefón',
        ];
    }

    /**
     * Get reason category options with labels
     */
    public static function getReasonCategoryOptions(): array
    {
        return [
            self::REASON_NO_SHOW => 'Neprišiel na rezerváciu',
            self::REASON_LATE_CANCELLATION => 'Zrušil neskoro',
            self::REASON_REPEATED_CANCELLATION => 'Opakované rušenie',
            self::REASON_BAD_BEHAVIOR => 'Nevhodné správanie',
            self::REASON_PAYMENT_ISSUE => 'Problém s platbou',
            self::REASON_FRAUD => 'Podvod',
            self::REASON_SPAM => 'Spam / Falošné rezervácie',
            self::REASON_OTHER => 'Iné',
        ];
    }

    /**
     * Get severity badge class
     */
    public function getSeverityBadgeClass(): string
    {
        return match ($this->severity) {
            self::SEVERITY_WARNING => 'badge-warning',
            self::SEVERITY_SOFT_BAN => 'badge-orange',
            self::SEVERITY_HARD_BAN => 'badge-danger',
            default => 'badge-muted',
        };
    }

    /**
     * Get severity label
     */
    public function getSeverityLabel(): string
    {
        return self::getSeverityOptions()[$this->severity] ?? $this->severity;
    }

    /**
     * Get reason category label
     */
    public function getReasonCategoryLabel(): string
    {
        return self::getReasonCategoryOptions()[$this->reason_category] ?? $this->reason_category;
    }

    /**
     * Get identifier type label
     */
    public function getIdentifierTypeLabel(): string
    {
        return self::getIdentifierTypeOptions()[$this->identifier_type] ?? $this->identifier_type;
    }

    /**
     * Check if entry is expired
     */
    public function isExpired(): bool
    {
        if (! $this->expires_at) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    /**
     * Check if entry is currently active (not expired and is_active)
     */
    public function isCurrentlyActive(): bool
    {
        return $this->is_active && ! $this->isExpired();
    }

    /**
     * Check if this entry blocks bookings
     */
    public function blocksBookings(): bool
    {
        if (! $this->isCurrentlyActive()) {
            return false;
        }

        return in_array($this->severity, [self::SEVERITY_SOFT_BAN, self::SEVERITY_HARD_BAN]);
    }

    /**
     * Check if booking can be overridden by admin
     */
    public function canBeOverridden(): bool
    {
        return $this->severity !== self::SEVERITY_HARD_BAN;
    }

    /**
     * Increment violation count
     */
    public function incrementViolation(): void
    {
        $this->increment('violation_count');
        $this->update(['last_violation_at' => now()]);
    }

    /**
     * Scope: Only active entries
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope: Only blocking entries (soft_ban or hard_ban)
     */
    public function scopeBlocking($query)
    {
        return $query->active()
            ->whereIn('severity', [self::SEVERITY_SOFT_BAN, self::SEVERITY_HARD_BAN]);
    }

    /**
     * Scope: Search by identifier
     */
    public function scopeByIdentifier($query, string $type, string $value)
    {
        return $query->where('identifier_type', $type)
            ->where('identifier_value', $value);
    }

    /**
     * Static: Check if email or phone is blacklisted
     * Returns the most severe active entry or null
     */
    public static function checkBlacklist(?string $email = null, ?string $phone = null): ?self
    {
        $entries = collect();

        if ($email) {
            $emailEntry = static::active()
                ->byIdentifier(self::TYPE_EMAIL, strtolower(trim($email)))
                ->orderByRaw("CASE severity WHEN 'hard_ban' THEN 0 WHEN 'soft_ban' THEN 1 ELSE 2 END")
                ->first();

            if ($emailEntry) {
                $entries->push($emailEntry);
            }
        }

        if ($phone) {
            // Normalize phone for comparison
            $normalizedPhone = preg_replace('/[^0-9+]/', '', $phone);
            $phoneEntry = static::active()
                ->byIdentifier(self::TYPE_PHONE, $normalizedPhone)
                ->orderByRaw("CASE severity WHEN 'hard_ban' THEN 0 WHEN 'soft_ban' THEN 1 ELSE 2 END")
                ->first();

            if ($phoneEntry) {
                $entries->push($phoneEntry);
            }
        }

        if ($entries->isEmpty()) {
            return null;
        }

        // Return the most severe entry
        $severityOrder = [self::SEVERITY_HARD_BAN => 0, self::SEVERITY_SOFT_BAN => 1, self::SEVERITY_WARNING => 2];

        return $entries->sortBy(fn ($entry) => $severityOrder[$entry->severity] ?? 99)->first();
    }

    /**
     * Static: Get all blacklist entries for email/phone
     */
    public static function getAllEntriesFor(?string $email = null, ?string $phone = null): Collection
    {
        $entries = collect();

        if ($email) {
            $emailEntries = static::active()
                ->byIdentifier(self::TYPE_EMAIL, strtolower(trim($email)))
                ->get();
            $entries = $entries->merge($emailEntries);
        }

        if ($phone) {
            $normalizedPhone = preg_replace('/[^0-9+]/', '', $phone);
            $phoneEntries = static::active()
                ->byIdentifier(self::TYPE_PHONE, $normalizedPhone)
                ->get();
            $entries = $entries->merge($phoneEntries);
        }

        return $entries;
    }

    // Relationships
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
