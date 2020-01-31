<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePossessions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('possessions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('possession_num')->default(0);
            $table->unsignedInteger('game_id');
            $table->integer('period');
            $table->integer('home_team_score')->default(0);
            $table->integer('away_team_score')->default(0);
            $table->time('remaining_time');
            $table->time('elapsed');
            $table->time('play_length');
            $table->string('description')->nullable();

            $table->unsignedInteger('team_id')->nullable();

            $table->string('type')->nullable()->index();
            $table->string('event_type')->nullable()->index();
            $table->string('result')->nullable()->index();
            $table->integer('points')->nullable();
            $table->integer('num')->nullable();
            $table->integer('outof')->nullable();

            $table->float('shot_distance', 11, 2)->nullable();
            $table->float('original_x', 11, 2)->nullable();
            $table->float('original_y', 11, 2)->nullable();
            $table->float('converted_x', 11, 2)->nullable();
            $table->float('converted_y', 11, 2)->nullable();

            $table->unsignedInteger('player_id')->nullable();
            $table->unsignedInteger('away_player_id')->nullable();
            $table->unsignedInteger('home_player_id')->nullable();
            $table->unsignedInteger('assist_player_id')->nullable();
            $table->unsignedInteger('block_player_id')->nullable();
            $table->unsignedInteger('entered_player_id')->nullable();
            $table->unsignedInteger('left_player_id')->nullable();
            $table->unsignedInteger('opponent_player_id')->nullable();
            $table->unsignedInteger('steal_player_id')->nullable();
            $table->unsignedInteger('possession_player_id')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('away_player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('home_player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('assist_player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('block_player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('entered_player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('left_player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('opponent_player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('steal_player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('possession_player_id')->references('id')->on('players')->onDelete('cascade');
        });

        Schema::create('player_possession', function (Blueprint $table) {
            $table->unsignedInteger('player_id');
            $table->unsignedInteger('possession_id');
            $table->unsignedInteger('team_id')->nullable();

            $table->foreign('possession_id')->references('id')->on('possessions')->onDelete('cascade');
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->primary(['possession_id', 'player_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('player_possession');
        Schema::dropIfExists('possessions');
    }
}
