<?php namespace MabarYuk\User\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * AddCompanyIdColumnOnUsersTable Migration
 */
class AddCompanyIdColumnOnUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function($table)
        {
            $table->integer('company_id')->unsigned()->nullable();

            // $table->foreign('company_id')->references('id')->on('mabaryuk_company_companies');
        });
    }

    public function down()
    {
        Schema::table('users', function($table) {
            // $table->dropForeign(['company_id']);

            $table->dropColumn([
                'company_id'
            ]);
        });
    }
}
