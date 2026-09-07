<?php

namespace App\Console\Commands;

use App\Services\PayPal\PayPalClient;
use Illuminate\Console\Command;

/**
 * One-time: creates the Pro product and its four billing plans in the
 * configured PayPal environment and prints the .env lines to store.
 */
class PayPalSetupPlans extends Command
{
    protected $signature = 'paypal:setup-plans';

    protected $description = 'Create the PayPal product and subscription plans (EUR/USD, monthly/yearly)';

    public function handle(PayPalClient $paypal): int
    {
        if (! $paypal->isConfigured()) {
            $this->error('PAYPAL_CLIENT_ID / PAYPAL_SECRET are not set.');

            return self::FAILURE;
        }

        $product = $paypal->createProduct((string) config('paypal.product_name'));
        $this->info("Product: {$product['id']} ({$product['name']}) in ".config('paypal.mode'));

        $lines = [];
        foreach (config('paypal.plans') as $key => $plan) {
            $name = config('paypal.product_name').' — '.strtoupper($plan['currency']).' '.($plan['interval'] === 'YEAR' ? 'yearly' : 'monthly');
            $created = $paypal->createPlan($product['id'], $name, $plan['currency'], $plan['amount'], $plan['interval']);
            $env = 'PAYPAL_PLAN_'.strtoupper($key);
            $lines[] = "{$env}={$created['id']}";
            $this->line("  {$key}: {$created['id']}");
        }

        $this->newLine();
        $this->info('Add to .env:');
        foreach ($lines as $line) {
            $this->line($line);
        }

        return self::SUCCESS;
    }
}
