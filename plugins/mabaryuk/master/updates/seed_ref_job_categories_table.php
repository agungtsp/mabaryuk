<?php namespace RainLab\User\Updates;

use MabarYuk\Master\Models\JobCategory;
use October\Rain\Database\Updates\Seeder;

class SeedRefCategoriesTable extends Seeder
{
    public function run()
    {
        JobCategory::create([
            'id'   => 1,
            'name' => 'Software Engineer',
        ]);

        JobCategory::create([
            'id'   => 2,
            'name' => 'Design',
        ]);

        JobCategory::create([
            'id'   => 3,
            'name' => 'Marketing',
        ]);

        JobCategory::create([
            'id'   => 4,
            'name' => 'Human Resource',
        ]);
    }
}
