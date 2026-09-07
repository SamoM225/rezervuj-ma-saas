<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A tenant's PayPal subscription (the Pro plan). Access rights are derived on
 * the tenant (`plan`, `pro_until`); this row keeps provider state and history.
 */
class Subscription extends Model
{
    protected $fillable = [
        'tenant_id', 'provider', 'provider_id', 'plan_key', 'status', 'currency', 'amount',
        'current_period_end', 'cancelled_at', 'raw',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'current_period_end' => 'datetime',
            'cancelled_at' => 'datetime',
            'raw' => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['ACTIVE', 'APPROVED'], true);
    }
}
