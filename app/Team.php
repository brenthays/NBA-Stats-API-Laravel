<?php

namespace App;

use App\BaseModel;

class Team extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'short_name', 'division_id', 'city', 'full_name', 'logo',
        'nickname',
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
        'id', 'short_name', 'division_id', 'full_name', 'nickname',
        'updated_at', 'created_at',
    ];


    // =========== MEMBER FUNCTIONS =========== //

    // =========== RELATIONSHIPS =========== //

    /**
     * Get the Division this Team belongs to
     */
    public function division()
    {
        return $this->belongsTo('App\Division');
    }

    /**
     * Get the Players for this Team
     */
    public function players()
    {
        return $this->hasMany('App\Player');
    }

    /**
     * Get the Games for this Team
     */
    public function games()
    {
        return $this->hasMany('App\Game');
    }

    // =========== SCOPES =========== //

    /**
     * Filter by name
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $names
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfShortName($query, $names)
    {
        $arr = explode(",", $names);
        return $query->whereIn('teams.short_name', $arr);
    }

    /**
     * Filter by city
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $names
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfCity($query, $cities)
    {
        $arr = explode(",", $cities);
        return $query->whereIn('teams.city', $arr);
    }

    /**
     * Filter by division
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $divisions
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfDivision($query, $divisions)
    {
        $arr = explode(",", $divisions);
        return $query->whereIn('teams.division_id', $arr);
    }
}
