<?php

namespace App;

use App\BaseModel;

class Division extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'conference_id',
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
        'id', 'name', 'conference_id', 'updated_at', 'created_at',
    ];


    // =========== MEMBER FUNCTIONS =========== //

    // =========== RELATIONSHIPS =========== //

    /**
     * Get the Conference this Division belongs to
     */
    public function conference()
    {
        return $this->belongsTo('App\Conference');
    }

    /**
     * Get the Teams for this Division
     */
    public function teams()
    {
        return $this->hasMany('App\Team');
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
        return $query->whereIn('divisions.name', $arr);
    }

    /**
     * Filter by conference
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $conferences
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfConference($query, $conferences)
    {
        $arr = explode(",", $conferences);
        return $query->whereIn('divisions.conference_id', $arr);
    }
}
