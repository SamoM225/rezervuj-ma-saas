<?php

namespace Tests;

use App\Support\Tenancy;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /** The current tenant is process-wide state; never let it leak between tests. */
    protected function tearDown(): void
    {
        Tenancy::forget();

        parent::tearDown();
    }
}
