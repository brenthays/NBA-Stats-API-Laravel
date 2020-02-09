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
     * Scopes a query to aggregate data for playing time
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $args
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatsMinutes($query)
    {
        return $query
        // Total play time
        ->addSelect(DB::raw('SUM(TIME_TO_SEC(p.play_length)) as total_play_time_seconds'))
        // Play time by quarter
        ->leftJoin('possessions as p_q1', function($join) {
            $join->on('p_q1.id', '=', 'p.id');
            $join->where('p_q1.period', '=', 1);
            $join->whereNull('p_q1.deleted_at');
        })
        ->addSelect(DB::raw('SUM(TIME_TO_SEC(p_q1.play_length)) as total_play_time_seconds_q1'))
        ->leftJoin('possessions as p_q2', function($join) {
            $join->on('p_q2.id', '=', 'p.id');
            $join->where('p_q2.period', '=', 2);
            $join->whereNull('p_q2.deleted_at');
        })
        ->addSelect(DB::raw('SUM(TIME_TO_SEC(p_q2.play_length)) as total_play_time_seconds_q2'))
        ->leftJoin('possessions as p_q3', function($join) {
            $join->on('p_q3.id', '=', 'p.id');
            $join->where('p_q3.period', '=', 3);
            $join->whereNull('p_q3.deleted_at');
        })
        ->addSelect(DB::raw('SUM(TIME_TO_SEC(p_q3.play_length)) as total_play_time_seconds_q3'))
        ->leftJoin('possessions as p_q4', function($join) {
            $join->on('p_q4.id', '=', 'p.id');
            $join->where('p_q4.period', '=', 4);
            $join->whereNull('p_q4.deleted_at');
        })
        ->addSelect(DB::raw('SUM(TIME_TO_SEC(p_q4.play_length)) as total_play_time_seconds_q4'))
        // Play time in overtimes
        ->leftJoin('possessions as p_ot', function($join) {
            $join->on('p_ot.id', '=', 'p.id');
            $join->where('p_ot.period', '>', 4);
            $join->whereNull('p_ot.deleted_at');
        })
        ->addSelect(DB::raw('SUM(TIME_TO_SEC(p_ot.play_length)) as total_play_time_seconds_ot'));
    }

    /**
     * Scopes a query to aggregate data for offensive vs defensive possessions
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $args
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatsOffensiveVsDefensive($query)
    {
        return $query
        // Offensive possessions by quarter
        ->leftJoin('possessions as p_offense_q1', function($join) {
            $join->on('p_offense_q1.id', '=', 'p.id');
            $join->on('p_offense_q1.team_id', '=', 'players.team_id');
            $join->where('p_offense_q1.period', '=', 1);
            $join->whereNull('p_offense_q1.deleted_at');
        })
        ->addSelect(DB::raw('SUM(p_offense_q1.points) as total_team_points_q1'))
        ->leftJoin('possessions as p_offense_q2', function($join) {
            $join->on('p_offense_q2.id', '=', 'p.id');
            $join->on('p_offense_q2.team_id', '=', 'players.team_id');
            $join->where('p_offense_q2.period', '=', 2);
            $join->whereNull('p_offense_q2.deleted_at');
        })
        ->addSelect(DB::raw('SUM(p_offense_q2.points) as total_team_points_q2'))
        ->leftJoin('possessions as p_offense_q3', function($join) {
            $join->on('p_offense_q3.id', '=', 'p.id');
            $join->on('p_offense_q3.team_id', '=', 'players.team_id');
            $join->where('p_offense_q3.period', '=', 3);
            $join->whereNull('p_offense_q3.deleted_at');
        })
        ->addSelect(DB::raw('SUM(p_offense_q3.points) as total_team_points_q3'))
        ->leftJoin('possessions as p_offense_q4', function($join) {
            $join->on('p_offense_q4.id', '=', 'p.id');
            $join->on('p_offense_q4.team_id', '=', 'players.team_id');
            $join->where('p_offense_q4.period', '=', 4);
            $join->whereNull('p_offense_q4.deleted_at');
        })
        ->addSelect(DB::raw('SUM(p_offense_q4.points) as total_team_points_q4'))
        // Offensive overtime
        ->leftJoin('possessions as p_offense_ot', function($join) {
            $join->on('p_offense_ot.id', '=', 'p.id');
            $join->on('p_offense_ot.team_id', '=', 'players.team_id');
            $join->where('p_offense_ot.period', '>', 4);
            $join->whereNull('p_offense_ot.deleted_at');
        })
        ->addSelect(DB::raw('SUM(p_offense_ot.points) as total_team_points_ot'))
        // Defensive possessions by quarter
        ->leftJoin('possessions as p_defense_q1', function($join) {
            $join->on('p_defense_q1.id', '=', 'p.id');
            $join->on('p_defense_q1.team_id', '!=', 'players.team_id');
            $join->where('p_defense_q1.period', '=', 1);
            $join->whereNull('p_defense_q1.deleted_at');
        })
        ->addSelect(DB::raw('SUM(p_defense_q1.points) as total_team_points_against_q1'))
        ->leftJoin('possessions as p_defense_q2', function($join) {
            $join->on('p_defense_q2.id', '=', 'p.id');
            $join->on('p_defense_q2.team_id', '!=', 'players.team_id');
            $join->where('p_defense_q2.period', '=', 2);
            $join->whereNull('p_defense_q2.deleted_at');
        })
        ->addSelect(DB::raw('SUM(p_defense_q2.points) as total_team_points_against_q2'))
        ->leftJoin('possessions as p_defense_q3', function($join) {
            $join->on('p_defense_q3.id', '=', 'p.id');
            $join->on('p_defense_q3.team_id', '!=', 'players.team_id');
            $join->where('p_defense_q3.period', '=', 3);
            $join->whereNull('p_defense_q3.deleted_at');
        })
        ->addSelect(DB::raw('SUM(p_defense_q3.points) as total_team_points_against_q3'))
        ->leftJoin('possessions as p_defense_q4', function($join) {
            $join->on('p_defense_q4.id', '=', 'p.id');
            $join->on('p_defense_q4.team_id', '!=', 'players.team_id');
            $join->where('p_defense_q4.period', '=', 4);
            $join->whereNull('p_defense_q4.deleted_at');
        })
        ->addSelect(DB::raw('SUM(p_defense_q4.points) as total_team_points_against_q4'))
        // Defensive overtime
        ->leftJoin('possessions as p_defense_ot', function($join) {
            $join->on('p_defense_ot.id', '=', 'p.id');
            $join->on('p_defense_ot.team_id', '!=', 'players.team_id');
            $join->where('p_defense_ot.period', '>', 4);
            $join->whereNull('p_defense_ot.deleted_at');
        })
        ->addSelect(DB::raw('SUM(p_defense_ot.points) as total_team_points_against_ot'))
        ->addSelect(DB::raw('SUM(p_offense_q1.points) + SUM(p_offense_q2.points) + SUM(p_offense_q3.points) + SUM(p_offense_q4.points) + SUM(p_offense_ot.points) as total_team_points'))
        ->addSelect(DB::raw('SUM(p_defense_q1.points) + SUM(p_defense_q2.points) + SUM(p_defense_q3.points) + SUM(p_defense_q4.points) + SUM(p_defense_ot.points) as total_team_points_against'));
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

    /**
     * Scopes a query to aggregate data for rebounding stats
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $args
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatsRebounding($query)
    {
        // Events where the player rebounded
        $query = $query->leftJoin('possessions as p_reb', function($join) {
            $join->on('p_reb.id', '=', 'p.id');
            $join->on('p_reb.player_id', '=', 'players.id');
            $join->where('p_reb.event_type', '=', 'rebound');
            $join->whereNull('p_reb.deleted_at');
        })->addSelect(DB::raw('COUNT(DISTINCT p_reb.id) as total_player_rebounds'));

        // Rebounds by quarter
        $query = $query
        ->leftJoin('possessions as p_reb_q1', function($join) {
            $join->on('p_reb_q1.id', '=', 'p_reb.id');
            $join->where('p_reb_q1.period', '=', 1);
        })->addSelect(DB::raw('COUNT(DISTINCT p_reb_q1.id) as total_player_rebounds_q1'))
        ->leftJoin('possessions as p_reb_q2', function($join) {
            $join->on('p_reb_q2.id', '=', 'p_reb.id');
            $join->where('p_reb_q2.period', '=', 2);
        })->addSelect(DB::raw('COUNT(DISTINCT p_points_q2.id) as total_player_rebounds_q2'))
        ->leftJoin('possessions as p_reb_q3', function($join) {
            $join->on('p_reb_q3.id', '=', 'p_reb.id');
            $join->where('p_reb_q3.period', '=', 3);
        })->addSelect(DB::raw('COUNT(DISTINCT p_reb_q3.id) as total_player_rebounds_q3'))
        ->leftJoin('possessions as p_reb_q4', function($join) {
            $join->on('p_reb_q4.id', '=', 'p_reb.id');
            $join->where('p_reb_q4.period', '=', 4);
        })->addSelect(DB::raw('COUNT(DISTINCT p_reb_q4.id) as total_player_rebounds_q4'));

        // Rebounds in overtime
        $query = $query->leftJoin('possessions as p_reb_ot', function($join) {
            $join->on('p_reb_ot.id', '=', 'p_reb.id');
            $join->where('p_reb_ot.period', '>', 4);
        })->addSelect(DB::raw('COUNT(DISTINCT p_reb_ot.id) as total_player_rebounds_ot'));

        return $query;
    }

    /**
     * Scopes a query to aggregate data for assist stats
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $args
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatsAssists($query)
    {
        // Events where the player assisted
        $query = $query->leftJoin('possessions as p_ast', function($join) {
            $join->on('p_ast.id', '=', 'p.id');
            $join->on('p_ast.assist_player_id', '=', 'players.id');
            $join->whereNull('p_ast.deleted_at');
        })->addSelect(DB::raw('COUNT(DISTINCT p_ast.id) as total_player_assists'));

        // Assists by quarter
        $query = $query
        ->leftJoin('possessions as p_ast_q1', function($join) {
            $join->on('p_ast_q1.id', '=', 'p_ast.id');
            $join->where('p_ast_q1.period', '=', 1);
        })->addSelect(DB::raw('COUNT(DISTINCT p_ast_q1.id) as total_player_assists_q1'))
        ->leftJoin('possessions as p_ast_q2', function($join) {
            $join->on('p_ast_q2.id', '=', 'p_ast.id');
            $join->where('p_ast_q2.period', '=', 2);
        })->addSelect(DB::raw('COUNT(DISTINCT p_ast_q2.id) as total_player_assists_q2'))
        ->leftJoin('possessions as p_ast_q3', function($join) {
            $join->on('p_ast_q3.id', '=', 'p_ast.id');
            $join->where('p_ast_q3.period', '=', 3);
        })->addSelect(DB::raw('COUNT(DISTINCT p_ast_q3.id) as total_player_assists_q3'))
        ->leftJoin('possessions as p_ast_q4', function($join) {
            $join->on('p_ast_q4.id', '=', 'p_ast.id');
            $join->where('p_ast_q4.period', '=', 4);
        })->addSelect(DB::raw('COUNT(DISTINCT p_ast_q4.id) as total_player_assists_q4'));

        // Assists in overtime
        $query = $query->leftJoin('possessions as p_ast_ot', function($join) {
            $join->on('p_ast_ot.id', '=', 'p_ast.id');
            $join->where('p_ast_ot.period', '>', 4);
        })->addSelect(DB::raw('COUNT(DISTINCT p_ast_ot.id) as total_player_assists_ot'));

        return $query;
    }
}
