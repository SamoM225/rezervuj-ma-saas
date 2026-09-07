<?php

namespace App\Models\Concerns;

use App\Models\Tenant;
use App\Support\Tenancy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Scopes every query to the current tenant and stamps `tenant_id` on create.
 *
 * Without a current tenant queries are unscoped on purpose (platform code,
 * tests that set up several tenants). Web requests always have one because
 * every tenant route runs through the ResolveTenant middleware.
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if ($tenantId = Tenancy::id()) {
                $builder->where($builder->qualifyColumn('tenant_id'), $tenantId);
            }
        });

        static::creating(function (Model $model) {
            if (empty($model->getAttribute('tenant_id')) && ($tenantId = Tenancy::id())) {
                $model->setAttribute('tenant_id', $tenantId);
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /** Query builder without the tenant scope (platform-level code only). */
    public static function acrossTenants(): Builder
    {
        return static::withoutGlobalScope('tenant');
    }
}
