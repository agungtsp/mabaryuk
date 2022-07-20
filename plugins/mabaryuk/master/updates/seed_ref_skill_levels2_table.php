<?php namespace RainLab\User\Updates;

use MabarYuk\Master\Models\SkillLevel;
use October\Rain\Database\Updates\Seeder;

class SeedRefSkillLevels2Table extends Seeder
{
    public function run()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::statement('TRUNCATE mabaryuk_ref_skill_levels');
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        SkillLevel::create([
            'id'                    => 1,
            'name'                  => 'Fresh Graduate',
            'alias'                 => 'Fresh Graduate (0 Year Experience)',
            'talent_commission'     => 20,
            'hero_commission'       => 60,
            'mabaryuk_commission' => 20,
            'min_year_exp'          => -1,
            'max_year_exp'          => 0,
            'min_fee'               => 1000000,
        ]);

        SkillLevel::create([
            'id'                    => 2,
            'name'                  => 'Junior',
            'alias'                 => 'Junior (1-3 Years Experience)',
            'talent_commission'     => 20,
            'hero_commission'       => 60,
            'mabaryuk_commission' => 20,
            'min_year_exp'          => 1,
            'max_year_exp'          => 3,
            'min_fee'               => 2000000,
        ]);

        SkillLevel::create([
            'id'                    => 3,
            'name'                  => 'Middle',
            'alias'                 => 'Middle (3 - 5 Years Experience)',
            'talent_commission'     => 20,
            'hero_commission'       => 60,
            'mabaryuk_commission' => 20,
            'min_year_exp'          => 3,
            'max_year_exp'          => 5,
            'min_fee'               => 3000000,
        ]);

        SkillLevel::create([
            'id'                    => 4,
            'name'                  => 'Senior',
            'alias'                 => 'Senior (More than 5 years Experience)',
            'talent_commission'     => 20,
            'hero_commission'       => 60,
            'mabaryuk_commission' => 20,
            'min_year_exp'          => 5,
            'max_year_exp'          => -1,
            'min_fee'               => 5000000,
        ]);
    }
}
