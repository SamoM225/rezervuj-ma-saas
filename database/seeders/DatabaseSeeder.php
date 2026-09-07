<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /** Seed a sample tenant for local development (never real client data). */
    public function run(): void
    {
        $this->call(DemoTenantSeeder::class);
    }
}
