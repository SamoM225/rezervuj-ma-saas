<?php

namespace Database\Seeders;

use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\City;
use App\Models\Service;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WorkerAvailability;
use App\Support\Tenancy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * A sample tenant ("demo") for local development and showcasing the public
 * booking page. Idempotent: re-running updates instead of duplicating.
 */
class DemoTenantSeeder extends Seeder
{
    public const SLUG = 'demo';

    public const ADMIN_EMAIL = 'admin@example.test';

    public const PASSWORD = 'ChangeMeBeforeProduction!';

    /** The well-known demo password is for local development only. */
    public static function password(): string
    {
        return app()->isProduction() ? Str::random(40) : self::PASSWORD;
    }

    public function run(): void
    {
        $tenant = Tenant::query()->updateOrCreate(['slug' => self::SLUG], [
            'name' => 'Demo Beauty Studio',
            'category' => 'beauty',
            'country' => 'SK',
            'city' => 'Bratislava',
            'address' => 'Hlavná 1, 811 01 Bratislava',
            'phone' => '+421 900 000 000',
            'email' => 'demo@example.test',
            'description' => 'Ukážková prevádzka platformy rezervuj-ma.online.',
            'locale' => 'sk',
            'plan' => Tenant::PLAN_FREE,
            'status' => Tenant::STATUS_ACTIVE,
            'is_public' => true,
        ]);

        Tenancy::runAs($tenant, function () use ($tenant) {
            $admin = User::query()->updateOrCreate(['email' => self::ADMIN_EMAIL], [
                'name' => 'Demo Admin',
                'role' => 'superadmin',
                'password' => Hash::make(self::password()),
                'calendar_color' => '#C19A3E',
            ]);
            $tenant->forceFill(['owner_user_id' => $admin->id])->save();

            $city = City::query()->updateOrCreate(['name' => 'Bratislava'], ['address' => 'Hlavná 1, 811 01 Bratislava']);

            $catalog = [
                'Kozmetika' => [['Čistenie pleti', 60, 45], ['Hydratačné ošetrenie', 45, 39]],
                'Nechty' => [['Gélové nechty', 90, 35], ['Manikúra', 45, 20]],
                'Masáže' => [['Klasická masáž 60 min', 60, 40], ['Relaxačná masáž 30 min', 30, 25]],
            ];

            $worker = User::query()->updateOrCreate(['email' => 'worker@example.test'], [
                'name' => 'Demo Pracovník',
                'role' => 'worker',
                'password' => Hash::make(self::password()),
                'city_id' => $city->id,
                'calendar_color' => '#3B82F6',
            ]);

            foreach ($catalog as $categoryName => $services) {
                $category = Category::query()->updateOrCreate(['name' => $categoryName], ['city' => '']);
                $category->cities()->syncWithoutDetaching([$city->id]);
                $worker->categories()->syncWithoutDetaching([$category->id]);

                foreach ($services as [$name, $duration, $price]) {
                    $service = Service::query()->updateOrCreate(
                        ['category_id' => $category->id, 'name' => $name],
                        ['duration' => $duration, 'break_time' => 0, 'price' => (string) $price, 'city' => '']
                    );
                    $worker->services()->syncWithoutDetaching([$service->id]);
                }
            }

            WorkerAvailability::query()->updateOrCreate(['user_id' => $worker->id, 'start_date' => now()->startOfYear()->toDateString()], [
                'end_date' => now()->endOfYear()->toDateString(),
                'days_of_week' => [1, 2, 3, 4, 5],
                'start_time' => '09:00',
                'end_time' => '17:00',
                'is_active' => true,
            ]);

            foreach ([
                ['business_name', $tenant->name, 'string'],
                ['support_email', $tenant->email, 'string'],
                ['support_phone', $tenant->phone, 'string'],
                ['business_address', $tenant->address, 'string'],
                ['allow_online_booking', true, 'boolean'],
                ['send_email_notifications', true, 'boolean'],
                ['default_language', 'sk', 'string'],
                ['available_languages', ['sk', 'cs', 'en'], 'json'],
            ] as [$key, $value, $type]) {
                BusinessSetting::set($key, $value, $type);
            }
        });
    }
}
