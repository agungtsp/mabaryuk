<?php namespace MabarYuk\User\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * UpdateCvColumnsOnUsersTable Migration
 */
class UpdateCvColumnsOnUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function($table)
        {
            $table->dropColumn(['is_npwp']);

            $table->boolean('is_address_diff')->default(false)->comment('Is current address different?');
            $table->string('npwp')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function($table) {
            $table->boolean('is_npwp')->nullable();
            $table->dropColumn(['npwp', 'is_address_diff']);
        });
    }
}