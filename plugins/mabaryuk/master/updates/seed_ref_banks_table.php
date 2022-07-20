<?php namespace RainLab\User\Updates;

use MabarYuk\Master\Models\Bank;
use October\Rain\Database\Updates\Seeder;

class SeedRefBanksTable extends Seeder
{
    public function run()
    {
        Bank::create([
            'id'   => 1,
            'name' => 'Mandiri',
        ]);

        Bank::create([
            'id'   => 2,
            'name' => 'BCA',
        ]);
    }
}
