<?php namespace RainLab\User\Updates;

use MabarYuk\Master\Models\SkillLevel;
use October\Rain\Database\Updates\Seeder;

class SeedRefSkillLevelsTable extends Seeder
{
    public function run()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::statement('TRUNCATE mabaryuk_ref_skill_levels');
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        SkillLevel::create([
            'id'                    => 1,
            'name'                  => 'Junior',
            'talent_commission'     => 20,
            'hero_commission'       => 60,
            'mabaryuk_commission' => 20
        ]);

        SkillLevel::create([
            'id'                    => 2,
            'name'                  => 'Middle',
            'talent_commission'     => 20,
            'hero_commission'       => 60,
            'mabaryuk_commission' => 20
        ]);

        SkillLevel::create([
            'id'                    => 3,
            'name'                  => 'Senior',
            'talent_commission'     => 20,
            'hero_commission'       => 60,
            'mabaryuk_commission' => 20
        ]);
    }
}
