<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use App\Support\Tenancy;
use Illuminate\View\View;

/**
 * Public, indexable profile of a business at /{slug}: offer with prices,
 * team, contact and a call to action to the booking flow. Schema.org
 * LocalBusiness (subtype per category) with Service/Offer entries.
 */
class TenantProfileController extends Controller
{
    public function show(): View
    {
        $tenant = Tenancy::current();

        $categories = Category::query()
            ->with(['services' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get()
            ->filter(fn (Category $c) => $c->services->isNotEmpty())
            ->values();

        $workers = BusinessSetting::get('show_worker_avatars', true)
            ? User::query()->where('role', 'worker')->orderBy('name')->get(['id', 'name', 'avatar_path'])
            : collect();

        $currency = $tenant->currency ?: 'EUR';
        $price = fn (Service $s) => is_numeric($s->price)
            ? number_format((float) $s->price, (float) $s->price === floor((float) $s->price) ? 0 : 2, ',', ' ').' '.($currency === 'EUR' ? '€' : $currency)
            : (string) $s->price;

        return view('tenant.profile', [
            'tenant' => $tenant,
            'categories' => $categories,
            'workers' => $workers,
            'price' => $price,
            'currency' => $currency,
            'address' => (string) (BusinessSetting::get('business_address') ?: $tenant->address),
            'phone' => (string) (BusinessSetting::get('support_phone') ?: $tenant->phone),
            'email' => (string) (BusinessSetting::get('support_email') ?: $tenant->email),
            'openingHours' => (string) BusinessSetting::get('opening_hours_text', ''),
            'logo' => BusinessSetting::get('business_logo_path'),
            'hero' => BusinessSetting::get('hero_background_image_path'),
            'indexable' => $tenant->is_public && $tenant->isActive(),
            'schemaType' => $tenant->schemaType(),
        ]);
    }
}
