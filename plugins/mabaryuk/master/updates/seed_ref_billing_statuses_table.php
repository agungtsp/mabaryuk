<?php namespace RainLab\User\Updates;

use MabarYuk\Master\Models\BillingStatus;
use October\Rain\Database\Updates\Seeder;

class SeedRefBillingStatusesTable extends Seeder
{
    public function run()
    {
        BillingStatus::create([
            'id'   => 1,
            'name' => 'Waiting for Payment',
        ]);

        BillingStatus::create([
            'id'   => 2,
            'name' => 'Payment Processed',
        ]);

        BillingStatus::create([
            'id'   => 3,
            'name' => 'Paid',
        ]);
    }
}
