<?php

namespace App;

use App\BaseModel;

class Possession extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'game_id', 'period', 'home_team_score', 'away_team_score',
        'remaining_time', 'elapsed', 'play_length', 'description',
        'team_id', 'type', 'event_type', 'result', 'points', 'num', 'outof',
        'shot_distance', 'original_x', 'original_y', 'converted_x',
        'converted_y', 'player_id', 'away_player_id', 'home_player_id',
        'assist_player_id', 'block_player_id', 'entered_player_id',
        'left_player_id', 'opponent_player_id', 'steal_player_id',
        'possession_player_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'home_team_score' => 0,
        'away_team_score' => 0,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that are sortable.
     *
     * @var array
     */
    protected $sortable = [
        'id', 'game_id', 'period', 'home_team_score', 'away_team_score',
        'remaining_time', 'elapsed', 'play_length', 'description',
        'team_id', 'type', 'event_type', 'result', 'points', 'num', 'outof',
        'shot_distance', 'original_x', 'original_y', 'converted_x',
        'converted_y', 'player_id', 'away_player_id', 'home_player_id',
        'assist_player_id', 'block_player_id', 'entered_player_id',
        'left_player_id', 'opponent_player_id', 'steal_player_id',
        'possession_player_id', 'created_at', 'updated_at',
    ];


    // =========== MEMBER FUNCTIONS =========== //

    // =========== RELATIONSHIPS =========== //

    /**
     * Get the Game for this Possession
     */
    public function game()
    {
        return $this->belongsTo('App\Game');
    }

    /**
     * Get the Team for this Possession
     */
    public function team()
    {
        return $this->belongsTo('App\Team');
    }

    // =========== SCOPES =========== //

    /**
     * Filter by game
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $games
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfGame($query, $games)
    {
        $arr = explode(",", $games);
        return $query->whereIn('possessions.game_id', $arr);
    }

    /**
     * Filter by team
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $teams
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfTeam($query, $teams)
    {
        $arr = explode(",", $teams);
        return $query->whereIn('possessions.team_id', $arr);
    }
}
