<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNullablePassportFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pasport_verify_requests', function(Blueprint $table){
            $table->integer('date_of_birth')->nullable()->change();
            $table->string('passport_number')->nullable()->change();
            $table->string('user_fio')->nullable()->change();
            $table->string('comment_to_user')->nullable()->change();
            $table->string('comment_to_manager')->nullable()->change();
        });
        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
