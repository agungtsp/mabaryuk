<?php namespace MabarYuk\User\Updates;

use League\Csv\Reader;
use MabarYuk\User\Models\Skill;
use October\Rain\Database\Updates\Seeder;

class SeedSkills2Table extends Seeder
{
    public function run()
    {
        $reader = Reader::createFromPath('files/user_skills_2.csv', 'r');
        $counter = 1;
        foreach ($reader as $row) {
            Skill::create([
                'name' => $row[0]
            ]);
            $counter++;
        }
    }
}
