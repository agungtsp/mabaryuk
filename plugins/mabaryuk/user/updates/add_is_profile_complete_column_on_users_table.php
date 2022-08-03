<?php namespace MabarYuk\User\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * AddIsProfileCompleteColumnOnUsersTable Migration
 */
class AddIsProfileCompleteColumnOnUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function($table)
        {
            $table->boolean('is_profile_complete')->default(false);
        });
    }

    public function down()
    {
        Schema::table('users', function($table) {
            $table->dropColumn([
                'is_profile_complete'
            ]);
        });
    }
}