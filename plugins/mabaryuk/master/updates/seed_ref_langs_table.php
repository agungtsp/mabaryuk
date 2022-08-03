<?php namespace RainLab\User\Updates;

use MabarYuk\Master\Models\Lang;
use October\Rain\Database\Updates\Seeder;

class SeedRefLangsTable extends Seeder
{
    public function run()
    {
        Lang::create([
            'id' => 1,
            'name' => 'English',
        ]);

        Lang::create([
            'id' => 2,
            'name' => 'Mandarin',
        ]);
    }
}
