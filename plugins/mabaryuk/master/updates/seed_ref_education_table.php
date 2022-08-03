<?php namespace RainLab\User\Updates;

use MabarYuk\Master\Models\Education;
use October\Rain\Database\Updates\Seeder;

class SeedRefEducationTable extends Seeder
{
    public function run()
    {
        Education::create([
            'id' => 1,
            'name' => 'Senior High School',
            'alias' => 'SMA/SMK',
        ]);

        Education::create([
            'id' => 2,
            'name' => 'Diploma / Associate Degree',
            'alias' => 'D3',
        ]);

        Education::create([
            'id' => 3,
            'name' => "Bachelor's Degree",
            'alias' => 'S1',
        ]);

        Education::create([
            'id' => 4,
            'name' => "Master's Degree",
            'alias' => 'S2',
        ]);
    }
}
