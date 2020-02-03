<?php

namespace App;

// use App\BaseModel;
use DB;
use Illuminate\Database\Eloquent\Builder;

class Player extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'team_id', 'picture',
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
        'id', 'team_id', 'name', 'created_at', 'updated_at',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('playersBoot', function(Builder $query){
            $query->addSelect("players.*")->groupBy("players.id");
        });
    }


    // =========== MEMBER FUNCTIONS =========== //

    // =========== RELATIONSHIPS =========== //

    /**
     * Get the Team for this Player
     */
    public function team()
    {
        return $this->belongsTo('App\Team');
    }

    /**
     * Get the Possessions for this Player
     */
    public function possessions()
    {
        return $this->belongsToMany('App\Possession');
    }

    // =========== SCOPES =========== //

    /**
     * Filter by name
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $names
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfName($query, $names)
    {
        $arr = explode(",", $names);
        return $query->whereIn('players.name', $arr);
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
        return $query->whereIn('players.team_id', $arr);
    }

    /**
     * Joins the player query to games / possessions needed to calculate stats
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $args
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatsBase($query, $args)
    {
        return $query
        // Events where the player is on the court
        ->join('player_possession as pp', function($join) {
            $join->on('pp.player_id', '=', 'players.id');
        })
        // Games filtered by id and/or season
        ->join('games as g', function($join) use ($args) {
            if(!empty($args['game_id'])) $join->where('g.id', '=', $args['game_id']);
            if(!empty($args['season_id'])) $join->where('g.season_id', '=', $args['season_id']);
            $join->whereNull('g.deleted_at');
        })
        ->addSelect(DB::raw('COUNT(DISTINCT g.id) as total_games'))
        // Events for the game and player
        ->join('possessions as p', function($join) {
            $join->on('p.game_id', '=', 'g.id');
            $join->on('p.id', '=', 'pp.possession_id');
            $join->whereNull('p.deleted_at');
        });
    }

    /**
     * Scopes a query to aggregate data for scoring stats
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $args
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatsScoring($query)
    {
        // Events where the player scored
        $query = $query->leftJoin('possessions as p_points', function($join) {
            $join->on('p_points.id', '=', 'p.id');
            $join->on('p_points.player_id', '=', 'players.id');
            $join->where('p_points.points', '>', 0);
            $join->whereNull('p_points.deleted_at');
        })->addSelect(DB::raw('SUM(p_points.points) as total_player_points'));

        // Scoring by quarter
        $query = $query
        ->leftJoin('possessions as p_points_q1', function($join) {
            $join->on('p_points_q1.id', '=', 'p_points.id');
            $join->where('p_points_q1.period', '=', 1);
        })->addSelect(DB::raw('SUM(p_points_q1.points) as total_player_points_q1'))
        ->leftJoin('possessions as p_points_q2', function($join) {
            $join->on('p_points_q2.id', '=', 'p_points.id');
            $join->where('p_points_q2.period', '=', 2);
        })->addSelect(DB::raw('SUM(p_points_q2.points) as total_player_points_q2'))
        ->leftJoin('possessions as p_points_q3', function($join) {
            $join->on('p_points_q3.id', '=', 'p_points.id');
            $join->where('p_points_q3.period', '=', 3);
        })->addSelect(DB::raw('SUM(p_points_q3.points) as total_player_points_q3'))
        ->leftJoin('possessions as p_points_q4', function($join) {
            $join->on('p_points_q4.id', '=', 'p_points.id');
            $join->where('p_points_q4.period', '=', 4);
        })->addSelect(DB::raw('SUM(p_points_q4.points) as total_player_points_q4'));

        // Scoring in overtime
        $query = $query->leftJoin('possessions as p_points_ot', function($join) {
            $join->on('p_points_ot.id', '=', 'p_points.id');
            $join->where('p_points_ot.period', '>', 4);
        })->addSelect(DB::raw('SUM(p_points_ot.points) as total_player_points_ot'));

        return $query;
    }
}
