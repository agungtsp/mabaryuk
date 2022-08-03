<?php namespace RainLab\User\Updates;

use MabarYuk\Master\Models\LangLevel;
use October\Rain\Database\Updates\Seeder;

class SeedRefLangLevelsTable extends Seeder
{
    public function run()
    {
        LangLevel::create([
            'id' => 1,
            'name' => 'Basic',
        ]);

        LangLevel::create([
            'id' => 2,
            'name' => 'Intermediate',
        ]);

        LangLevel::create([
            'id' => 3,
            'name' => "Proficient",
        ]);

        LangLevel::create([
            'id' => 4,
            'name' => "Fluent",
        ]);
    }
}
