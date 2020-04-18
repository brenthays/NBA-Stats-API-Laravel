<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Applies appropriate scope methods to a query based on request parameters
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applyFilters(Request $request, $query)
    {
       if(!empty($this->filters))
       {
           $input = $request->all();
           foreach($input as $key => $value)
           {
               if(!empty($this->filters[$key]))
               {
                   $thisFilter = $this->filters[$key];
                   $query = $query->{$thisFilter}($value);
               }
           }
       }
       return $query;
    }

    /**
     * Creates a cache key based on the request parameters
     *
     * @param  string $prefix
     * @param  \Illuminate\Http\Request $request
     * @return string
     */
    public function getCacheKey($prefix, Request $request)
    {
        $input = $request->all();
        ksort($input); // sort the array by key
        return $prefix . json_encode($input);
    }
}
