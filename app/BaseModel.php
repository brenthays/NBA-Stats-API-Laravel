<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{
    use SoftDeletes;

    // =========== SCOPES =========== //

    /**
     * Scope a query to only include models of given ids
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $ids
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfId($query, $ids)
    {
        $table = $this->getTable();

        $ids_array = explode(",",$ids);
        return $query->whereIn($table.'.id', $ids_array);
    }

    /**
     * Scope a query to exclude models by given ids
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $ids
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfNot($query, $ids)
    {
        $table = $this->getTable();

        $ids_array = explode(",",$ids);
        return $query->whereNotIn($table.".id", $ids_array);
    }

    /**
     * Scope a query to order model collection accordingly
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  String $orderStr (attr,dir)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfOrderBy($query, $orderStr)
    {
        if(empty($this->sortable)) return $query;

        // orderStr argument is in format (attr, dir)
        $orderArr = explode(",", $orderStr);
        if(count($orderArr) !== 2) return $query;

        $orderDir = strtolower($orderArr[1]);
        if(!in_array($orderDir, ['asc', 'desc'])) return $query;

        $orderAttr = strtolower($orderArr[0]);
        if(!in_array($orderAttr, $this->sortable)) return $query;

        $table = $this->getTable();
        return $query->orderBy($table.'.'.$orderAttr, $orderDir);
    }
}
