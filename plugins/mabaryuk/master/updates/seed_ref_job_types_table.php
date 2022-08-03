<?php namespace RainLab\User\Updates;

use MabarYuk\Master\Models\JobType;
use October\Rain\Database\Updates\Seeder;

class SeedRefJobTypesTable extends Seeder
{
    public function run()
    {
        JobType::create([
            'id' => 1,
            'name' => 'Full Time',
        ]);

        JobType::create([
            'id' => 2,
            'name' => 'Part Time',
        ]);

        JobType::create([
            'id' => 3,
            'name' => 'Temporary',
        ]);

        JobType::create([
            'id' => 4,
            'name' => 'Contract',
        ]);

        JobType::create([
            'id' => 5,
            'name' => 'Internship',
        ]);
    }
}
