<?php namespace MabarYuk\User\Updates;

use RainLab\User\Models\UserGroup;
use October\Rain\Database\Updates\Seeder;

class SeedUserGroupsTable extends Seeder
{
    public function run()
    {
        UserGroup::updateOrCreate(
            [ 'id' => 1 ],
            [
                'name' => 'Hero',
                'code' => 'hero',
                'description' => 'Default group for hero users.'
            ]
        );

        UserGroup::updateOrCreate(
            [ 'id' => 2 ],
            [
                'name' => 'Admin - Company',
                'code' => 'admin_company',
                'description' => 'Default group for admin users of company.'
            ]
        );
    }
}
