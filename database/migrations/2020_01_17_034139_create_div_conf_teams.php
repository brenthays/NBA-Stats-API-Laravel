<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDivConfTeams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conferences', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('divisions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->unsignedInteger('conference_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('cascade');
        });

        Schema::create('teams', function (Blueprint $table) {
            $table->increments('id');
            $table->string('city')->nullable();
            $table->string('full_name')->nullable();
            $table->string('short_name')->nullable();
            $table->string('logo')->nullable();
            $table->string('nickname')->nullable();
            $table->unsignedInteger('division_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('division_id')->references('id')->on('divisions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teams');
        Schema::dropIfExists('divisions');
        Schema::dropIfExists('conferences');
    }
}
