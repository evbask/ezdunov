<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('agree')->nullable()->change();
            $table->string('name')->nullable()->change();
            //$table->string('phone')->nullable(false)->unique()->change();
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function change()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            //$table->string('phone')->nullable()->unique()->change();
            $table->string('email')->nullable()->change();
        });
    }
}
