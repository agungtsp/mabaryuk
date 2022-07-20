<?php namespace RainLab\User\Updates;

use MabarYuk\Master\Models\ProfSkillLevel;
use October\Rain\Database\Updates\Seeder;

class SeedRefProfSkillLevelsTable extends Seeder
{
    public function run()
    {
        ProfSkillLevel::create([
            'id' => 1,
            'name' => 'Beginner',
        ]);

        ProfSkillLevel::create([
            'id' => 2,
            'name' => 'Average',
        ]);

        ProfSkillLevel::create([
            'id' => 3,
            'name' => 'Skilled',
        ]);

        ProfSkillLevel::create([
            'id' => 4,
            'name' => 'Specialist',
        ]);

        ProfSkillLevel::create([
            'id' => 5,
            'name' => 'Expert',
        ]);
    }
}
