<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreRentPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_rent_photos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pre_rent_problem_id');
            $table->foreign('pre_rent_problem_id')->references('id')->on('pre_rent_problems');
            $table->text('photo_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pre_rent_photos');
    }
}
