<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 99999),
            'name' => $name,
            'category' => 'beauty',
            'country' => 'SK',
            'city' => fake()->city(),
            'locale' => 'sk',
            'timezone' => 'Europe/Bratislava',
            'currency' => 'EUR',
            'plan' => Tenant::PLAN_FREE,
            'status' => Tenant::STATUS_ACTIVE,
            'is_public' => true,
        ];
    }

    public function pro(): static
    {
        return $this->state(fn () => ['plan' => Tenant::PLAN_PRO, 'pro_until' => now()->addMonth()]);
    }

    public function suspended(): static
    {
        return $this->state(fn () => ['status' => Tenant::STATUS_SUSPENDED]);
    }
}
