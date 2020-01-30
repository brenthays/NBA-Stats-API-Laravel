<?php

namespace App;

use App\BaseModel;

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
}
