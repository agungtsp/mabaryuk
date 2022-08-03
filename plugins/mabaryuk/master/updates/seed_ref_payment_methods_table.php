<?php namespace RainLab\User\Updates;

use MabarYuk\Master\Models\PaymentMethod;
use October\Rain\Database\Updates\Seeder;

class SeedRefPaymentMethodsTable extends Seeder
{
    public function run()
    {
        PaymentMethod::create([
            'id'                    => 1,
            'name'                  => 'Transfer',
        ]);

        PaymentMethod::create([
            'id'                    => 2,
            'name'                  => 'Wallet',
        ]);
    }
}
