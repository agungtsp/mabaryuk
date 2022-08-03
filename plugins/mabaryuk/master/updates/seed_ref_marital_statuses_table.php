<?php namespace RainLab\User\Updates;

use MabarYuk\Master\Models\MaritalStatus;
use October\Rain\Database\Updates\Seeder;

class SeedRefMaritalStatusesTable extends Seeder
{
    public function run()
    {
        MaritalStatus::create([
            'id' => 1,
            'name' => 'Single',
        ]);

        MaritalStatus::create([
            'id' => 2,
            'name' => 'Married',
        ]);

        MaritalStatus::create([
            'id' => 3,
            'name' => 'Divorced',
        ]);

        MaritalStatus::create([
            'id' => 4,
            'name' => 'Widow',
        ]);
    }
}
