<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Season;
use Illuminate\Support\Facades\Cache;

class SeasonController extends Controller
{
    protected $filters = [
        'id' => 'ofId',
        'orderBy' => 'ofOrderBy',
        'not' => 'ofNot',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'id' => 'exists:seasons,id',
        ]);

        $cacheKey = 'allSeasons' . implode($request->all(), '&');
        $seasons = Cache::remember($cacheKey, 900, function() use ($request) {
            return $this->applyFilters($request, new Season)->get();
        });

        return $seasons;
    }
}
