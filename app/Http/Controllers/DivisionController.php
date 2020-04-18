<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Division;
use Illuminate\Support\Facades\Cache;

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

        $cacheKey = $this->getCacheKey('allDivisions', $request);
        $divisions = Cache::remember($cacheKey, now()->addMinutes(15), function() use ($request) {
            return $this->applyFilters($request, new Division)->get();
        });

        return $divisions;
    }
}
