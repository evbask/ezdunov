<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeletePreRentProblemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pre_rent_problems', function (Blueprint $table) {
            $table->dropColumn('rent_id');
            $table->dropColumn('user_id');
        });
        Schema::dropIfExists('pre_rent_problems');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('pre_rent_problems', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('rent_id');
            $table->foreign('rent_id')->references('id')->on('rents');
            $table->integer('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->text('message')->nullable();

            $table->timestamps();
        });
    }
}