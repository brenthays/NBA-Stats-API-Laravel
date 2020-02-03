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
}
