<?php namespace RainLab\User\Updates;

use MabarYuk\Master\Models\ProgressStatus;
use October\Rain\Database\Updates\Seeder;

class SeedRefProgressStatusesTable extends Seeder
{
    public function run()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::statement('TRUNCATE mabaryuk_ref_progress_statuses');
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        ProgressStatus::create([
            'id' => 1,
            'name' => 'No Response',
            'description' => 'Email sent, no response'
        ]);

        ProgressStatus::create([
            'id' => 2,
            'name' => 'User - Accept',
            'description' => 'User accepts the offering'
        ]);

        ProgressStatus::create([
            'id' => 3,
            'name' => 'User - Decline',
            'description' => 'User declines the offering'
        ]);
        
        ProgressStatus::create([
            'id' => 4,
            'name' => 'User - Link Expired',
            'description' => 'Link already expired'
        ]);

        ProgressStatus::create([
            'id' => 5,
            'name' => 'CV - Passed',
            'description' => 'CV\'s screening is passed'
        ]);

        ProgressStatus::create([
            'id' => 6,
            'name' => 'CV - Not Passed',
            'description' => 'CV\'s screening is not passed'
        ]);

        ProgressStatus::create([
            'id' => 7,
            'name' => 'Assessment Test - Passed',
            'description' => 'Assessment test is passed'
        ]);

        ProgressStatus::create([
            'id' => 8,
            'name' => 'Assessment Test - Not Passed',
            'description' => 'Assessment test is not passed'
        ]);

        ProgressStatus::create([
            'id' => 9,
            'name' => 'Interview',
            'description' => 'On interview process by company'
        ]);

        ProgressStatus::create([
            'id' => 10,
            'name' => 'Applicant - Accepted',
            'description' => 'Applicant is accepted after interview process'
        ]);

        ProgressStatus::create([
            'id' => 11,
            'name' => 'Applicant - Declined',
            'description' => 'Applicant is declined after interview process'
        ]);
    }
}
