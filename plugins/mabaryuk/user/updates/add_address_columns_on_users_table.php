<?php namespace MabarYuk\User\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * AddProfileColumnsOnUsersTable Migration
 */
class AddProfileColumnsOnUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function($table)
        {
            $table->enum('id_card', ['KTP', 'Passport'])->nullable();
            $table->string('id_number', 30)->nullable();
            $table->string('birth_place', 80)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('domicile_address')->nullable();
            $table->string('domicile_province_code', 20)->index()->nullable();
            $table->string('domicile_city_code', 20)->index()->nullable();
            $table->string('domicile_district_code', 20)->index()->nullable();
            $table->string('domicile_village_code', 20)->index()->nullable();
            $table->boolean('is_npwp')->nullable();
            $table->text('about')->nullable();
            $table->integer('ref_marital_status_id')->unsigned()->nullable();

            $table->foreign('ref_marital_status_id')->references('id')->on('mabaryuk_ref_marital_statuses');
            $table->foreign('domicile_province_code')->references('code')->on('mabaryuk_ref_locations');
            $table->foreign('domicile_city_code')->references('code')->on('mabaryuk_ref_locations');
            $table->foreign('domicile_district_code')->references('code')->on('mabaryuk_ref_locations');
            $table->foreign('domicile_village_code')->references('code')->on('mabaryuk_ref_locations');
        });
    }

    public function down()
    {
        Schema::table('users', function($table) {
            $table->dropForeign(['ref_marital_status_id']);
            $table->dropForeign(['domicile_province_code']);
            $table->dropForeign(['domicile_city_code']);
            $table->dropForeign(['domicile_district_code']);
            $table->dropForeign(['domicile_village_code']);

            $table->dropColumn([
                'card_type',
                'id_card',
                'birth_place',
                'birth_date',
                'domicile_address',
                'domicile_province_code',
                'domicile_city_code',
                'domicile_district_code',
                'domicile_village_code',
                'is_npwp',
                'about',
                'ref_marital_status_id'
            ]);
        });
    }
}
