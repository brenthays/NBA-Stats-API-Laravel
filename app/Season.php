<?php

namespace App;

use App\BaseModel;

class Season extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'start', 'end',
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
        'start' => 'date',
        'end' => 'date',
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
        'id', 'name', 'start', 'end', 'created_at', 'updated_at',
    ];


    // =========== MEMBER FUNCTIONS =========== //

    // =========== RELATIONSHIPS =========== //

    /**
     * Get the Games for this Season
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
    public function scopeOfName($query, $names)
    {
        $arr = explode(",", $names);
        return $query->whereIn('seasons.name', $arr);
    }
}
