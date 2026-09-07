<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Support\Tenancy;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the tenant from the first URL segment ({tenant} route parameter),
 * makes it current for the request, and makes sure a staff session from one
 * tenant cannot be used inside another tenant's admin.
 */
class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $slug = (string) $request->route('tenant');
        if ($slug === '' || Tenant::isReservedSlug($slug)) {
            abort(404);
        }

        $tenant = Tenant::query()->where('slug', $slug)->first();
        if (! $tenant) {
            abort(404);
        }
        if ($tenant->status === Tenant::STATUS_DELETED) {
            abort(404);
        }
        if (! $tenant->isActive()) {
            abort(403, 'This booking page is currently unavailable.');
        }

        Tenancy::set($tenant);
        $request->route()->setParameter('tenant', $tenant);
        $request->route()->forgetParameter('tenant');
        $request->attributes->set('tenant', $tenant);

        // Sessions are shared across the whole domain (path-based tenancy):
        // a user logged into tenant A must not appear logged in on tenant B.
        $user = Auth::user();
        if ($user && (int) $user->tenant_id !== (int) $tenant->id) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return $next($request);
    }
}
