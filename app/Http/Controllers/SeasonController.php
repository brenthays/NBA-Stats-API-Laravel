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

        $cacheKey = $this->getCacheKey('allSeasons', $request);
        $seasons = Cache::remember($cacheKey, now()->addMinutes(15), function() use ($request) {
            return $this->applyFilters($request, new Season)->get();
        });

        return $seasons;
    }
}
