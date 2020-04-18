<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Division;

class DivisionController extends Controller
{
    protected $filters = [
        'id' => 'ofId',
        'orderBy' => 'ofOrderBy',
        'not' => 'ofNot',
        'conference_id' => 'ofConference',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'id' => 'exists:divisions,id',
            'conference_id' => 'exists:conferences,id',
        ]);

        $cacheKey = 'allDivisions' . implode($request->all(), '&');
        $divisions = Cache::remember($cacheKey, 900, function() use ($request) {
            return $this->applyFilters($request, new Division)->get();
        });

        return $divisions;
    }
}
