<?php namespace RainLab\User\Updates;

use MabarYuk\Master\Models\JobStatus;
use October\Rain\Database\Updates\Seeder;

class SeedRefJobStatusesTable extends Seeder
{
    public function run()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::statement('TRUNCATE mabaryuk_ref_job_statuses');
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        JobStatus::create([
            'id' => 1,
            'name' => 'Publish',
            'description' => 'Publish job posting'
        ]);

        JobStatus::create([
            'id' => 2,
            'name' => 'Unpublish',
            'description' => 'Unpublish job posting'
        ]);
    }
}
