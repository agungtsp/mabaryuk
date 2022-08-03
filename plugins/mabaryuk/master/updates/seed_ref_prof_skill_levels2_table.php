<?php namespace RainLab\User\Updates;

use DB;
use MabarYuk\Master\Models\ProfSkillLevel;
use October\Rain\Database\Updates\Seeder;

class SeedRefProfSkillLevels2Table extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('TRUNCATE mabaryuk_ref_prof_skill_levels');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        ProfSkillLevel::create([
            'id' => 1,
            'name' => 'Beginner',
            'alias' => 'Beginner (less than 1 year)'
        ]);

        ProfSkillLevel::create([
            'id' => 2,
            'name' => 'Junior',
            'alias' => 'Junior (1 - 2 years)'
        ]);

        ProfSkillLevel::create([
            'id' => 3,
            'name' => 'Mid-Level',
            'alias' => 'Mid-Level (3 - 5 years)'
        ]);

        ProfSkillLevel::create([
            'id' => 4,
            'name' => 'Expert',
            'alias' => 'Expert (more than 5 years)'
        ]);
    }
}
