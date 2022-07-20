<?php namespace MabarYuk\Master\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateBankAccountsTable Migration
 */
class CreateBankAccountsTable extends Migration
{
    public function up()
    {
        Schema::create('mabaryuk_ref_bank_accounts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('ref_bank_id')->unsigned();
            $table->string('acc_name', 225);
            $table->string('acc_number', 100)->nullable();
            $table->string('description', 255)->nullable();

            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign Key
            $table->foreign('ref_bank_id')->references('id')->on('mabaryuk_ref_banks');
            $table->foreign('created_by')->references('id')->on('backend_users');
            $table->foreign('updated_by')->references('id')->on('backend_users');
            $table->foreign('deleted_by')->references('id')->on('backend_users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mabaryuk_ref_bank_accounts');
    }
}
