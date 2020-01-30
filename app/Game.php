<?php

namespace App;

use App\BaseModel;

class Game extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'season_id', 'game_date', 'away_team_id', 'home_team_id',
        'away_team_score', 'home_team_score',
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
        'away_team_score' => 0,
        'home_team_score' => 0,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'game_date' => 'date',
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
        'id', 'season_id', 'game_date', 'away_team_id', 'home_team_id',
        'away_team_score', 'home_team_score', 'created_at', 'updated_at',
    ];


    // =========== MEMBER FUNCTIONS =========== //

    // =========== RELATIONSHIPS =========== //

    /**
     * Get the Possessions for this Game
     */
    public function possessions()
    {
        return $this->hasMany('App\Possession');
    }

    // =========== SCOPES =========== //

    /**
     * Filter by season
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $seasons
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfSeason($query, $seasons)
    {
        $arr = explode(",", $seasons);
        return $query->whereIn('games.season_id', $arr);
    }

    /**
     * Filter by date
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $dates
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfDate($query, $dates)
    {
        $arr = explode(",", $dates);
        return $query->whereIn('games.game_date', $arr);
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
        return $query->whereIn('games.away_team_id', $arr)
            ->orWhere('games.home_team_id', $arr);
    }
}
